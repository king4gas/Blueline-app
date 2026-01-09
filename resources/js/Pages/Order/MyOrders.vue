<script setup>
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({ orders: Array });

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR'}).format(n);
const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'});

// Styling Badge Modern
const getStatusBadge = (s) => {
    if(s==='pending_verification') return {class:'bg-amber-100 text-amber-700 border border-amber-200', label:'⏳ Menunggu Verifikasi', icon:'⏳'};
    if(s==='verified') return {class:'bg-blue-50 text-blue-700 border border-blue-200', label:'✅ Terverifikasi', icon:'✅'};
    if(s==='shipped') return {class:'bg-purple-50 text-purple-700 border border-purple-200', label:'🚚 Sedang Diproses', icon:'🚚'};
    if(s==='completed') return {class:'bg-emerald-50 text-emerald-700 border border-emerald-200', label:'🎉 Selesai', icon:'🎉'};
    return {class:'bg-red-50 text-red-700 border border-red-200', label:'❌ Dibatalkan', icon:'❌'};
};
</script>

<template>
    <Head title="Pesanan Saya" />

    <UserLayout>
        <div class="min-h-screen bg-[#F8FAFC] py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-bold text-slate-900">Riwayat Pesanan</h1>
                    <Link :href="route('products.index')" class="text-sm font-bold text-blue-600 hover:underline">
                        + Pesan Lagi
                    </Link>
                </div>

                <div v-if="orders.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="text-lg font-bold text-slate-900">Belum ada pesanan</h3>
                    <p class="text-slate-500 text-sm mt-2">Semua riwayat transaksi Anda akan muncul di sini.</p>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="order in orders" :key="order.id" class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300 overflow-hidden">
                        
                        <div class="px-6 py-4 bg-slate-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Invoice</div>
                                <div class="font-mono font-bold text-slate-700">{{ order.invoice_number }}</div>
                            </div>
                            <div class="text-right">
                                <span :class="`px-4 py-1.5 rounded-full text-xs font-bold ${getStatusBadge(order.status).class}`">
                                    {{ getStatusBadge(order.status).label }}
                                </span>
                                <div class="text-xs text-slate-400 mt-1.5">{{ formatDate(order.created_at) }}</div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-slate-200"></div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">{{ item.product_name }}</div>
                                            <div class="text-xs text-slate-500">{{ item.quantity }} x {{ formatRupiah(item.price) }}</div>
                                        </div>
                                    </div>
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ formatRupiah(item.price * item.quantity) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-white border-t border-gray-50 flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-500">Total Pembayaran</span>
                            <span class="text-xl font-bold text-blue-600">{{ formatRupiah(order.total_price) }}</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </UserLayout>
</template>