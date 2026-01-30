<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::view('/about', 'about')->name('home.about');
Route::view('/contact', 'contact')->name('home.contact');

Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'product_details'])->name('shop.product.details');

Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}',[CartController::class,'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}',[CartController::class,'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'remove_item'])->name('cart.item.remove');

Route::post('/wishlist/add',[WishlistController::class,'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/item/{rowId}',[WishlistController::class,'remove_item'])->name('wishlist.item.remove');

Route::get('/checkout',[CartController::class,'checkout'])->name('checkout.index');
Route::post('/cart/place-order',[CartController::class,'place_an_order'])->name('cart.place.order');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[UserController::class,'index'])->name('user.index');
    Route::get('/account-dashboard/orders',[UserController::class,'orders'])->name('user.orders');
    Route::get('/account-dashboard/orders/{order_id}',[UserController::class,'order_details'])->name('user.order.details');
    Route::get('/account-dashboard/addresses', [UserController::class, 'addresses'])->name('user.addresses');
    Route::get('/account-dashboard/account-details', [UserController::class, 'account_details'])->name('user.account_details');
    Route::post('/account-dashboard/account-details', [UserController::class, 'account_update'])->name('user.account_update');
    Route::get('/account-dashboard/wishlist', [UserController::class, 'wishlist'])->name('user.wishlist');
    Route::get('/account-dashboard/address/add', [UserController::class, 'add_address'])->name('user.address.add');
    Route::post('/account-dashboard/address/store', [UserController::class, 'store_address'])->name('user.address.store');
    Route::get('/account-dashboard/address/edit/{id}', [UserController::class, 'edit_address'])->name('user.address.edit');
    Route::put('/account-dashboard/address/update/{id}', [UserController::class, 'update_address'])->name('user.address.update');
});

Route::middleware(['auth',AuthAdmin::class])->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    Route::get('/admin/brands',[AdminController::class,'brands'])->name('admin.brands');
    Route::get('/admin/brand/add',[AdminController::class,'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brand/store',[AdminController::class,'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}',[AdminController::class,'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update',[AdminController::class,'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete',[AdminController::class,'brand_delete'])->name('admin.brand.delete');
    
    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/category/add',[AdminController::class,'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store',[AdminController::class,'category_store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit',[AdminController::class,'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update',[AdminController::class,'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete',[AdminController::class,'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/product/add',[AdminController::class,'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store',[AdminController::class,'product_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit',[AdminController::class,'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update',[AdminController::class,'product_update'])->name('admin.product.update');
    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/orders/{order_id}',[AdminController::class,'order_details'])->name('admin.order.details');
    Route::put('/admin/order/update-status',[AdminController::class,'update_order_status'])->name('admin.order.status.update');

    Route::delete('/admin/product/{id}/delete',[AdminController::class,'product_delete'])->name('admin.product.delete');
}); 

use App\Http\Controllers\MomoController;
Route::get('/momo/payment', [MomoController::class, 'payment'])->name('momo.payment');
Route::get('/momo/callback', [MomoController::class, 'callback'])->name('momo.callback');

use App\Http\Controllers\VirtualTryonController;

Route::get('/virtual-tryon', [VirtualTryonController::class, 'index'])->name('virtual-tryon.index');
Route::post('/virtual-tryon/upload', [VirtualTryonController::class, 'upload'])->name('virtual-tryon.upload');
Route::get('/virtual-tryon/result/{id}', [VirtualTryonController::class, 'getResult'])->name('virtual-tryon.result');

Route::middleware(['auth'])->group(function() {
    Route::get('/virtual-tryon/gallery', [VirtualTryonController::class, 'gallery'])->name('virtual-tryon.gallery');
});