<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\UrunVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private function calculateTotals($cartItems): array
    {
        $subtotal       = collect($cartItems)->sum(fn($i) => ($i->urunVariant->discount_price ?? $i->urunVariant->price) * $i->quantity);
        $shippingCost   = 0;
        $discountAmount = session('discount_amount', 0);
        $taxAmount      = ($subtotal - $discountAmount) * 0.18;
        $total          = $subtotal - $discountAmount + $taxAmount;

        return compact('subtotal', 'shippingCost', 'discountAmount', 'taxAmount', 'total');
    }

    public function index()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        foreach ($cartItems as $item) {
            $variant        = $item->urunVariant;
            $availableStock = $variant->stock - $variant->stock_reserved;
            if ($availableStock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "Insufficient stock for {$variant->urun->name}");
            }
        }

        $totals    = $this->calculateTotals($cartItems);
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('PaymentInformation', array_merge(
            compact('cartItems', 'addresses'),
            $totals
        ));
    }

    public function process(Request $request)
    {
        try {
            $cartItems = $this->getCartItems();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Your cart is empty!');
            }

            foreach ($cartItems as $item) {
                $variant        = UrunVariant::find($item->urunVariant->id);
                $availableStock = $variant->stock - $variant->stock_reserved;
                if ($availableStock < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$variant->urun->name}");
                }
            }

            // ── Kapıda ödeme ──────────────────────────────
            if ($request->payment_method === 'cash_on_delivery') {
                DB::beginTransaction();
                $order = $this->createOrder($request, $cartItems);
                $this->createOrderItems($order, $cartItems);
                $this->createPayment($order, $request);
                if ($request->boolean('save_address') && !$request->shipping_address_id) {
                    $this->saveAddress($request);
                }
                CartItem::where('user_id', Auth::id())->delete();
                session()->forget(['discount_amount', 'discount_code']);
                DB::commit();

                return redirect()->route('myorders')->with('success', 'Your order has been placed successfully!');
            }

            // ── Kredi kartı → Stripe ──────────────────────
            if ($request->payment_method === 'credit_card') {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                $totals = $this->calculateTotals($cartItems);

                // Stripe'a gitmeden önce stoku rezerve et
                foreach ($cartItems as $item) {
                    $reserved = UrunVariant::where('id', $item->urunVariant->id)
                        ->whereRaw('(stock - stock_reserved) >= ?', [$item->quantity])
                        ->increment('stock_reserved', $item->quantity);

                    if ($reserved === 0) {
                        $this->releaseReservations($cartItems, $item->urunVariant->id);
                        throw new \Exception("Insufficient stock for {$item->urunVariant->urun->name}. Please try again.");
                    }
                }

                $cartSnapshot = $cartItems->map(fn($i) => [
                    'variant_id' => $i->urunVariant->id,
                    'quantity'   => $i->quantity,
                ])->toArray();

                $sessionKey = 'checkout_' . Auth::id() . '_' . Str::random(8);

                session([
                    $sessionKey => [
                        'form'     => $request->all(),
                        'snapshot' => $cartSnapshot,
                    ]
                ]);

                $lineItems = [];
                foreach ($cartItems as $item) {
                    $variant     = $item->urunVariant;
                    $price       = $variant->discount_price ?? $variant->price;
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'try',
                            'product_data' => [
                                'name' => $variant->urun->name . ' (' . $variant->color . ($variant->size ? ' / ' . $variant->size : '') . ')',
                            ],
                            'unit_amount'  => round($price * 100),
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'try',
                        'product_data' => ['name' => 'VAT (18%)'],
                        'unit_amount'  => round($totals['taxAmount'] * 100),
                    ],
                    'quantity' => 1,
                ];

                $stripeSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => $lineItems,
                    'mode'                 => 'payment',
                    'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&ck=' . $sessionKey,
                    'cancel_url'           => route('checkout.cancel') . '?ck=' . $sessionKey,
                    'customer_email'       => Auth::user()->email,
                    'metadata'             => [
                        'user_id'      => Auth::id(),
                        'session_key'  => $sessionKey,
                        // ✅ Snapshot metadata'ya gömüldü — webhook'ta session'a gerek yok
                        'cart_snapshot' => json_encode($cartSnapshot),
                    ],
                    'expires_at' => time() + (30 * 60),
                ]);

                return redirect($stripeSession->url);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function success(Request $request)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $stripeSession = \Stripe\Checkout\Session::retrieve($request->session_id);

            if ($stripeSession->payment_status !== 'paid') {
                return redirect()->route('checkout.index')->with('error', 'Payment could not be verified.');
            }

            $existingPayment = Payment::where('transaction_id', $stripeSession->payment_intent)->first();
            if ($existingPayment) {
                return redirect()->route('myorders')->with('success', 'Your payment was successful!');
            }

            $sessionKey   = $request->ck;
            $checkoutData = session($sessionKey);

            if (!$checkoutData) {
                return redirect()->route('myorders')->with('info', 'Your order is being processed.');
            }

            $fakeRequest  = new Request($checkoutData['form']);
            $cartSnapshot = $checkoutData['snapshot'];

            $snapshotItems = collect($cartSnapshot)->map(function ($snap) {
                $variant          = UrunVariant::with('urun')->findOrFail($snap['variant_id']);
                $obj              = new \stdClass();
                $obj->urunVariant = $variant;
                $obj->quantity    = $snap['quantity'];
                return $obj;
            });

            DB::beginTransaction();

            $order = $this->createOrder($fakeRequest, $snapshotItems);
            $this->createOrderItemsFromReservation($order, $snapshotItems);

            // ✅ metadata'dan user_id al — session'dan Auth::id() yerine güvenli
            $userId = $stripeSession->metadata->user_id ?? Auth::id();

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'credit_card',
                'transaction_id' => $stripeSession->payment_intent,
                'amount'         => $order->total,
                'currency'       => 'TRY',
                'status'         => 'success',
                'response_data'  => json_encode($stripeSession->toArray()),
                'completed_at'   => now(),
            ]);

            if ($fakeRequest->boolean('save_address') && !$fakeRequest->shipping_address_id) {
                $this->saveAddress($fakeRequest);
            }

            CartItem::where('user_id', $userId)->delete();
            session()->forget([$sessionKey, 'discount_amount', 'discount_code']);

            DB::commit();

            return redirect()->route('myorders')->with('success', 'Your payment was successful!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        $sessionKey   = $request->ck;
        $checkoutData = session($sessionKey);

        if ($checkoutData && isset($checkoutData['snapshot'])) {
            foreach ($checkoutData['snapshot'] as $snap) {
                UrunVariant::where('id', $snap['variant_id'])
                    ->where('stock_reserved', '>=', $snap['quantity'])
                    ->decrement('stock_reserved', $snap['quantity']);
            }
            session()->forget($sessionKey);
        }

        return redirect()->route('checkout.index')->with('error', 'Payment cancelled. Please try again.');
    }

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook imza hatası: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // Ödeme tamamlandı
        if ($event->type === 'checkout.session.completed') {
            $stripeSession = $event->data->object;

            if (Payment::where('transaction_id', $stripeSession->payment_intent)->exists()) {
                return response('Already processed', 200);
            }

            $userId = $stripeSession->metadata->user_id ?? null;

            if (!$userId) {
                Log::error('Stripe webhook: user_id metadata eksik');
                return response('Missing metadata', 400);
            }

            try {
                DB::beginTransaction();

                $order = Order::where('user_id', $userId)
                    ->whereDoesntHave('payment', fn($q) => $q->where('status', 'success'))
                    ->latest()
                    ->first();

                if ($order) {
                    Payment::updateOrCreate(
                        ['transaction_id' => $stripeSession->payment_intent],
                        [
                            'order_id'       => $order->id,
                            'payment_method' => 'credit_card',
                            'amount'         => $order->total,
                            'currency'       => 'TRY',
                            'status'         => 'success',
                            'response_data'  => json_encode((array) $stripeSession),
                            'completed_at'   => now(),
                        ]
                    );

                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at'        => now(),
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Stripe webhook order update hatası: ' . $e->getMessage());
                return response('Error', 500);
            }
        }

        // ✅ Stripe session süresi doldu → metadata'daki snapshot ile rezervasyonu serbest bırak
        if ($event->type === 'checkout.session.expired') {
            $stripeSession = $event->data->object;
            $sessionKey    = $stripeSession->metadata->session_key ?? null;
            $snapshotJson  = $stripeSession->metadata->cart_snapshot ?? null;

            Log::info('Stripe session expired, rezervasyon temizleniyor', ['session_key' => $sessionKey]);

            if ($snapshotJson) {
                $snapshot = json_decode($snapshotJson, true);

                if (is_array($snapshot)) {
                    foreach ($snapshot as $snap) {
                        UrunVariant::where('id', $snap['variant_id'])
                            ->where('stock_reserved', '>=', $snap['quantity'])
                            ->decrement('stock_reserved', $snap['quantity']);
                    }

                    Log::info('Stripe expired: rezervasyonlar serbest bırakıldı', [
                        'session_key' => $sessionKey,
                        'items'       => count($snapshot),
                    ]);
                }
            }
        }

        return response('OK', 200);
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        try {
            $address->delete();
            return back()->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not delete address.');
        }
    }

    private function createOrder($request, $cartItems)
    {
        $totals = $this->calculateTotals(collect($cartItems));

        if ($request->shipping_address_id) {
            $address      = Address::findOrFail($request->shipping_address_id);
            $shippingData = [
                'shipping_full_name' => $address->full_name,
                'shipping_phone'     => $address->phone,
                'shipping_address'   => $address->address,
                'shipping_city'      => $address->city,
                'shipping_district'  => $address->district,
                'shipping_zip'       => $address->zip_code,
            ];
        } else {
            $shippingData = [
                'shipping_full_name' => $request->shipping_full_name,
                'shipping_phone'     => $request->shipping_phone,
                'shipping_address'   => $request->shipping_address,
                'shipping_city'      => $request->shipping_city,
                'shipping_district'  => $request->shipping_district,
                'shipping_zip'       => $request->shipping_zip,
            ];
        }

        $isPaid      = in_array($request->payment_method, ['wallet', 'credit_card']);
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(str_replace('-', '', Str::uuid()), 0, 8));

        return Order::create([
            'order_number'    => $orderNumber,
            'user_id'         => Auth::id(),
            ...$shippingData,
            'payment_method'  => $request->payment_method,
            'payment_status'  => $isPaid ? 'paid' : 'pending',
            'subtotal'        => $totals['subtotal'],
            'shipping_cost'   => $totals['shippingCost'],
            'discount_amount' => $totals['discountAmount'],
            'tax_amount'      => $totals['taxAmount'],
            'total'           => $totals['total'],
            'customer_note'   => $request->customer_note,
            'status'          => 'pending',
            'paid_at'         => $isPaid ? now() : null,
        ]);
    }

    private function createOrderItems($order, $cartItems)
    {
        foreach ($cartItems as $item) {
            $variant = $item->urunVariant;
            $urun    = $variant->urun;
            $price   = $variant->discount_price ?? $variant->price;

            $affected = UrunVariant::where('id', $variant->id)
                ->whereRaw('(stock - stock_reserved) >= ?', [$item->quantity])
                ->decrement('stock', $item->quantity);

            if ($affected === 0) {
                throw new \Exception("Yetersiz stok: {$urun->name}. Lütfen sepetinizi güncelleyin.");
            }

            OrderItem::create([
                'order_id'            => $order->id,
                'urun_id'             => $urun->id,
                'urun_variant_id'     => $variant->id,
                'product_name'        => $urun->name,
                'variant_name'        => $variant->color . ($variant->size ? ' / ' . $variant->size : ''),
                'sku'                 => $variant->sku,
                'unit_price'          => $variant->price,
                'unit_discount_price' => $variant->discount_price,
                'quantity'            => $item->quantity,
                'subtotal'            => $price * $item->quantity,
                'total'               => $price * $item->quantity,
            ]);
        }
    }

    private function createOrderItemsFromReservation($order, $snapshotItems)
    {
        foreach ($snapshotItems as $item) {
            $variant = $item->urunVariant;
            $urun    = $variant->urun;
            $price   = $variant->discount_price ?? $variant->price;

            $affected = UrunVariant::where('id', $variant->id)
                ->where('stock', '>=', $item->quantity)
                ->decrement('stock', $item->quantity);

            if ($affected === 0) {
                throw new \Exception("Stok hatası: {$urun->name}.");
            }

            UrunVariant::where('id', $variant->id)
                ->where('stock_reserved', '>=', $item->quantity)
                ->decrement('stock_reserved', $item->quantity);

            OrderItem::create([
                'order_id'            => $order->id,
                'urun_id'             => $urun->id,
                'urun_variant_id'     => $variant->id,
                'product_name'        => $urun->name,
                'variant_name'        => $variant->color . ($variant->size ? ' / ' . $variant->size : ''),
                'sku'                 => $variant->sku,
                'unit_price'          => $variant->price,
                'unit_discount_price' => $variant->discount_price,
                'quantity'            => $item->quantity,
                'subtotal'            => $price * $item->quantity,
                'total'               => $price * $item->quantity,
            ]);
        }
    }

    private function releaseReservations($cartItems, $stopAtVariantId = null)
    {
        foreach ($cartItems as $item) {
            if ($stopAtVariantId && $item->urunVariant->id === $stopAtVariantId) {
                break;
            }
            UrunVariant::where('id', $item->urunVariant->id)
                ->where('stock_reserved', '>=', $item->quantity)
                ->decrement('stock_reserved', $item->quantity);
        }
    }

    private function createPayment($order, $request)
    {
        $status = 'pending';

        if ($request->payment_method === 'wallet') {
            $user = Auth::user();
            if ($user->wallet_balance >= $order->total) {
                $user->decrement('wallet_balance', $order->total);
                $status = 'paid';
            } else {
                throw new \Exception('Insufficient wallet balance');
            }
        }

        Payment::create([
            'order_id'       => $order->id,
            'payment_method' => $request->payment_method,
            'amount'         => $order->total,
            'currency'       => 'TRY',
            'status'         => $status,
        ]);
    }

    private function saveAddress($request)
    {
        Address::create([
            'user_id'    => Auth::id(),
            'title'      => 'New Address',
            'full_name'  => $request->shipping_full_name,
            'phone'      => $request->shipping_phone,
            'address'    => $request->shipping_address,
            'city'       => $request->shipping_city,
            'district'   => $request->shipping_district,
            'zip_code'   => $request->shipping_zip,
            'is_default' => Address::where('user_id', Auth::id())->count() === 0,
        ]);
    }

    private function getCartItems()
    {
        return CartItem::with(['urunVariant.urun', 'urunVariant.images'])
            ->where('user_id', Auth::id())
            ->get();
    }
}
