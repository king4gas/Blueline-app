<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: UserLayout });

const props = defineProps({
    order: Object
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// Fungsi Simulasi Bayar Otomatis (Demo Skripsi)
const simulatePayment = () => {
    Swal.fire({
        title: 'Simulasi Pembayaran',
        text: "Gunakan fitur ini untuk mendemokan sistem verifikasi otomatis tanpa perlu upload struk.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#0891b2',
        cancelButtonColor: '#475569',
        confirmButtonText: '⚡ Bayar Sekarang',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('orders.simulate_payment', props.order.id), {}, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        text: 'Dana telah dideteksi otomatis oleh sistem.',
                        icon: 'success',
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Bayar Tagihan" />

    <div class="min-h-screen bg-slate-950 py-12 px-4">
        <div class="max-w-3xl mx-auto">
            
            <div class="text-center mb-10">
                <div class="inline-block p-4 rounded-full bg-slate-900 border border-slate-800 mb-4 shadow-lg shadow-cyan-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><rect x="2" y="5" width="20" height="14" rx="2" /><line x1="2" y1="10" x2="22" y2="10" /></svg>
                </div>
                <h1 class="text-3xl font-black text-white">Instruksi Pembayaran</h1>
                <p class="text-slate-400 mt-2">Invoice: <span class="font-mono text-cyan-400 font-bold">{{ order.invoice_number }}</span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-6">
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800">
                        <h3 class="text-slate-400 text-xs font-bold uppercase mb-4 tracking-wider">Rincian Pembelian</h3>
                        
                        <div v-if="order.items && order.items.length > 0">
                            <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center mb-2">
                                <span class="text-white font-medium">{{ item.product_name || item.product?.name }}</span>
                                <span class="text-slate-300">{{ formatRupiah(item.price) }}</span>
                            </div>
                        </div>
                        
                        <div v-else-if="order.product" class="flex justify-between items-center mb-2">
                            <span class="text-white font-medium">{{ order.product.name }}</span>
                            <span class="text-slate-300">{{ formatRupiah(order.product.price) }}</span>
                        </div>

                        <div class="border-t border-slate-800 mt-4 pt-4 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 italic">Kode Unik Verifikasi</span>
                                <span class="text-yellow-500 font-bold">+{{ order.unique_code }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-white font-bold text-lg">Total Bayar</span>
                                <span class="text-cyan-400 font-black text-2xl">{{ formatRupiah(order.total_price) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h4 class="text-white font-bold mb-2 flex items-center gap-2">
                            <span class="text-yellow-500">⚠️</span> Penting
                        </h4>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            Mohon transfer <strong>TEPAT</strong> hingga 3 digit terakhir. Kode unik digunakan oleh sistem kami untuk memverifikasi pembayaran Anda secara otomatis tanpa perlu mengunggah bukti transfer.
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-blue-900 to-slate-900 rounded-2xl p-6 border border-blue-700/50 relative overflow-hidden h-full flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-blue-200 text-xs font-bold uppercase mb-4 tracking-wider">Transfer Bank BCA</h3>
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" class="h-8 bg-white p-1 rounded">
                                <div>
                                    <p class="text-2xl font-mono font-black text-white tracking-widest">123 456 7890</p>
                                    <p class="text-blue-300 text-sm">a.n PT BlueLine Indonesia</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 relative z-10 space-y-4">
                            <button @click="simulatePayment" 
                                class="w-full py-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-black shadow-lg shadow-cyan-900/50 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                                ⚡ BAYAR SEKARANG
                            </button>
                            
                            <p class="text-[10px] text-center text-blue-200/40 uppercase tracking-widest">Verifikasi Otomatis 24/7</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <Link :href="route('my-orders')" class="text-slate-500 hover:text-white text-sm font-bold transition">
                    &larr; Lihat Riwayat Pesanan Saya
                </Link>
            </div>

        </div>
    </div>
</template>