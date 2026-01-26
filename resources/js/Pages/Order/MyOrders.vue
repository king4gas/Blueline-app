<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

defineOptions({ layout: UserLayout });

const props = defineProps({ orders: Array });

// === LOGIC PENGEMBALIAN BARANG ===
const showReturnModal = ref(false);
const selectedOrder = ref(null);
const evidencePreview = ref(null);

const returnForm = useForm({
    reason: '',
    evidence: null
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', minimumFractionDigits: 0}).format(n);
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute:'2-digit' });

// Status Color Helper
const getStatusClass = (status) => {
    switch(status) {
        case 'verified': case 'completed': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
        case 'shipped': return 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20';
        case 'cancelled': return 'bg-red-500/10 text-red-400 border-red-500/20';
        default: return 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'pending_verification': 'Menunggu Verifikasi',
        'verified': 'Pembayaran Diterima',
        'shipped': 'Sedang Dikirim',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };
    return labels[status] || status;
};

// Buka Modal Return
const openReturnModal = (order) => {
    selectedOrder.value = order;
    showReturnModal.value = true;
    returnForm.reset();
    evidencePreview.value = null;
};

// Handle File Upload
const handleEvidenceUpload = (e) => {
    const file = e.target.files[0];
    returnForm.evidence = file;
    if (file) evidencePreview.value = URL.createObjectURL(file);
};

// Submit Pengajuan
const submitReturn = () => {
    if (!selectedOrder.value) return;

    returnForm.post(route('orders.return', selectedOrder.value.id), {
        onSuccess: () => {
            showReturnModal.value = false;
            Swal.fire({
                title: 'Berhasil',
                text: 'Pengajuan retur telah dikirim.',
                icon: 'success',
                background: '#1e293b',
                color: '#fff'
            });
        },
        onError: () => {
            Swal.fire({
                title: 'Gagal',
                text: 'Periksa kembali data Anda.',
                icon: 'error',
                background: '#1e293b',
                color: '#fff'
            });
        }
    });
};

// Fitur Beli Lagi
const buyAgain = (order) => {
    if (order.items.length > 0) {
        router.post(route('cart.store'), { product_id: order.items[0].product_id });
    }
};
</script>

<template>
    <Head title="Pesanan Saya" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-screen text-slate-100">
        
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-cyan-500/10 rounded-xl border border-cyan-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Riwayat Pesanan</h1>
                <p class="text-slate-400 text-sm">Pantau status pengiriman dan riwayat transaksi Anda.</p>
            </div>
        </div>

        <div v-if="orders.length === 0" class="text-center py-20 bg-slate-900/50 rounded-3xl border border-slate-800 border-dashed">
            <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">🛍️</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Belum ada pesanan</h3>
            <p class="text-slate-500 mb-6">Yuk mulai belanja produk internet terbaik kami.</p>
            <Link :href="route('products.index')" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold transition shadow-lg shadow-cyan-900/50">
                Mulai Belanja
            </Link>
        </div>

        <div v-else class="space-y-6">
            <div v-for="order in orders" :key="order.id" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-700 transition duration-300">
                
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:bg-cyan-500/10 transition"></div>

                <div class="flex flex-col sm:flex-row justify-between sm:items-center border-b border-slate-800 pb-4 mb-4 gap-4 relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Invoice</span>
                            <span class="font-mono text-cyan-400 font-bold bg-cyan-950/30 px-2 py-0.5 rounded text-sm border border-cyan-500/20 select-all">{{ order.invoice_number }}</span>
                        </div>
                        <div class="text-xs text-slate-400">{{ formatDate(order.created_at) }}</div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase border backdrop-blur-sm"
                            :class="getStatusClass(order.status)">
                            {{ getStatusLabel(order.status) }}
                        </span>
                        <div class="text-right pl-4 border-l border-slate-800">
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Total</div>
                            <div class="font-bold text-white text-lg">{{ formatRupiah(order.total_price) }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-6 relative z-10">
                    <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4 bg-slate-950/50 p-3 rounded-xl border border-slate-800/50">
                        <div class="w-14 h-14 bg-slate-800 rounded-lg overflow-hidden flex-shrink-0 border border-slate-700">
                            <img v-if="item.product" :src="item.product.image" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center text-xs text-slate-500">N/A</div>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-sm">{{ item.product_name || 'Produk dihapus' }}</h4>
                            <p class="text-xs text-slate-400 mt-1">
                                <span class="text-cyan-400 font-bold">{{ item.quantity }}x</span> 
                                {{ formatRupiah(item.price) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-slate-800 relative z-10">
                    
                    <button @click="buyAgain(order)" class="px-5 py-2.5 rounded-xl border border-cyan-500/30 text-cyan-400 font-bold text-sm hover:bg-cyan-500/10 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/><line x1="21" y1="5" x2="9" y2="17"/><line x1="15" y1="5" x2="21" y2="5"/><line x1="21" y1="5" x2="21" y2="11"/></svg>
                        Beli Lagi
                    </button>

                    <button 
                        v-if="['verified', 'shipped', 'completed'].includes(order.status)"
                        @click="openReturnModal(order)" 
                        class="px-5 py-2.5 rounded-xl border border-red-500/30 text-red-400 font-bold text-sm hover:bg-red-500/10 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                        Ajukan Pengembalian
                    </button>

                </div>
            </div>
        </div>

        <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showReturnModal = false"></div>
            
            <div class="bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl relative z-10 overflow-hidden animate-in fade-in zoom-in duration-200 border border-slate-700">
                <div class="bg-red-900/20 p-6 border-b border-red-500/20 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-red-400 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 2v6h6M2.66 15.57a10 10 0 1 0 .57-8.38"/></svg>
                            Pengembalian Barang
                        </h3>
                        <p class="text-red-300/70 text-xs mt-1 font-mono">INV: {{ selectedOrder?.invoice_number }}</p>
                    </div>
                    <button @click="showReturnModal = false" class="text-slate-400 hover:text-white transition">✕</button>
                </div>
                
                <form @submit.prevent="submitReturn" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-2">Alasan Pengembalian</label>
                        <div class="relative">
                            <select v-model="returnForm.reason" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl focus:ring-red-500 focus:border-red-500 text-sm p-3 appearance-none">
                                <option value="" disabled>Pilih Alasan Masalah</option>
                                <option value="Barang Rusak / Cacat">Barang Rusak / Cacat</option>
                                <option value="Barang Tidak Sesuai Pesanan">Barang Tidak Sesuai Pesanan</option>
                                <option value="Kelengkapan Kurang">Kelengkapan Kurang</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="absolute right-3 top-3.5 pointer-events-none text-slate-500">▼</div>
                        </div>
                        <div v-if="returnForm.errors.reason" class="text-red-500 text-xs mt-1">{{ returnForm.errors.reason }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-2">Foto Bukti (Wajib)</label>
                        <div class="flex items-start gap-4 p-4 bg-slate-950 rounded-xl border border-slate-800 border-dashed">
                            <div class="flex-1">
                                <input type="file" @change="handleEvidenceUpload" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-red-500/10 file:text-red-400 hover:file:bg-red-500/20 cursor-pointer">
                                <p class="text-xs text-slate-500 mt-2">Upload foto barang yang bermasalah.</p>
                            </div>
                            <div v-if="evidencePreview" class="w-16 h-16 rounded-lg border border-slate-700 overflow-hidden bg-slate-800">
                                <img :src="evidencePreview" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div v-if="returnForm.errors.evidence" class="text-red-500 text-xs mt-1">{{ returnForm.errors.evidence }}</div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="showReturnModal = false" class="px-5 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl font-bold text-sm transition">Batal</button>
                        <button type="submit" :disabled="returnForm.processing" class="px-6 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-500 transition shadow-lg shadow-red-900/50 disabled:opacity-70 flex items-center gap-2">
                            <span v-if="returnForm.processing" class="animate-pulse">Mengirim...</span>
                            <span v-else>Kirim Pengajuan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>