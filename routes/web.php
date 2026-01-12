<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\PageController;

// === IMPORT MODEL & DB ===
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

// === 1. PUBLIC ROUTES (Bisa diakses siapa saja) ===

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        
        // === LOGIKA BARU: BEST SELLER ===
        // Mengambil 4 produk dengan jumlah pesanan terbanyak (otomatis)
        'featuredProducts' => Product::withCount('orderItems')
                                ->orderBy('order_items_count', 'desc') // Urutkan dari yang paling laku
                                ->take(4)
                                ->get(),
        
        // Ambil 3 Feedback Terbaru (Rating 4 ke atas)
        'feedbacks' => Feedback::where('rating', '>=', 4)
                        ->latest()
                        ->take(3)
                        ->get()
    ]);
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::post('/feedback', [PageController::class, 'storeFeedback'])->name('feedback.store');

// === 2. AUTHENTICATED ROUTES (Harus Login) ===
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard User (Opsional)
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- KERANJANG (CART) & CHECKOUT ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    
    // Route untuk Update Quantity (+/-) di Cart
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');

    // Route Proses Checkout
    Route::post('/checkout/process', [CartController::class, 'checkout'])->name('checkout.process');
    
    // --- PESANAN SAYA ---
    Route::get('/my-orders', [OrderController::class, 'index'])->name('my-orders');
});

// === 3. ADMIN ROUTES (Khusus Admin) ===
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    // === DASHBOARD DENGAN DATA REAL ===
    Route::get('/dashboard', function () {
        
        // 1. Hitung Total Pendapatan (Status: Verified/Shipped/Completed)
        $revenue = Order::whereIn('status', ['verified', 'shipped', 'completed'])
                        ->sum('total_price');

        // 2. Hitung Pesanan Baru (Menunggu Verifikasi)
        $pendingOrders = Order::where('status', 'pending_verification')->count();

        // 3. Hitung Total Pelanggan
        $totalCustomers = User::count(); 

        // 4. Hitung Produk Terjual
        $productsSold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['verified', 'shipped', 'completed'])
            ->sum('order_items.quantity');

        // 5. Ambil 5 Pesanan Terbaru
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'revenue' => (int) $revenue,
                'pending' => $pendingOrders,
                'customers' => $totalCustomers,
                'sold' => (int) $productsSold,
            ],
            'recentOrders' => $recentOrders
        ]);
    })->name('admin.dashboard');

    // === ORDER MANAGEMENT ===
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

    // === PRODUCT MANAGEMENT ===
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
});

require __DIR__.'/auth.php';