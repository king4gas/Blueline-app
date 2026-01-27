<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Ambil semua user yang pernah order Layanan (Service) dengan status sukses
        $subscribers = User::whereHas('orders', function ($query) {
            $query->whereIn('status', ['verified', 'shipped', 'completed'])
                  ->whereHas('items.product', function ($q) {
                      $q->where('type', 'service');
                  });
        })->with(['orders' => function ($query) {
            // Ambil order layanan terakhir yang sukses
            $query->whereIn('status', ['verified', 'shipped', 'completed'])
                  ->whereHas('items.product', function ($q) {
                      $q->where('type', 'service');
                  })
                  ->latest()
                  ->with('items.product');
        }])->get();

        // Format data untuk tabel Admin
        $activeSubscriptions = $subscribers->map(function ($user) {
            $lastOrder = $user->orders->first();
            
            if (!$lastOrder) return null;

            $productItem = $lastOrder->items->first(fn($i) => $i->product->type === 'service');
            if (!$productItem) return null;

            $startDate = Carbon::parse($lastOrder->created_at);
            // Ambil durasi dari database product, default 30 hari
            $duration = $productItem->product->duration ?? 30; 
            $expiredDate = $startDate->copy()->addDays($duration);
            $daysRemaining = Carbon::now()->diffInDays($expiredDate, false);

            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'service_name' => $productItem->product_name,
                'invoice' => $lastOrder->invoice_number,
                'start_date' => $startDate->format('d M Y'),
                'expired_date' => $expiredDate->format('d M Y'),
                'days_remaining' => (int) $daysRemaining,
                'status' => $daysRemaining > 0 ? 'Active' : 'Expired',
            ];
        })->filter()->values(); // Hapus yang null

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $activeSubscriptions
        ]);
    }
}