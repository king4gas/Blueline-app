<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\OrderReturnController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController; // Controller Chat User

// Admin Controllers
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\ChatController as AdminChatController; // Controller Chat Admin

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// === 1. PUBLIC ROUTES ===
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'featuredProducts' => Product::withCount('orderItems')->orderBy('order_items_count', 'desc')->take(4)->get(),
        'feedbacks' => Feedback::with('user')->where('rating', '>=', 4)->latest()->take(3)->get()
    ]);
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::post('/feedback', [PageController::class, 'storeFeedback'])->name('feedback.store');

// === 2. AUTHENTICATED ROUTES (User) ===
Route::middleware(['auth', 'verified'])->group(function () {
    
    // LIVE CHAT USER (Pengganti Complaint)
    Route::get('/chat/messages', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.store');
    
    Route::get('/dashboard', function () { return Inertia::render('Dashboard'); })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart & Checkout
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/checkout/process', [CartController::class, 'checkout'])->name('checkout.process');
    
    // Orders & Return
    Route::get('/my-orders', [OrderController::class, 'index'])->name('my-orders');
    Route::post('/orders/{order}/return', [OrderReturnController::class, 'store'])->name('orders.return');

    // Subscription & Payment
    Route::get('/my-subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/upload', [PaymentController::class, 'upload'])->name('payment.upload');
});

// === 3. ADMIN ROUTES ===
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $revenue = Order::whereIn('status', ['verified', 'shipped', 'completed'])->sum('total_price');
        $pendingOrders = Order::where('status', 'pending_verification')->count();
        $totalCustomers = User::count(); 
        $productsSold = DB::table('order_items')->join('orders', 'order_items.order_id', '=', 'orders.id')->whereIn('orders.status', ['verified', 'shipped', 'completed'])->sum('order_items.quantity');
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [ 'revenue' => (int) $revenue, 'pending' => $pendingOrders, 'customers' => $totalCustomers, 'sold' => (int) $productsSold ],
            'recentOrders' => $recentOrders
        ]);
    })->name('admin.dashboard');

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::post('/orders/{order}/return-verify', [AdminOrderController::class, 'verifyReturn'])->name('admin.orders.verify_return');

    // Product Management
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    // Subscription Monitoring
    Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('admin.subscriptions.index');

    // LIVE CHAT ADMIN (PENTING! INI YANG BIKIN ERROR SEBELUMNYA KALAU TIDAK ADA)
    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/chat/{user}/reply', [AdminChatController::class, 'store'])->name('admin.chat.store');
});

require __DIR__.'/auth.php';