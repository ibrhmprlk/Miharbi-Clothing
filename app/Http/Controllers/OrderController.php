<?php

namespace App\Http\Controllers;

use App\Models\MyOrder;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Cart;
use App\Models\UrunVariant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Kullanıcının tüm siparişlerini göster (Liste görünümü)
     */
    public function index()
    {
        $orders = MyOrder::where('user_id', Auth::id())
            ->with(['items.variant.urun.images', 'items.review'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $singleOrder = null;
        
        return view('myorders', compact('orders', 'singleOrder'));
    }

    /**
     * Belirli bir siparişin detaylarını göster (Detay görünümü)
     */
    public function show($id)
    {
        $singleOrder = MyOrder::where('user_id', Auth::id())
            ->with(['items.variant.urun.images', 'items.review'])
            ->findOrFail($id);

        // Diğer siparişler için liste (sidebar için)
        $orders = MyOrder::where('user_id', Auth::id())
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('myorders', compact('orders', 'singleOrder'));
    }

    /**
     * Bireysel ürün iptali
     */
   public function cancelItem(OrderItem $item)
{
    if ($item->order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }

    if ($item->order->status !== 'pending') {
        return back()->with('error', 'This order cannot be modified.');
    }

    // status kolonu yok, return_status kullan
    if ($item->return_status !== 'none') {
        return back()->with('error', 'Item already cancelled.');
    }

    // İptal işaretini return_status ile yap
    $item->update(['return_status' => 'requested']);

    // Sipariş toplamını yeniden hesapla (status yok, return_status filtrele)
    $order = $item->order;
    $activeItemsTotal = $order->items()
        ->where('return_status', 'none')
        ->sum('total'); // tabloda 'total' kolonu var

    $newTotal = $activeItemsTotal + $order->shipping_cost - $order->discount_amount;

    $order->update([
        'subtotal' => $activeItemsTotal,
        'total' => max(0, $newTotal)
    ]);

    // Tüm ürünler iptal edildiyse siparişi de iptal et
    $remainingItems = $order->items()->where('return_status', 'none')->count();
    if ($remainingItems === 0) {
        $order->update(['status' => 'cancelled']);
    }

    return back()->with('success', 'Item cancelled successfully.');
}
    /**
     * Sipariş oluştur (Checkout işlemi)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,cash_on_delivery',
            'customer_note' => 'nullable|string|max:500',
            
            // Adres bilgileri
            'selected_address' => 'nullable|exists:addresses,id',
            'shipping_full_name' => 'required_without:selected_address|string|max:255',
            'shipping_phone' => 'required_without:selected_address|string|min:10|max:11',
            'shipping_city' => 'required_without:selected_address|string|max:100',
            'shipping_district' => 'required_without:selected_address|string|max:100',
            'shipping_address' => 'required_without:selected_address|string|max:500',
            'shipping_zip' => 'nullable|string|max:10',
            
            // Kredi kartı bilgileri
            'card_number' => 'required_if:payment_method,credit_card|string|min:16|max:19',
            'card_expiry' => 'required_if:payment_method,credit_card|string|size:5',
            'card_cvc' => 'required_if:payment_method,credit_card|string|size:3',
            'card_holder' => 'required_if:payment_method,credit_card|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form fields.');
        }

        $user = Auth::user();

        // Sepet kontrolü
        $cartItems = Cart::where('user_id', $user->id)
            ->with(['urunVariant.urun'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('mycart')
                ->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            // Adres bilgilerini hazırla
            $addressData = $this->prepareAddressData($request, $user->id);

            // Fiyat hesaplamaları
            $priceDetails = $this->calculatePrices($cartItems);

            // Stok kontrolü ve düşürme
            $this->checkAndDecreaseStock($cartItems);

            // Sipariş oluştur
            $order = MyOrder::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                
                // Adres bilgileri
                'shipping_full_name' => $addressData['full_name'],
                'shipping_phone' => $addressData['phone'],
                'shipping_address' => $addressData['address'],
                'shipping_city' => $addressData['city'],
                'shipping_district' => $addressData['district'],
                'shipping_zip' => $addressData['zip'] ?? null,
                
                // Fatura adresi (aynı)
                'same_as_shipping' => true,
                'billing_full_name' => $addressData['full_name'],
                'billing_address' => $addressData['address'],
                
                // Ödeme ve durum
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'paid',
                'paid_at' => $request->payment_method === 'credit_card' ? now() : null,
                'status' => 'pending',
                
                // Fiyatlandırma
                'subtotal' => $priceDetails['subtotal'],
                'shipping_cost' => $priceDetails['shipping'],
                'tax_amount' => $priceDetails['tax'],
                'discount_amount' => $priceDetails['discount'],
                'total' => $priceDetails['total'],
                
                // Notlar
                'customer_note' => $request->customer_note,
            ]);

            // Sipariş kalemlerini oluştur
            foreach ($cartItems as $cartItem) {
                $variant = $cartItem->urunVariant;
                $unitPrice = $variant->discount_price ?? $variant->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'urun_variant_id' => $variant->id,
                    'urun_id' => $variant->urun_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $cartItem->quantity,
                    'product_name_snapshot' => $variant->urun->name,
                    'variant_details_snapshot' => "Renk: {$variant->color}" . ($variant->size ? ", Beden: {$variant->size}" : ''),
                    'status' => 'active',
                ]);
            }

            // Sepeti temizle
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order->id)
                ->with('success', "Your order has been successfully created! Your order number is: #{$order->order_number}");

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Order creation error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create order.')
                ->withInput();
        }
    }

    /**
     * Adres verilerini hazırla
     */
    private function prepareAddressData(Request $request, $userId)
    {
        if ($request->filled('selected_address')) {
            $address = Address::where('id', $request->selected_address)
                ->where('user_id', $userId)
                ->firstOrFail();

            return [
                'full_name' => $address->full_name,
                'phone' => $address->phone,
                'address' => $address->address,
                'city' => $address->city,
                'district' => $address->district,
                'zip' => $address->zip_code,
            ];
        }

        return [
            'full_name' => $request->shipping_full_name,
            'phone' => $request->shipping_phone,
            'address' => $request->shipping_address,
            'city' => $request->shipping_city,
            'district' => $request->shipping_district,
            'zip' => $request->shipping_zip,
        ];
    }

    /**
     * Fiyat hesaplamaları
     */
    private function calculatePrices($cartItems)
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $variant = UrunVariant::find($item->urun_variant_id);
            $price = $variant->discount_price ?? $variant->price;
            $subtotal += $price * $item->quantity;
        }

        $shipping = 0;
        $taxRate = 0.18;
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = 0;
        $total = $subtotal + $taxAmount + $shipping - $discountAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($taxAmount, 2),
            'discount' => round($discountAmount, 2),
            'total' => round($total, 2),
        ];
    }

 /**
 * Stok kontrolü ve düşürme
 */
private function checkAndDecreaseStock($cartItems)
{
    foreach ($cartItems as $item) {
        // ✅ ATOMIC STOK DÜŞÜRME
        $affected = UrunVariant::where('id', $item->urun_variant_id)
            ->where('stock', '>=', $item->quantity)
            ->decrement('stock', $item->quantity);

        if ($affected === 0) {
            $variant = UrunVariant::find($item->urun_variant_id);
            $productName = $variant?->urun?->name ?? 'Ürün';
            throw new \Exception("Yetersiz stok: {$productName}");
        }
    }
}

    /**
     * Benzersiz sipariş numarası oluştur
     */
    private function generateOrderNumber()
    {
        $prefix = 'MH';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        $timestamp = now()->format('His');
        
        return "{$prefix}-{$date}-{$timestamp}-{$random}";
    }

    /**
     * Tam sipariş iptali
     */
    public function cancel($id)
    {
        $order = MyOrder::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::beginTransaction();

        try {
            // Stokları geri ekle
            foreach ($order->items as $item) {
                if ($item->status !== 'cancelled') {
                    UrunVariant::where('id', $item->urun_variant_id)
                        ->increment('stock', $item->quantity);
                }
            }

            // Tüm ürünleri iptal et
            $order->items()->update(['status' => 'cancelled']);

            $order->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            return redirect()->route('myorders')
                ->with('success', 'Your order has been cancelled.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Cancelation failed.');
        }
    }

    /**
     * Siparişi tekrarla
     */
    public function reorder($id)
    {
        $order = MyOrder::where('user_id', Auth::id())
            ->with('items')
            ->findOrFail($id);

        $addedCount = 0;
        $unavailableItems = [];

        foreach ($order->items as $item) {
            if ($item->status === 'cancelled') continue;
            
            $variant = UrunVariant::find($item->urun_variant_id);
            
            if (!$variant || $variant->stock < $item->quantity) {
                $unavailableItems[] = $item->product_name_snapshot;
                continue;
            }

            $cartItem = Cart::where('user_id', Auth::id())
                ->where('urun_variant_id', $item->urun_variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $item->quantity);
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'urun_variant_id' => $item->urun_variant_id,
                    'quantity' => $item->quantity,
                ]);
            }
            $addedCount++;
        }

        $message = "$addedCount Product added to cart.";
        if (!empty($unavailableItems)) {
            $message .= " Products that are out of stock: " . implode(', ', $unavailableItems);
        }

        return redirect()->route('cart.index')
            ->with('success', $message);
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Admin: All orders list
     */
    public function adminIndex(Request $request)
    {
        $query = MyOrder::with(['user', 'items.variant.images'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_full_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => MyOrder::count(),
            'pending' => MyOrder::where('status', 'pending')->count(),
            'approved' => MyOrder::where('status', 'approved')->count(),
            'shipped' => MyOrder::where('status', 'shipped')->count(),
            'delivered' => MyOrder::where('status', 'delivered')->count(),
            'cancelled' => MyOrder::where('status', 'cancelled')->count(),
            'today' => MyOrder::whereDate('created_at', today())->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Admin: Order detail
     */
    public function adminShow(MyOrder $order)
    {
        $order->load(['user', 'items.variant.images', 'items.review.user']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, MyOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,processing,shipped,delivered,cancelled',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // Update timestamps based on status
        $updateData = ['status' => $newStatus];
        
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            // approved_at field doesn't exist in your migration, skip or add to migration
        } elseif ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
            $updateData['shipped_at'] = now();
        } elseif ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
            $updateData['delivered_at'] = now();
        }

        if (!empty($validated['admin_note'])) {
            $updateData['admin_note'] = $validated['admin_note'];
        }

        $order->update($updateData);

        return redirect()->back()->with('success', "Order status updated from {$oldStatus} to {$newStatus}");
    }

    /**
     * Admin: Update tracking info
     */
    public function updateTracking(Request $request, MyOrder $order)
    {
        $validated = $request->validate([
            'shipping_company' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->update([
            'shipping_company' => $validated['shipping_company'],
            'tracking_number' => $validated['tracking_number'],
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tracking information updated and order marked as shipped');
    }

    /**
     * Admin: Reviews list
     */
    public function reviewsIndex(Request $request)
    {
        $query = Review::with(['user', 'urun', 'variant', 'orderItem.order'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('urun', function($pq) use ($search) {
                      $pq->where('urun_adi', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
            'avg_rating' => Review::where('is_approved', true)->avg('rating') ?? 0,
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Admin: Approve review
     */
    public function approveReview(Review $review)
    {
        $review->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Review approved successfully');
    }

    /**
     * Admin: Delete review
     */
    public function destroyReview(Review $review)
    {
        $review->delete();
        return redirect()->back()->with('success', 'Review deleted successfully');
    }

    /**
     * Admin: Bulk review actions
     */
    public function bulkReviewAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,delete',
            'reviews' => 'required|array',
            'reviews.*' => 'exists:reviews,id',
        ]);

        $ids = $validated['reviews'];

        if ($validated['action'] === 'approve') {
            Review::whereIn('id', $ids)->update(['is_approved' => true]);
            $message = count($ids) . ' reviews approved successfully';
        } else {
            Review::whereIn('id', $ids)->delete();
            $message = count($ids) . ' reviews deleted successfully';
        }

        return redirect()->back()->with('success', $message);
    }
/**
 * Müşteri: Yorum gönder (Sadece ana rating)
 */
public function storeReview(Request $request)
{
    $validated = $request->validate([
        'urun_id' => 'required|exists:uruns,id',
        'urun_variant_id' => 'required|exists:urun_variants,id',
        'order_item_id' => 'required|exists:order_items,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10|max:1000',
    ]);

    // OrderItem'a ait siparişin bu kullanıcıya ait olduğunu kontrol et
    $orderItem = OrderItem::with('order')->findOrFail($validated['order_item_id']);
    
    if ($orderItem->order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }

    // Sipariş teslim edilmiş mi kontrol et
    if ($orderItem->order->status !== 'delivered') {
        return back()->with('error', 'You can only review delivered orders.');
    }

    // Zaten yorum yapılmış mı kontrol et
    if ($orderItem->review()->exists()) {
        return back()->with('error', 'You have already reviewed this item.');
    }

    Review::create([
        'user_id' => Auth::id(),
        'urun_id' => $validated['urun_id'],
        'urun_variant_id' => $validated['urun_variant_id'],
        'order_item_id' => $validated['order_item_id'],
        'rating' => $validated['rating'],  // Sadece ana rating
        'comment' => $validated['comment'],
        'is_approved' => false,
        'purchased_at' => $orderItem->order->created_at,
    ]);

    return back()->with('success', 'Your review has been submitted and is pending approval.');
}
}
