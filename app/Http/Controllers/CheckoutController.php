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

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        foreach ($cartItems as $item) {
            $variant = $item->urunVariant;
            if ($variant->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "Insufficient stock for {$variant->urun->name}");
            }
        }

        $subtotal = $cartItems->sum(function($item) {
            $price = $item->urunVariant->discount_price ?? $item->urunVariant->price;
            return $price * $item->quantity;
        });

        // Shipping her zaman ücretsiz
        $shippingCost = 0;
        $discountAmount = session('discount_amount', 0);
        $taxAmount = ($subtotal - $discountAmount) * 0.18;
        $total = $subtotal - $discountAmount + $taxAmount; // shippingCost çıkarıldı

        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('PaymentInformation', compact(
            'cartItems', 'subtotal', 'shippingCost', 
            'discountAmount', 'taxAmount', 'total', 'addresses'
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
            $variant = UrunVariant::find($item->urunVariant->id);
            if ($variant->stock < $item->quantity) {
                throw new \Exception("Insufficient stock for {$variant->urun->name}");
            }
        }

        // Kapıda ödeme ise direkt sipariş oluştur
        if ($request->payment_method === 'cash_on_delivery') {
            DB::beginTransaction();
            $order = $this->createOrder($request);
            $this->createOrderItems($order, $cartItems);
            $this->createPayment($order, $request);
            if ($request->boolean('save_address') && !$request->shipping_address_id) {
                $this->saveAddress($request);
            }
            CartItem::where('user_id', Auth::id())->delete();
            DB::commit();
            return redirect()->route('myorders')->with('success', 'Your order has been placed successfully!');
        }

        // Kredi kartı ise Stripe'a yönlendir
        if ($request->payment_method === 'credit_card') {
            // Form verilerini session'a kaydet
            session(['checkout_request' => $request->all()]);

            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $subtotal = $cartItems->sum(function($item) {
                $price = $item->urunVariant->discount_price ?? $item->urunVariant->price;
                return $price * $item->quantity;
            });

            $discountAmount = session('discount_amount', 0);
            $taxAmount = ($subtotal - $discountAmount) * 0.18;
            $total = $subtotal - $discountAmount + $taxAmount;

            // Stripe TRY desteklemiyor, USD'ye çeviriyoruz (test için)
            $totalInCents = round($total * 100); // kuruş cinsinden

            $lineItems = [];
            foreach ($cartItems as $item) {
                $variant = $item->urunVariant;
                $price = $variant->discount_price ?? $variant->price;
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'try',
                        'product_data' => [
                            'name' => $variant->urun->name . ' (' . $variant->color . ($variant->size ? ' / ' . $variant->size : '') . ')',
                        ],
                        'unit_amount' => round($price * 100),
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            // KDV satırı ekle
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'try',
                    'product_data' => [
                        'name' => 'VAT (18%)',
                    ],
                    'unit_amount' => round($taxAmount * 100),
                ],
                'quantity' => 1,
            ];

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'customer_email' => Auth::user()->email,
            ]);

            return redirect($session->url);
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
        
        $session = \Stripe\Checkout\Session::retrieve($request->session_id);
        
        if ($session->payment_status !== 'paid') {
            return redirect()->route('checkout.index')->with('error', 'Payment could not be verified.');
        }

        $checkoutData = session('checkout_request');
        if (!$checkoutData) {
            return redirect()->route('checkout.index')->with('error', 'Session expired. Please try again.');
        }

        $fakeRequest = new Request($checkoutData);
        $cartItems = $this->getCartItems();

        DB::beginTransaction();
        $order = $this->createOrder($fakeRequest);
        $this->createOrderItems($order, $cartItems);
        
        Payment::create([
            'order_id'       => $order->id,
            'payment_method' => 'credit_card',
            'transaction_id' => $session->payment_intent,
            'amount'         => $order->total,
            'currency'       => 'TRY',
            'status'         => 'success',
            'response_data'  => json_encode($session->toArray()),
            'completed_at'   => now(),
        ]);

        if ($fakeRequest->boolean('save_address') && !$fakeRequest->shipping_address_id) {
            $this->saveAddress($fakeRequest);
        }

        CartItem::where('user_id', Auth::id())->delete();
        session()->forget('checkout_request');

        DB::commit();

        return redirect()->route('myorders');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Stripe success error: ' . $e->getMessage());
        return redirect()->route('checkout.index')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

public function cancel()
{
    return redirect()->route('checkout.index')->with('error', 'Payment cancelled. Please try again.');
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

    private function createOrder($request)
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(function($item) {
            $price = $item->urunVariant->discount_price ?? $item->urunVariant->price;
            return $price * $item->quantity;
        });

        // Shipping her zaman ücretsiz
        $shippingCost = 0;
        $discountAmount = session('discount_amount', 0);
        $taxAmount = ($subtotal - $discountAmount) * 0.18;
        $total = $subtotal - $discountAmount + $taxAmount; // shippingCost çıkarıldı

        if ($request->shipping_address_id) {
            $address = Address::findOrFail($request->shipping_address_id);
            $shippingData = [
                'shipping_full_name' => $address->full_name,
                'shipping_phone' => $address->phone,
                'shipping_address' => $address->address,
                'shipping_city' => $address->city,
                'shipping_district' => $address->district,
                'shipping_zip' => $address->zip_code,
            ];
        } else {
            $shippingData = [
                'shipping_full_name' => $request->shipping_full_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_zip' => $request->shipping_zip,
            ];
        }

        $isPaid = in_array($request->payment_method, ['wallet', 'credit_card']);

        return Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'user_id' => Auth::id(),
            ...$shippingData,
            'payment_method' => $request->payment_method,
            'payment_status' => $isPaid ? 'paid' : 'pending',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'customer_note' => $request->customer_note,
            'status' => 'pending',
            'paid_at' => $isPaid ? now() : null,
        ]);
    }

   private function createOrderItems($order, $cartItems)
{
    foreach ($cartItems as $item) {
        $variant = $item->urunVariant;
        $urun = $variant->urun;
        $price = $variant->discount_price ?? $variant->price;

        // ✅ ATOMIC STOK DÜŞÜRME - Race condition'ı önler
        // Tek sorguda: "Stok yeterli mi?" kontrolü + "Stok düşür" işlemi
        $affected = UrunVariant::where('id', $variant->id)
            ->where('stock', '>=', $item->quantity)  // Stok yeterli mi kontrol et
            ->decrement('stock', $item->quantity);  // Hemen düşür (atomic)

        // Eğer 0 satır etkilendiyse, stok yetersiz demektir
        if ($affected === 0) {
            throw new \Exception("Yetersiz stok: {$urun->name}. Ürün başka biri tarafından son anda satın alındı, lütfen sepetinizi güncelleyin.");
        }

   OrderItem::create([
    'order_id'            => $order->id,
    'urun_id'             => $urun->id,
    'urun_variant_id'     => $variant->id,
    'product_name'        => $urun->name,
    'variant_name'        => $variant->color . ($variant->size ? ' / ' . $variant->size : ''),
    'sku'                 => $variant->sku,
    'unit_price'          => $variant->discount_price ?? $variant->price,  // ← düzeltme
    'unit_discount_price' => $variant->discount_price,
    'quantity'            => $item->quantity,
    'subtotal'            => $price * $item->quantity,
    'total'               => $price * $item->quantity,
]);
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
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'amount' => $order->total,
            'currency' => 'TRY',
            'status' => $status,
        ]);
    }

    private function saveAddress($request)
    {
        Address::create([
            'user_id' => Auth::id(),
            'title' => 'New Address',
            'full_name' => $request->shipping_full_name,
            'phone' => $request->shipping_phone,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'district' => $request->shipping_district,
            'zip_code' => $request->shipping_zip,
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
