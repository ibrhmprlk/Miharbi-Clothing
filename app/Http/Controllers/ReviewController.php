<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Yorum kaydet (Sipariş detayından)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'urun_id' => 'required|exists:uruns,id',
            'urun_variant_id' => 'nullable|exists:urun_variants,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'rating_quality' => 'nullable|integer|min:1|max:5',
            'rating_fit' => 'nullable|integer|min:1|max:5',
            'rating_shipping' => 'nullable|integer|min:1|max:5',
            'rating_price' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $orderItem = OrderItem::findOrFail($validated['order_item_id']);

        // Güvenlik kontrolleri
        if ($orderItem->order->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Sadece teslim edilmiş ürünlere yorum yapılabilir
        if ($orderItem->order->status !== 'delivered') {
            return back()->with('error', 'You can only review delivered items.');
        }

        // Daha önce yorum yapılmış mı kontrol et
        if ($orderItem->review) {
            return back()->with('error', 'You already reviewed this item.');
        }

        // İptal edilmiş ürüne yorum yapılamaz
        if ($orderItem->status === 'cancelled') {
            return back()->with('error', 'Cannot review cancelled items.');
        }

        // Yorum oluştur
        $review = Review::create([
            'user_id' => Auth::id(),
            'urun_id' => $validated['urun_id'],
            'urun_variant_id' => $validated['urun_variant_id'],
            'order_item_id' => $validated['order_item_id'],
            'rating' => $validated['rating'],
            'rating_quality' => $validated['rating_quality'],
            'rating_fit' => $validated['rating_fit'],
            'rating_shipping' => $validated['rating_shipping'],
            'rating_price' => $validated['rating_price'],
            'comment' => $validated['comment'],
            'purchased_at' => $orderItem->order->created_at
        ]);

        return back()->with('success', 'Review submitted successfully! It will be published after moderation.');
    }

    /**
     * Yorum güncelle
     */
    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated successfully.');
    }

    /**
     * Yorum sil
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
