<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;

// --- НЭЭЛТТЭЙ ЗАМУУД (Нэвтрээгүй хүн ашиглаж болно) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/sellers/{id}', [\App\Http\Controllers\SellerController::class, 'show']);
Route::get('/seller-profile/{id}', [App\Http\Controllers\ProfileController::class, 'getSellerProfile']);
Route::get('/search-suggestions', [App\Http\Controllers\ProductController::class, 'searchSuggestions']);

// ЭНД НЭМЭХ: Нууц үг сэргээх замууд энд (Middleware-ийн гадна) байх ёстой
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/email/verify', [ProfileController::class, 'verifyEmail']);

// --- ХАМГААЛАЛТТАЙ ЗАМУУД (Заавал нэвтэрсэн байх ёстой) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/items', [CartController::class, 'getUserItems']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::post('/cart/remove', [CartController::class, 'remove']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);

    Route::get('/profile/data', [ProfileController::class, 'getProfileData']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::post('/email/verification-notification', [ProfileController::class, 'sendVerificationEmail']);
    Route::post('/profile/avatar/delete', [ProfileController::class, 'deleteAvatar']);
    Route::post('/purchase-requests', [ProductController::class, 'submitRequest']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/notifications', [App\Http\Controllers\ProfileController::class, 'getNotifications']);
    Route::post('/submit-review', [App\Http\Controllers\ProfileController::class, 'submitReview']);
    Route::post('/verification-requests', [App\Http\Controllers\ProfileController::class, 'submitVerificationRequest']);
    Route::post('/orders/{id}/pay', [App\Http\Controllers\OrderController::class, 'markAsPaid']);
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store']);
    Route::post('/requests/accept/{id}', [ProfileController::class, 'acceptRequest']);
    Route::post('/orders/pay/{id}', [ProfileController::class, 'completePayment']);
    Route::post('/requests/decline/{id}', [ProfileController::class, 'declineRequest']);
    Route::post('/cart/pay-all', [ProfileController::class, 'payCart']);
    Route::post('/orders/cart/checkout', [ProfileController::class, 'checkoutCart']);
    Route::post('/cart/pay-orders', [ProfileController::class, 'payCartOrders']);
    Route::put('/orders/{id}/shipping', [ProfileController::class, 'updateOrderShipping']);
    Route::delete('/orders/{id}', [App\Http\Controllers\ProfileController::class, 'cancelOrder']);
    Route::delete('/purchase-requests/{id}', [App\Http\Controllers\ProfileController::class, 'cancelPurchaseRequest']);
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::post('/reports', [\App\Http\Controllers\ProfileController::class, 'submitReport']);
    // Админ замууд
    Route::get('/admin/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/admin/products', [AdminController::class, 'getProducts']);
    Route::post('/admin/verifications/{id}/approve', [AdminController::class, 'approveProduct']);
    Route::delete('/admin/products/{id}', [AdminController::class, 'deleteProduct']);
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::post('/admin/users/{id}/toggle-block', [AdminController::class, 'toggleBlockUser']);

    Route::get('/admin/reports', [AdminController::class, 'getReports']);
    Route::post('/admin/reports/{id}/resolve', [AdminController::class, 'resolveReport']);

    Route::get('/admin/categories', [AdminController::class, 'getCategories']);
    Route::post('/admin/categories', [AdminController::class, 'addCategory']);
    Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory']);
    // ...
});
