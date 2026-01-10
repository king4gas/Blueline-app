<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({ orders: Array });

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', minimumFractionDigits: 0}).format(n);
const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'});

// Styling Badge Modern Dark Mode
const getStatusBadge = (s) => {
    if(s==='pending_verification') return {class:'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20', label:'⏳ Menunggu Verifikasi'};
    if(s==='verified') return {class:'bg-blue-500/10 text-blue-400 border border-blue-500/20', label:'✅ Terverifikasi'};
    if(s==='shipped') return {class:'bg-purple-500/10 text-purple-400 border border-purple-500/20', label:'🚚 Sedang Diproses'};
    if(s==='completed') return {class:'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20', label:'🎉 Selesai'};
    return {class:'bg-red-500/10 text-red-400 border border-red-500/20', label:'❌ Dibatalkan'};
};
</script>

<template>
    <Head title="Pesanan Saya" />

    <UserLayout>
        <div class="min-h-screen bg-slate-950 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10 gap-4">
                    <h1 class="text-3xl font-black text-white flex items-center gap-3">
                        <span class="text-cyan-400">📦</span> Riwayat Pesanan
                    </h1>
                    <Link :href="route('products.index')" class="px-6 py-2.5 bg-cyan-600 text-white rounded-full font-bold text-sm hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/50">
                        + Pesan Lagi
                    </Link>
                </div>

                <div v-if="orders.length === 0" class="bg-slate-900 rounded-[2rem] p-16 text-center border border-slate-800 border-dashed">
                    <div class="w-24 h-24 bg-slate-800 text-slate-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-6">📦</div>
                    <h3 class="text-xl font-bold text-white mb-2">Belum ada pesanan</h3>
                    <p class="text-slate-400 text-sm max-w-sm mx-auto">Semua riwayat transaksi Anda akan muncul di sini.</p>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="order in orders" :key="order.id" class="group bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl overflow-hidden hover:border-slate-700 transition duration-300">
                        
                        <div class="px-8 py-5 bg-slate-950/50 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Invoice</div>
                                <div class="font-mono font-bold text-cyan-400 tracking-wide text-lg">#{{ order.invoice_number || order.id }}</div>
                            </div>
                            <div class="text-right">
                                <span :class="`inline-flex px-4 py-1.5 rounded-full text-xs font-bold ${getStatusBadge(order.status).class}`">
                                    {{ getStatusBadge(order.status).label }}
                                </span>
                                <div class="text-xs text-slate-500 mt-2 font-medium">{{ formatDate(order.created_at) }}</div>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="space-y-6">
                                <div v-for="item in order.items" :key="item.id" class="flex items-start justify-between group/item">
                                    <div class="flex items-start gap-4">
                                        
                                        <div class="w-16 h-16 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0 shadow-inner">
                                            <span class="text-3xl">⚡</span>
                                        </div>

                                        <div>
                                            <div class="font-bold text-white text-base mb-1 group-hover/item:text-cyan-400 transition">
                                                {{ item.product_name || item.product.name }}
                                            </div>
                                            <div class="text-sm text-slate-500 font-mono">
                                                {{ item.quantity }} x {{ formatRupiah(item.price) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="font-bold text-white text-base self-center">
                                        {{ formatRupiah(item.price * item.quantity) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-slate-950/30 border-t border-slate-800 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-400">Total Pembayaran</span>
                            <span class="text-2xl font-black text-white">{{ formatRupiah(order.total_price) }}</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </UserLayout>
</template>