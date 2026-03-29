<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\UrunVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Sepeti göster
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $cartItems = $user->cartItems()
            ->with(['urunVariant.urun', 'urunVariant.images'])
            ->latest()
            ->get();
        
        $total = $cartItems->sum(function($item) {
            $price = $item->urunVariant->discount_price ?? $item->urunVariant->price;
            return $price * $item->quantity;
        });
        
        return view('mycart', compact('cartItems', 'total'));
    }

    /**
     * Sepete ürün ekle
     */
    public function add(Request $request, UrunVariant $urunVariant)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:10'
        ]);
        
        $quantity = $request->input('quantity', 1);
        
        // Stok kontrolü
        if ($urunVariant->stock < $quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Current stock: ' . $urunVariant->stock
            ], 400);
        }
        
        $existingItem = $user->cartItems()
            ->where('urun_variant_id', $urunVariant->id)
            ->first();
        
        if ($existingItem) {
            // Mevcut ürün var, quantity artır
            $newQuantity = $existingItem->quantity + $quantity;
            
            if ($newQuantity > $urunVariant->stock) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient stock. Current stock: ' . $urunVariant->stock
                ], 400);
            }
            
            if ($newQuantity > 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can add a maximum of 10 pieces of a product.'
                ], 400);
            }
            
            $existingItem->update(['quantity' => $newQuantity]);
            
            return response()->json([
                'status' => 'updated',
                'message' => 'Cart updated',
                'quantity' => $newQuantity
            ]);
        }
        
        // Yeni ürün ekle
        $user->cartItems()->create([
            'urun_variant_id' => $urunVariant->id,
            'quantity' => $quantity
        ]);
        
        // Sepet sayısını döndür
        $cartCount = $user->cartItems()->sum('quantity');
        
        return response()->json([
            'status' => 'added',
            'message' => 'Added to cart',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Sepetten ürün çıkar
     */
    public function remove(UrunVariant $urunVariant)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->cartItems()
            ->where('urun_variant_id', $urunVariant->id)
            ->delete();
        
        $cartCount = $user->cartItems()->sum('quantity');
        
        return response()->json([
            'status' => 'removed',
            'message' => 'Removed from cart',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Miktarı güncelle
     */
    public function updateQuantity(Request $request, UrunVariant $urunVariant)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);
        
        $quantity = $request->input('quantity');
        
        // Stok kontrolü
        if ($quantity > $urunVariant->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Current stock: ' . $urunVariant->stock
            ], 400);
        }
        
        $cartItem = $user->cartItems()
            ->where('urun_variant_id', $urunVariant->id)
            ->first();
        
        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found in cart'
            ], 404);
        }
        
        $cartItem->update(['quantity' => $quantity]);
        
        // Toplam fiyatı hesapla
        $cartItems = $user->cartItems()->with('urunVariant')->get();
        $total = $cartItems->sum(function($item) {
            $price = $item->urunVariant->discount_price ?? $item->urunVariant->price;
            return $price * $item->quantity;
        });
        
        $itemTotal = ($urunVariant->discount_price ?? $urunVariant->price) * $quantity;
        
        return response()->json([
            'status' => 'updated',
            'message' => 'Miktar güncellendi',
            'quantity' => $quantity,
            'itemTotal' => number_format($itemTotal, 2),
            'cartTotal' => number_format($total, 2),
            'cartCount' => $cartItems->sum('quantity')
        ]);
    }

    /**
     * Sepeti temizle
     */
    public function clear()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->cartItems()->delete();
        
        return response()->json([
            'status' => 'cleared',
            'message' => 'Cart cleared'
        ]);
    }
}