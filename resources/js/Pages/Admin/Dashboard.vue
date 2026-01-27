<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    stats: Object,
    recentOrders: Array
});

// Helper Format Rupiah
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Helper Tanggal
const formatDate = (dateString) => {
    const options = { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Helper Warna Status
const getStatusColor = (status) => {
    switch(status) {
        case 'pending_verification': return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/50';
        case 'verified': return 'bg-blue-500/20 text-blue-400 border-blue-500/50';
        case 'shipped': return 'bg-cyan-500/20 text-cyan-400 border-cyan-500/50';
        case 'completed': return 'bg-emerald-500/20 text-emerald-400 border-emerald-500/50';
        case 'return_requested': return 'bg-red-500/20 text-red-400 border-red-500/50';
        default: return 'bg-slate-700 text-slate-300';
    }
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="space-y-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Dashboard Overview</h1>
                <p class="text-slate-400 mt-1">Pantau performa bisnis dan aktivitas jaringan hari ini.</p>
            </div>
            <div class="flex items-center gap-3 bg-slate-900 p-2 rounded-xl border border-slate-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">System Online</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-cyan-500/50 transition duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition group-hover:bg-cyan-500/20"></div>
                <div class="relative z-10">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Pendapatan</div>
                    <div class="text-2xl font-black text-white">{{ formatRupiah(stats.revenue) }}</div>
                    <div class="mt-2 text-xs text-emerald-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        <span>Update Realtime</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-yellow-500/50 transition duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-yellow-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition group-hover:bg-yellow-500/20"></div>
                <div class="relative z-10">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Menunggu Verifikasi</div>
                    <div class="text-3xl font-black text-white">{{ stats.pending }}</div>
                    <div class="mt-2 text-xs text-yellow-500 font-bold">
                        {{ stats.pending > 0 ? 'Perlu tindakan segera!' : 'Semua aman terkendali' }}
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-blue-500/50 transition duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition group-hover:bg-blue-500/20"></div>
                <div class="relative z-10">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Pelanggan</div>
                    <div class="text-3xl font-black text-white">{{ stats.customers }}</div>
                    <div class="mt-2 text-xs text-slate-500">User Terdaftar</div>
                </div>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-emerald-500/50 transition duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10 transition group-hover:bg-emerald-500/20"></div>
                <div class="relative z-10">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Layanan Aktif</div>
                    <div class="text-3xl font-black text-white">{{ stats.active_subs }}</div>
                    <div class="mt-2 text-xs text-slate-500">Koneksi Berjalan</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
                <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg">Transaksi Terbaru</h3>
                    <Link :href="route('admin.orders.index')" class="text-xs font-bold text-cyan-400 hover:text-cyan-300">Lihat Semua &rarr;</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-white">{{ order.invoice_number }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white">{{ order.user.name }}</div>
                                    <div class="text-[10px] opacity-70">{{ formatDate(order.created_at) }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-white">{{ formatRupiah(order.total_price) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border" 
                                          :class="getStatusColor(order.status)">
                                        {{ order.status.replace('_', ' ') }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="recentOrders.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada transaksi.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-gradient-to-br from-cyan-900/40 to-slate-900 rounded-2xl p-6 border border-cyan-500/20">
                    <h3 class="font-bold text-white mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <Link :href="route('admin.products.create')" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-cyan-600 hover:text-white rounded-xl transition border border-slate-700 group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-cyan-400 group-hover:text-white"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                            <span class="text-xs font-bold">Tambah Produk</span>
                        </Link>
                        <Link :href="route('admin.chat.index')" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-emerald-600 hover:text-white rounded-xl transition border border-slate-700 group">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-emerald-400 group-hover:text-white"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <span class="text-xs font-bold">Buka Chat</span>
                        </Link>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6">
                    <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Network Health
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>Bandwidth Usage</span>
                                <span class="text-cyan-400">78%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2">
                                <div class="bg-cyan-500 h-2 rounded-full" style="width: 78%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>Server Load</span>
                                <span class="text-emerald-400">42%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: 42%"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-slate-950 p-3 rounded-lg text-center">
                                <div class="text-[10px] text-slate-500 uppercase">Ping</div>
                                <div class="text-white font-bold text-lg">12ms</div>
                            </div>
                            <div class="bg-slate-950 p-3 rounded-lg text-center">
                                <div class="text-[10px] text-slate-500 uppercase">Uptime</div>
                                <div class="text-white font-bold text-lg">99.9%</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>