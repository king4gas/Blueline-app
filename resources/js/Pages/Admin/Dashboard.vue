<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Menerima data Real dari web.php
const props = defineProps({
    stats: Object,       // { revenue, pending, customers, sold }
    recentOrders: Array  // [List Pesanan Terbaru]
});

// Format Rupiah
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', minimumFractionDigits: 0}).format(n);

// Format Tanggal
const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short' });

// Warna Status Badge
const statusColor = (s) => {
    if(s === 'pending_verification') return 'bg-yellow-100 text-yellow-700';
    if(s === 'verified') return 'bg-blue-100 text-blue-700';
    if(s === 'shipped') return 'bg-purple-100 text-purple-700';
    if(s === 'completed') return 'bg-emerald-100 text-emerald-700';
    return 'bg-red-100 text-red-700';
};

// Mengolah Data Statistik untuk ditampilkan di Card
const statCards = computed(() => [
    { 
        label: 'Total Pendapatan', 
        value: formatRupiah(props.stats.revenue), 
        icon: '💰', 
        color: 'bg-emerald-50 text-emerald-600',
        desc: 'Semua pesanan selesai'
    },
    { 
        label: 'Pesanan Baru', 
        value: props.stats.pending, 
        icon: '🔔', 
        color: 'bg-blue-50 text-blue-600',
        desc: 'Menunggu verifikasi'
    },
    { 
        label: 'Total Pelanggan', 
        value: props.stats.customers, 
        icon: '👥', 
        color: 'bg-purple-50 text-purple-600',
        desc: 'User terdaftar'
    },
    { 
        label: 'Item Terjual', 
        value: props.stats.sold, 
        icon: '📦', 
        color: 'bg-amber-50 text-amber-600',
        desc: 'Unit produk keluar'
    },
]);
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>Overview Bisnis</template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div v-for="(stat, index) in statCards" :key="index" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div :class="`w-12 h-12 rounded-xl flex items-center justify-center text-2xl ${stat.color}`">
                        {{ stat.icon }}
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-slate-50 text-slate-400">
                        Update Terbaru
                    </span>
                </div>
                <h3 class="text-slate-500 text-sm font-bold mb-1">{{ stat.label }}</h3>
                <div class="text-2xl font-black text-slate-800">{{ stat.value }}</div>
                <div class="text-xs text-slate-400 mt-1">{{ stat.desc }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div v-if="stats.pending > 0" class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-8 text-white flex flex-col sm:flex-row justify-between items-center shadow-lg shadow-blue-900/20 gap-4">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Halo Admin! 👋</h3>
                        <p class="text-blue-100 text-sm">
                            Ada <span class="font-bold text-white bg-white/20 px-2 rounded">{{ stats.pending }} pesanan baru</span> yang menunggu verifikasi Anda.
                        </p>
                    </div>
                    <Link :href="route('admin.orders.index')" class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold shadow-md hover:bg-gray-50 transition text-sm whitespace-nowrap">
                        Proses Sekarang &rarr;
                    </Link>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 text-lg">Transaksi Terakhir</h3>
                        <Link :href="route('admin.orders.index')" class="text-xs text-blue-600 font-bold hover:underline">Lihat Semua</Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400">
                                <tr>
                                    <th class="px-6 py-3">Invoice</th>
                                    <th class="px-6 py-3">Pelanggan</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs">Belum ada data transaksi.</td>
                                </tr>
                                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-mono font-bold text-slate-700">{{ order.invoice_number }}</div>
                                        <div class="text-[10px] text-slate-400">{{ formatDate(order.created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium">{{ order.user.name }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="`px-2 py-1 rounded-full text-[10px] font-bold uppercase ${statusColor(order.status)}`">
                                            {{ order.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-800">
                                        {{ formatRupiah(order.total_price) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <Link :href="route('admin.products.create')" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center text-xl group-hover:scale-110 transition">📦</div>
                            <span class="font-bold text-sm">Tambah Produk Baru</span>
                        </Link>
                        <Link :href="route('admin.orders.index')" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-blue-600 transition group">
                            <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center text-xl group-hover:scale-110 transition">🔍</div>
                            <span class="font-bold text-sm">Cek Bukti Transfer</span>
                        </Link>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl p-6 text-white text-center relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="text-4xl mb-2">📈</div>
                        <h3 class="font-bold text-lg mb-1">Laporan Bulanan</h3>
                        <p class="text-slate-400 text-xs mb-4">Download rekap penjualan dalam format PDF/Excel.</p>
                        <button class="px-4 py-2 bg-blue-600 rounded-lg font-bold text-xs hover:bg-blue-500 w-full transition">Download Report</button>
                    </div>
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl translate-x-10 -translate-y-10"></div>
                </div>
            </div>

        </div>

    </AdminLayout>
</template>