<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrunController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ==================== PUBLIC ROUTES (şifresiz) ====================

Route::get('/', [UrunController::class, 'index'])->name('welcome');

Route::get('/reviews/variant/{variant}', [UrunController::class, 'getVariantReviews'])->name('reviews.variant');

Route::get('/adminpanel', [UrunController::class, 'urun'])->name('adminpanel');
Route::get('/userlogin', [UrunController::class, 'login'])->name('userlogin');

Route::get('/urunler', [UrunController::class, 'urunler'])->name('urunler');
Route::get('/urundetay/{id}', [UrunController::class, 'urundetay'])->name('urundetay');

Route::get('/create', [UrunController::class, 'create'])->name('create');
Route::post('/admin/adminpanel', [UrunController::class, 'store'])->name('uruns.store');

Route::get('/about', [UrunController::class, 'aboutPage'])->name('about');
Route::post('/about', [UrunController::class, 'about'])->name('about.store');
Route::get('/editAbout/{id}', [UrunController::class, 'editAbout'])->name('editAbout');
Route::post('/updateAbout/{id}', [UrunController::class, 'updateAbout'])->name('updateAbout');

Route::get('/categories', [CategoriesController::class, 'categories'])->name('categories');
Route::post('/admin/category', [CategoriesController::class, 'store'])->name('categories.store');
Route::delete('/category-delete/{id}', [CategoriesController::class, 'deleteCategory'])->name('category.delete');

Route::delete('/variant-delete', [UrunController::class, 'variantDelete'])->name('variant.delete');
Route::put('/variant-all-update', [UrunController::class, 'variantUpdate'])->name('variant.update');

Route::get('/contact', function () { return view('dashboard'); })->name('contact.index');
Route::post('/contact', [ContactController::class, 'sendMail'])->name('contact.send');

Route::post('/payment/callback', [PaymentGatewayController::class, 'callback'])->name('payment.callback');

// ==================== ADMIN ROUTES (şifresiz) ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'adminShow'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.tracking');

    Route::get('/reviews', [OrderController::class, 'reviewsIndex'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [OrderController::class, 'approveReview'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [OrderController::class, 'destroyReview'])->name('reviews.destroy');
    Route::post('/reviews/bulk', [OrderController::class, 'bulkReviewAction'])->name('reviews.bulk');
});

// ==================== AUTH ROUTES (şifreli) ====================
Route::middleware(['auth', 'nocache'])->group(function () {

    Route::get('/dashboard', [UrunController::class, 'dashboard'])
        ->middleware('verified')
        ->name('dashboard');

    // FAVORITES
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/toggle/{urunVariant}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/favorites/{urunVariant}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/favorites/check/{variantId}', [FavoriteController::class, 'check']);

    // CART
    Route::get('/mycart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/mycart/add/{urunVariant}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/mycart/remove/{urunVariant}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/mycart/update/{urunVariant}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/mycart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // ADDRESS
    Route::delete('/address/{address}', [CheckoutController::class, 'deleteAddress'])->name('address.delete');

    // CHECKOUT
// CHECKOUT
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');  // ← değişti
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');     // ← eklendi
});

    // MY ORDERS
    Route::get('/myorders', [OrderController::class, 'index'])->name('myorders');
    Route::get('/myorders/{order}', [OrderController::class, 'show'])->name('myorders.show');
    Route::post('/myorders/{order}/cancel', [OrderController::class, 'cancel'])->name('myorders.cancel');
    Route::post('/myorders/{order}/reorder', [OrderController::class, 'reorder'])->name('myorders.reorder');
    Route::post('/myorders/item/{item}/cancel', [OrderController::class, 'cancelItem'])->name('myorders.cancel.item');

    // REVIEWS
    Route::post('/reviews', [OrderController::class, 'storeReview'])->name('reviews.store');

    // PAYMENT
    Route::get('/payment/gateway/{order}', [PaymentGatewayController::class, 'index'])->name('payment.gateway');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
