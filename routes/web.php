<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home Page
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }
    }
    return view('welcome');
});

// Check Email (Client-side validation)
Route::post('/check-email', [App\Http\Controllers\Auth\RegisteredUserController::class, 'checkEmail'])->name('check.email');

// Shop Page (Protected)
Route::get('/shop', [ProductController::class, 'shop'])
    ->name('shop')
    ->middleware('auth');

// Product Details (Public or Auth? Project seems mixed. Let's make it public but view uses auth checks)
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');


/*
|--------------------------------------------------------------------------
| Default Dashboard (Laravel Breeze / Jetstream)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->role === 'seller') {
            return redirect()->route('seller.dashboard');
        } else {
            return redirect()->route('shop');
        }
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('seller')->group(function () {

    // Seller Dashboard
    Route::get('/dashboard', [ProductController::class, 'dashboard'])
        ->name('seller.dashboard');

    // Seller Orders
    Route::get('/orders', [ProductController::class, 'orders'])
        ->name('seller.orders');

    // Seller Notifications
    Route::get('/notifications', [ProductController::class, 'notifications'])
        ->name('seller.notifications');

    // Update Shipping Status
    Route::post('/orders/{id}/update-shipping', [ProductController::class, 'updateShipping'])
        ->name('seller.orders.update-shipping');
    
    // Seller Order Tracking
    Route::get('/orders/{id}/track', [ProductController::class, 'trackOrder'])
        ->name('seller.orders.track');
    
    // Mark Notification as Read
    Route::post('/notifications/{id}/read', [ProductController::class, 'markNotificationAsRead'])
        ->name('seller.notifications.read');

    // Seller Products List
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    // Create Product Form
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    // Store Product
    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    // Edit Product
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    // Update Product
    Route::put('/products/{id}', [ProductController::class, 'update'])
        ->name('products.update');

    // Delete Product
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
});

/*
|--------------------------------------------------------------------------
| Wishlist Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/add/{id}', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('wishlist/remove/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Sellers
    Route::get('/sellers', [AdminController::class, 'sellers'])->name('admin.sellers');
    Route::post('/sellers/{id}/approve', [AdminController::class, 'approveSeller'])->name('admin.sellers.approve');
    Route::post('/sellers/{id}/decline', [AdminController::class, 'declineSeller'])->name('admin.sellers.decline');
    Route::delete('/sellers/{id}', [AdminController::class, 'deleteSeller'])->name('admin.sellers.delete');

    // Buyers (Orders)
    Route::get('/buyers', [AdminController::class, 'buyers'])->name('admin.buyers');
    Route::get('/fraud-orders', [AdminController::class, 'fraudOrders'])->name('admin.orders.fraud');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/orders/{id}/approve', [AdminController::class, 'approveOrder'])->name('admin.orders.approve');
    Route::post('/orders/{id}/decline', [AdminController::class, 'declineOrder'])->name('admin.orders.decline');
    Route::delete('/orders/{id}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::post('/products/{id}/approve', [AdminController::class, 'approveProduct'])->name('admin.products.approve');
    Route::post('/products/{id}/decline', [AdminController::class, 'declineProduct'])->name('admin.products.decline');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    // Fraud Rules
    Route::resource('fraud-rules', App\Http\Controllers\Admin\FraudRuleController::class);

    // Bundle Rules
    Route::resource('bundle-rules', App\Http\Controllers\Admin\BundleRuleController::class);

    // Order Ranking Settings
    Route::get('/ranking-settings', [App\Http\Controllers\Admin\RankingSettingsController::class, 'index'])->name('admin.ranking-settings.index');
    Route::post('/ranking-settings', [App\Http\Controllers\Admin\RankingSettingsController::class, 'update'])->name('admin.ranking-settings.update');

    // Returns Management
    Route::get('/returns', [App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('admin.returns.index');
    Route::post('/returns/{return}/approve', [App\Http\Controllers\Admin\ReturnController::class, 'approve'])->name('admin.returns.approve');
    Route::post('/returns/{return}/reject', [App\Http\Controllers\Admin\ReturnController::class, 'reject'])->name('admin.returns.reject');


});


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('cart/add/{id}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
});

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/

Route::get('checkout', function() { return redirect()->route('cart.index'); });
Route::post('checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout.process')->middleware('auth');
Route::post('payment/callback', [App\Http\Controllers\OrderController::class, 'paymentCallback'])->name('payment.callback')->middleware('auth');
Route::get('orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index')->middleware('auth');
Route::get('orders/{id}/track', [App\Http\Controllers\OrderController::class, 'trackOrder'])->name('orders.track')->middleware('auth');

// Product Reviews
Route::post('/products/{product}/review', [App\Http\Controllers\ProductReviewController::class, 'store'])->name('products.review.store')->middleware('auth');

// Order Returns (Customer)
Route::post('/orders/{order}/return', [App\Http\Controllers\OrderReturnController::class, 'store'])->name('orders.return.store')->middleware('auth');
Route::post('/orders/{order}/claim-refund', [App\Http\Controllers\OrderReturnController::class, 'claimRefund'])->name('orders.claim-refund')->middleware('auth');



require __DIR__.'/auth.php';

