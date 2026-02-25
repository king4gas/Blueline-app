<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    orders: Array
});

// === STATE MODAL RETUR ===
const showReturnModal = ref(false);
const selectedOrder = ref(null);
const imagePreview = ref(null);

const form = useForm({
    reason: '',
    image: null,
    video: null,
});

// Helper Format Rupiah
const formatRupiah = (n) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
};

// Helper Tanggal
const formatDate = (dateString) => {
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

// === LOGIC MODAL RETUR ===
const openReturnModal = (order) => {
    selectedOrder.value = order;
    form.reset();
    imagePreview.value = null;
    showReturnModal.value = true;
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitReturn = () => {
    if (!selectedOrder.value) return;
    form.post(route('orders.return', selectedOrder.value.id), {
        onSuccess: () => {
            showReturnModal.value = false;
            form.reset();
            Swal.fire({
                icon: 'success', title: 'Berhasil!', text: 'Pengajuan retur terkirim.',
                background: '#1e293b', color: '#fff', confirmButtonColor: '#0891b2'
            });
        },
        forceFormData: true
    });
};

// === LOGIC PESANAN SELESAI ===
const completeOrder = (orderId) => {
    Swal.fire({
        title: 'Konfirmasi Pesanan Selesai?',
        text: "Pastikan Anda telah menerima barang dalam kondisi baik. Setelah pesanan diselesaikan, Anda tidak dapat mengajukan pengembalian (retur).",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', // Emerald color
        cancelButtonColor: '#475569',
        confirmButtonText: 'Ya, Pesanan Selesai',
        cancelButtonText: 'Batal',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            // Gunakan router.patch untuk mengubah status menjadi 'finished' (atau sesuai di Controller Anda)
            router.patch(route('orders.complete', orderId), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Terima Kasih!',
                        text: 'Pesanan telah diselesaikan.',
                        icon: 'success',
                        background: '#1e293b',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Riwayat Pesanan" />

    <UserLayout>
        <div class="py-12 bg-slate-950 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="mb-8">
                    <h2 class="font-black text-3xl text-white">Riwayat Pesanan</h2>
                    <p class="text-slate-400">Pantau status pesanan dan layanan Anda.</p>
                </div>

                <div class="space-y-6">
                    
                    <div v-for="order in orders" :key="order.id" class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg transition hover:border-slate-700">
                        
                        <div class="bg-slate-950/50 p-4 border-b border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">No. Invoice</div>
                                <div class="text-white font-mono font-bold">#{{ order.invoice_number || order.id }}</div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right hidden sm:block">
                                    <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tanggal</div>
                                    <div class="text-slate-300 text-sm">{{ formatDate(order.created_at) }}</div>
                                </div>
                                
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border tracking-wider"
                                    :class="{
                                        'bg-yellow-500/20 text-yellow-500 border-yellow-500/50': order.status === 'pending_payment' || order.status === 'pending_verification',
                                        'bg-blue-500/20 text-blue-500 border-blue-500/50': order.status === 'verified',
                                        'bg-cyan-500/20 text-cyan-500 border-cyan-500/50': order.status === 'shipped',
                                        'bg-emerald-500/20 text-emerald-500 border-emerald-500/50': order.status === 'completed',
                                        'bg-purple-500/20 text-purple-400 border-purple-500/50': order.status === 'finished', // Tambahan warna untuk Finished
                                        'bg-red-500/20 text-red-500 border-red-500/50': order.status === 'return_requested' || order.status === 'returned',
                                    }">
                                    {{ 
                                        order.status === 'pending_payment' || order.status === 'pending_verification' ? 'BELUM BAYAR' : 
                                        order.status === 'completed' ? 'DIKIRIM / SELESAI' :
                                        order.status === 'finished' ? 'TRANSAKSI SELESAI' :
                                        order.status.replace('_', ' ') 
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-950 flex flex-col gap-4">
                            
                            <div class="w-full" v-if="order.status === 'pending_payment' || order.status === 'pending_verification'">
                                <div class="bg-blue-900/10 border border-blue-500/30 rounded-xl p-5 relative overflow-hidden">
                                    <h4 class="text-blue-400 font-bold mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        Instruksi Pembayaran Otomatis
                                    </h4>
                                    <p class="text-slate-300 text-sm mb-4">
                                        Transfer <strong class="text-white uppercase">TEPAT</strong> sesuai nominal hingga 3 digit terakhir agar sistem dapat memverifikasi pembayaran Anda otomatis.
                                    </p>
                                    
                                    <div class="bg-slate-950 p-4 rounded-lg border border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 mb-5">
                                        <div>
                                            <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Bank BCA (a.n Blueline Project)</p>
                                            <p class="text-xl font-mono font-bold text-white tracking-wider">8812 3344 5566</p>
                                        </div>
                                        <div class="text-right border-t sm:border-t-0 sm:border-l border-slate-800 pt-3 sm:pt-0 sm:pl-4 w-full sm:w-auto">
                                            <p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Total Bayar</p>
                                            <p class="text-2xl font-black text-cyan-400">{{ formatRupiah(order.total_price) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-3 border-t border-slate-800/50">
                                        <button @click="router.post(route('orders.simulate_payment', order.id))" 
                                                class="w-full sm:w-auto px-5 py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-bold text-sm shadow-lg shadow-purple-900/20 transition flex items-center justify-center gap-2">
                                            ⚡ Simulasi Cek Mutasi
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full" v-else>
                                
                                <div class="mb-4 pb-4 border-b border-slate-800">
                                    <template v-if="order.items && order.items.length > 0">
                                        <div v-for="item in order.items" :key="item.id" class="flex gap-4 mb-3 last:mb-0">
                                            <img :src="item.product?.image || '/placeholder.jpg'" class="w-16 h-16 object-cover rounded-lg bg-slate-800 border border-slate-700">
                                            <div>
                                                <h4 class="text-white font-bold">{{ item.product?.name }}</h4>
                                                <p class="text-slate-400 text-xs">{{ item.product?.type === 'hardware' ? 'Perangkat Keras' : 'Layanan Internet' }}</p>
                                                <p class="text-cyan-400 text-sm mt-1 font-bold">{{ formatRupiah(item.price) }} <span class="text-slate-500 font-normal">x {{ item.quantity }}</span></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                    <div class="text-white font-bold text-lg w-full md:w-auto">
                                        Total: <span class="text-cyan-400">{{ formatRupiah(order.total_price) }}</span>
                                    </div>

                                    <div class="w-full md:w-2/3 flex justify-end gap-3">
                                        
                                        <div v-if="order.status === 'completed' && !order.return_request" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto justify-end">
                                            
                                            <button @click="openReturnModal(order)"
                                                    class="px-4 py-2.5 bg-slate-900 hover:bg-red-600/10 text-slate-300 hover:text-red-400 border border-slate-700 hover:border-red-500/50 rounded-lg transition text-sm font-bold flex items-center justify-center gap-2 w-full sm:w-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                                Ajukan Retur
                                            </button>

                                            <button @click="completeOrder(order.id)"
                                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white border border-emerald-500 rounded-lg transition text-sm font-bold flex items-center justify-center gap-2 w-full sm:w-auto shadow-lg shadow-emerald-900/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Pesanan Diterima
                                            </button>

                                        </div>

                                        <div v-if="order.status === 'finished'" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 text-sm font-bold flex items-center justify-center w-full sm:w-auto">
                                            <span class="mr-2">🎉</span> Pesanan telah diselesaikan.
                                        </div>

                                        <div v-if="order.return_request" class="space-y-3 w-full">
                                            <div v-if="order.return_request.status === 'pending'" class="w-full px-5 py-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl flex items-center gap-4 animate-pulse-slow">
                                                <div class="bg-yellow-500/20 p-2 rounded-full"><span class="text-yellow-500 text-lg">⏳</span></div>
                                                <div>
                                                    <h4 class="font-bold text-yellow-400 text-sm">Menunggu Verifikasi Admin</h4>
                                                    <p class="text-xs text-yellow-200/60 mt-0.5">Bukti foto & alasan Anda sedang dicek.</p>
                                                </div>
                                            </div>
                                            <div v-if="order.return_request.status === 'rejected'" class="w-full px-5 py-3 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-4">
                                                <div class="bg-red-500/20 p-2 rounded-full"><span class="text-red-500 text-lg">❌</span></div>
                                                <div>
                                                    <h4 class="font-bold text-red-500 text-sm">Pengajuan Ditolak</h4>
                                                    <p class="text-xs text-red-300/60 mt-0.5 italic">"{{ order.return_request.admin_note }}"</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div v-if="orders.length === 0" class="text-center py-20 bg-slate-900 border border-slate-800 rounded-2xl">
                        <div class="text-6xl mb-4 opacity-50">📦</div>
                        <h3 class="text-white font-bold text-xl">Belum ada pesanan</h3>
                        <p class="text-slate-400 mb-6">Yuk mulai belanja produk terbaik kami!</p>
                        <a :href="route('products.index')" class="bg-cyan-600 hover:bg-cyan-500 text-white px-6 py-2 rounded-full font-bold transition">Lihat Produk</a>
                    </div>

                </div>
            </div>
        </div>

        <Modal :show="showReturnModal" @close="showReturnModal = false">
            <div class="bg-slate-900 text-white p-6 border border-slate-700 rounded-lg">
                <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold">Ajukan Pengembalian</h3>
                    <button @click="showReturnModal = false" class="text-slate-500 hover:text-white text-xl">✕</button>
                </div>

                <form @submit.prevent="submitReturn" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Jelaskan Masalah / Kerusakan</label>
                        <textarea v-model="form.reason" rows="3" 
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-red-500 transition"
                            placeholder="Contoh: Barang rusak saat diterima..." required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Bukti Foto (Wajib)</label>
                        <input type="file" @change="handleImageUpload" accept="image/*" required
                            class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-800 file:text-red-400 hover:file:bg-slate-700 cursor-pointer border border-slate-700 rounded-xl bg-slate-950">
                        <div v-if="imagePreview" class="mt-3">
                            <img :src="imagePreview" class="h-32 w-auto object-cover rounded-lg border border-slate-700">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="showReturnModal = false" class="px-4 py-2 text-slate-400 hover:text-white font-bold">Batal</button>
                        <button type="submit" :disabled="form.processing" 
                            class="bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl font-bold transition flex items-center gap-2">
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

    </UserLayout>
</template>