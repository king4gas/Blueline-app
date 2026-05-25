<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';
import { computed } from 'vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
    subscription: Object 
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// PERBAIKAN UTAMA: Menggunakan Math.round(Number()) agar string desimal "500000.00" tetap dibaca 500000
const basePrice = computed(() => {
    return Math.round(Number(props.subscription?.price) || 0);
});

const penaltyFee = computed(() => {
    return Math.round(Number(props.subscription?.penalty_fee) || 0);
});

const isTerminated = computed(() => props.subscription?.is_terminated || false);

// Sekarang dijamin penjumlahan matematika antar angka murni, bukan teks
const totalBill = computed(() => basePrice.value + penaltyFee.value);

const renewSubscription = () => {
    if (!props.subscription) return;

    let textMessage = `Tagihan pokok sebesar ${formatRupiah(basePrice.value)} akan dibuat.`;
    
    // Tambahan teks jika ada denda
    if (penaltyFee.value > 0) {
        textMessage = `Tagihan pokok ${formatRupiah(basePrice.value)} + Denda Keterlambatan ${formatRupiah(penaltyFee.value)}.\nTotal yang harus dibayar: ${formatRupiah(totalBill.value)}. Lanjutkan?`;
    } else {
        textMessage += " Lanjutkan?";
    }

    Swal.fire({
        title: isTerminated.value ? 'Aktifkan Kembali Layanan?' : 'Perpanjang Layanan?',
        text: textMessage,
        icon: isTerminated.value ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar Sekarang',
        cancelButtonText: 'Batal',
        background: '#1e293b', 
        color: '#fff', 
        confirmButtonColor: isTerminated.value ? '#10b981' : '#0891b2', 
        cancelButtonColor: '#475569', 
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('subscription.renew'), { 
                product_id: props.subscription.product.id,
                expected_total: totalBill.value 
            });
        }
    });
};
</script>

<template>
    <Head title="Layanan Saya" />

    <div class="min-h-screen bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-cyan-500/10 rounded-xl border border-cyan-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Layanan Saya</h1>
                    <p class="text-slate-400 text-sm">Informasi paket internet aktif Anda.</p>
                </div>
            </div>

            <div v-if="subscription" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                
                <div v-if="isTerminated" class="bg-red-500/10 border border-red-500/50 rounded-2xl p-4 flex items-start gap-4 animate-pulse">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <h3 class="text-red-400 font-bold text-lg">Layanan Diputus Sementara</h3>
                        <p class="text-red-300/80 text-sm">Layanan internet Anda telah diisolasi karena keterlambatan pembayaran lebih dari 2 bulan. Segera lunasi tunggakan beserta denda untuk mengaktifkan kembali layanan.</p>
                    </div>
                </div>
                <div v-else-if="penaltyFee > 0" class="bg-yellow-500/10 border border-yellow-500/50 rounded-2xl p-4 flex items-start gap-4">
                    <span class="text-2xl">⏳</span>
                    <div>
                        <h3 class="text-yellow-400 font-bold text-lg">Denda Keterlambatan</h3>
                        <p class="text-yellow-200/80 text-sm">Anda telah melewati batas waktu pembayaran. Denda sebesar 5% telah ditambahkan ke tagihan Anda.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-[2rem] p-8 relative overflow-hidden shadow-2xl"
                     :class="{'border-red-500/30': isTerminated, 'border-yellow-500/30': penaltyFee > 0 && !isTerminated}">
                    
                    <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 relative z-10">
                        <div class="w-full md:w-1/2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 border border-slate-700 text-white text-xs font-bold uppercase tracking-wider mb-3 shadow-sm">
                                <span class="w-2 h-2 rounded-full" 
                                      :class="isTerminated ? 'bg-red-500' : (subscription.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-yellow-500')"></span>
                                {{ isTerminated ? 'ISOLASI / PUTUS' : (subscription.is_active ? 'Aktif' : 'Menunggu Pembayaran') }}
                            </div>
                            <h2 class="text-3xl font-black text-white mb-4">{{ subscription.product.name }}</h2>
                            
                            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-400">Harga Paket / Bulan</span>
                                    <span class="text-white">{{ formatRupiah(basePrice) }}</span>
                                </div>
                                <div v-if="penaltyFee > 0" class="flex justify-between text-sm text-red-400 border-b border-slate-800 pb-2">
                                    <span>Denda Keterlambatan (5%)</span>
                                    <span>+ {{ formatRupiah(penaltyFee) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-black pt-2" :class="penaltyFee > 0 ? 'text-red-400' : 'text-cyan-400'">
                                    <span>Total Tagihan</span>
                                    <span>{{ formatRupiah(totalBill) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-auto mt-4 md:mt-0 flex flex-col items-end justify-center h-full">
                            <button 
                                @click="renewSubscription" 
                                class="w-full md:w-auto px-8 py-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold shadow-lg shadow-cyan-900/50 transition transform active:scale-95 flex items-center justify-center gap-2"
                                :class="{'bg-red-600 hover:bg-red-500 shadow-red-900/50': penaltyFee > 0}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                                {{ isTerminated ? 'Lunasi & Aktifkan Kembali' : (penaltyFee > 0 ? 'Bayar Tagihan & Denda' : 'Perpanjang Paket') }}
                            </button>
                            <p class="text-slate-500 text-[10px] text-center mt-3 uppercase tracking-wide w-full">Pembayaran Otomatis</p>
                        </div>
                    </div>

                    <hr class="border-slate-800 my-8">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                            <p class="text-slate-500 text-[10px] font-bold uppercase mb-1 tracking-wider">Mulai Tanggal</p>
                            <p class="text-white font-bold">{{ subscription.start_date }}</p>
                        </div>
                        <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                            <p class="text-slate-500 text-[10px] font-bold uppercase mb-1 tracking-wider">Jatuh Tempo</p>
                            <p class="font-bold" :class="isTerminated || penaltyFee > 0 ? 'text-red-400' : 'text-white'">{{ subscription.expired_date }}</p>
                        </div>
                         <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                            <p class="text-slate-500 text-[10px] font-bold uppercase mb-1 tracking-wider">Status Pembayaran</p>
                            <p class="font-bold flex items-center gap-2" :class="isTerminated || penaltyFee > 0 ? 'text-red-400' : 'text-emerald-400'">
                                <svg v-if="isTerminated || penaltyFee > 0" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                {{ isTerminated || penaltyFee > 0 ? 'Menunggak' : 'Lunas' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-slate-400 text-sm font-bold">Masa Aktif</span>
                            <span class="text-white font-bold" :class="subscription.days_remaining < 3 ? 'text-red-400' : ''">
                                {{ subscription.days_remaining > 0 ? `Sisa ${subscription.days_remaining} Hari` : 'Sudah Habis' }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-4 overflow-hidden border border-slate-700">
                            <div class="h-full rounded-full transition-all duration-1000 ease-out relative"
                                 :class="subscription.days_remaining < 5 ? 'bg-red-500' : 'bg-gradient-to-r from-cyan-500 to-blue-500'"
                                 :style="{ width: `${100 - subscription.progress}%` }">
                                 <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite] skew-x-12"></div>
                            </div>
                        </div>
                        <p class="text-slate-500 text-xs mt-3">*Sistem akan menambahkan denda keterlambatan 5% jika melewati tanggal jatuh tempo.</p>
                    </div>
                </div>

            </div>

            <div v-else class="text-center py-24 bg-slate-900 rounded-[2rem] border border-slate-800 border-dashed animate-in zoom-in duration-300">
                <div class="w-24 h-24 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <span class="text-5xl">📡</span>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Belum ada layanan aktif</h2>
                <p class="text-slate-400 mb-8 max-w-md mx-auto leading-relaxed">Anda belum berlangganan layanan internet BlueLine. Yuk pilih paket terbaik untuk kebutuhan digitalmu sekarang!</p>
                <Link :href="route('products.index')" class="px-8 py-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold transition shadow-lg shadow-cyan-900/50 inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Pilih Paket Internet
                </Link>
            </div>

        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
  0% { transform: translateX(-150%) skewX(-12deg); }
  100% { transform: translateX(150%) skewX(-12deg); }
}
</style>