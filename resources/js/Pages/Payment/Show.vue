<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
    order: Object
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// Form Upload
const form = useForm({
    payment_proof: null
});

const filePreview = ref(null);

const handleFile = (e) => {
    const file = e.target.files[0];
    form.payment_proof = file;
    if(file) filePreview.value = URL.createObjectURL(file);
};

const submitPayment = () => {
    form.post(route('payment.upload', props.order.id), {
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Bukti pembayaran telah dikirim. Layanan akan aktif setelah diverifikasi admin.',
                icon: 'success',
                background: '#1e293b',
                color: '#fff'
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
                <h1 class="text-3xl font-black text-white">Selesaikan Pembayaran</h1>
                <p class="text-slate-400 mt-2">Invoice: <span class="font-mono text-cyan-400 font-bold">{{ order.invoice_number }}</span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-6">
                    <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800">
                        <h3 class="text-slate-400 text-xs font-bold uppercase mb-4 tracking-wider">Rincian Layanan</h3>
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center mb-2">
                            <span class="text-white font-medium">{{ item.product_name }}</span>
                            <span class="text-slate-300">{{ formatRupiah(item.price) }}</span>
                        </div>
                        <div class="border-t border-slate-800 mt-4 pt-4 flex justify-between items-center">
                            <span class="text-white font-bold text-lg">Total Tagihan</span>
                            <span class="text-cyan-400 font-black text-2xl">{{ formatRupiah(order.total_price) }}</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-900 to-slate-900 rounded-2xl p-6 border border-blue-700/50 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                        
                        <h3 class="text-blue-200 text-xs font-bold uppercase mb-4 tracking-wider relative z-10">Transfer ke Rekening BCA</h3>
                        <div class="flex items-center gap-4 relative z-10">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" class="h-8 bg-white p-1 rounded">
                            <div>
                                <p class="text-2xl font-mono font-black text-white tracking-widest">123 456 7890</p>
                                <p class="text-blue-300 text-sm">a.n PT BlueLine Indonesia</p>
                            </div>
                        </div>
                        <p class="text-xs text-blue-200/60 mt-4 relative z-10">*Mohon transfer sesuai nominal hingga digit terakhir.</p>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 flex flex-col h-full">
                    <h3 class="text-white font-bold text-lg mb-1">Konfirmasi Pembayaran</h3>
                    <p class="text-slate-400 text-sm mb-6">Upload bukti transfer agar layanan segera aktif.</p>

                    <form @submit.prevent="submitPayment" class="flex flex-col flex-grow">
                        <div class="flex-grow flex flex-col items-center justify-center border-2 border-dashed border-slate-700 rounded-xl bg-slate-950/50 hover:bg-slate-950 hover:border-cyan-500/50 transition cursor-pointer relative group overflow-hidden">
                            
                            <input type="file" @change="handleFile" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                            
                            <img v-if="filePreview" :src="filePreview" class="absolute inset-0 w-full h-full object-contain z-10 bg-slate-950 p-2">
                            
                            <div v-if="!filePreview" class="text-center p-6">
                                <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-cyan-900/30 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-cyan-400"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <p class="text-slate-300 font-bold text-sm">Klik untuk upload bukti</p>
                                <p class="text-slate-500 text-xs mt-1">Format: JPG, PNG (Max 2MB)</p>
                            </div>
                        </div>
                        <div v-if="form.errors.payment_proof" class="text-red-500 text-xs mt-2 text-center">{{ form.errors.payment_proof }}</div>

                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.payment_proof"
                            class="mt-6 w-full py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold shadow-lg shadow-cyan-900/50 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Bukti Pembayaran &rarr;</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-8">
                <Link :href="route('subscription.index')" class="text-slate-500 hover:text-white text-sm font-bold transition">
                    &larr; Kembali ke Layanan Saya
                </Link>
            </div>

        </div>
    </div>
</template>