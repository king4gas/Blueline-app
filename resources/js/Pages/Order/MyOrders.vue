<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    orders: Array
});

// === STATE ===
const showReturnModal = ref(false);
const selectedOrder = ref(null);
const imagePreview = ref(null);

const form = useForm({
    reason: '',
    image: null,
    video: null,
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// === LOGIC MODAL ===
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
        },
        forceFormData: true
    });
};
</script>

<template>
    <Head title="Riwayat Pesanan" />

    <UserLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="mb-8">
                    <h2 class="font-black text-3xl text-white">Riwayat Pesanan</h2>
                    <p class="text-slate-400">Pantau status pesanan dan pengajuan pengembalian Anda.</p>
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
                                    <div class="text-slate-300 text-sm">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</div>
                                </div>
                                
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border"
                                    :class="{
                                        'bg-yellow-500/20 text-yellow-500 border-yellow-500/50': order.status === 'pending_verification',
                                        'bg-blue-500/20 text-blue-500 border-blue-500/50': order.status === 'verified',
                                        'bg-cyan-500/20 text-cyan-500 border-cyan-500/50': order.status === 'shipped',
                                        'bg-emerald-500/20 text-emerald-500 border-emerald-500/50': order.status === 'completed',
                                        'bg-red-500/20 text-red-500 border-red-500/50': order.status === 'return_requested' || order.status === 'returned',
                                    }">
                                    {{ order.status.replace('_', ' ') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div v-for="item in order.order_items" :key="item.id" class="flex gap-4 mb-4 last:mb-0">
                                <img :src="item.product?.image || '/placeholder.jpg'" class="w-16 h-16 object-cover rounded-lg bg-slate-800 border border-slate-700">
                                <div>
                                    <h4 class="text-white font-bold">{{ item.product?.name }}</h4>
                                    <p class="text-slate-400 text-xs">{{ item.product?.type === 'hardware' ? 'Perangkat Keras' : 'Layanan Internet' }}</p>
                                    <p class="text-cyan-400 text-sm mt-1 font-bold">{{ formatRupiah(item.price) }} <span class="text-slate-500 font-normal">x {{ item.quantity }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-950 border-t border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="text-white font-bold text-lg w-full md:w-auto mb-2 md:mb-0">
                                Total: <span class="text-cyan-400">{{ formatRupiah(order.total_price) }}</span>
                            </div>

                            <div class="w-full md:w-2/3">
                                
                                <div v-if="!order.return_request" class="flex justify-end">
                                    <button v-if="order.status === 'completed'" 
                                            @click="openReturnModal(order)"
                                            class="px-4 py-2 bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white border border-red-600/30 rounded-lg transition text-sm font-bold flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        Ajukan Pengembalian
                                    </button>
                                </div>

                                <div v-else class="space-y-3">
                                    
                                    <div v-if="order.return_request.status === 'pending'" class="w-full px-5 py-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl flex items-center gap-4 animate-pulse-slow">
                                        <div class="bg-yellow-500/20 p-2 rounded-full">
                                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-yellow-400 text-sm">Menunggu Verifikasi Admin</h4>
                                            <p class="text-xs text-yellow-200/60 mt-0.5">Bukti foto & alasan Anda sedang dicek.</p>
                                        </div>
                                    </div>

                                    <div v-if="order.return_request.status === 'approved'" class="relative w-full bg-gradient-to-r from-emerald-900/40 to-slate-900 border border-emerald-500/30 rounded-xl overflow-hidden shadow-lg shadow-emerald-900/10">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                                        
                                        <div class="p-5">
                                            <div class="flex items-start gap-4">
                                                <div class="bg-emerald-500/20 p-3 rounded-xl flex-shrink-0">
                                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-emerald-400 font-bold text-lg">Pengajuan Disetujui! 🎉</h4>
                                                    <p class="text-slate-300 text-sm mt-1 mb-4 leading-relaxed">
                                                        Silakan kirim barang retur ke alamat gudang kami:
                                                    </p>
                                                    
                                                    <div class="bg-slate-950 border-2 border-dashed border-slate-700 rounded-lg p-4 font-mono relative group hover:border-emerald-500/30 transition-colors">
                                                        <div class="absolute top-2 right-2 text-[10px] text-slate-500 uppercase tracking-widest border border-slate-700 px-2 py-0.5 rounded">Warehouse Label</div>
                                                        <p class="text-white font-bold text-sm">PT. BLUELINE INDONESIA (Divisi Retur)</p>
                                                        <p class="text-slate-300 text-sm mt-1">Jl. Jend. Sudirman No. 88, Singaraja, Bali.</p>
                                                        <div class="mt-3 pt-3 border-t border-slate-800">
                                                            <p class="text-yellow-400 text-xs font-bold">⚠️ Tulis "RETUR #{{ order.id }}" di paket.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="order.return_request.status === 'item_received'" class="w-full px-5 py-4 bg-purple-500/10 border border-purple-500/20 rounded-xl flex items-center gap-4">
                                        <div class="bg-purple-500/20 p-2 rounded-full">
                                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-purple-400 text-sm">Barang Sampai di Gudang</h4>
                                            <p class="text-xs text-purple-300/60 mt-0.5">Kami sedang melakukan pengecekan fisik barang.</p>
                                        </div>
                                    </div>

                                    <div v-if="order.return_request.status === 'completed'" class="w-full px-5 py-4 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center gap-4">
                                        <div class="bg-blue-500/20 p-2 rounded-full">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-blue-400 text-sm">Retur Selesai</h4>
                                            <p class="text-xs text-blue-300/60 mt-0.5">Barang pengganti dikirim / Dana dikembalikan.</p>
                                        </div>
                                    </div>

                                    <div v-if="order.return_request.status === 'rejected'" class="w-full px-5 py-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-4">
                                        <div class="bg-red-500/20 p-2 rounded-full">
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-red-500 text-sm">Pengajuan Ditolak</h4>
                                            <p class="text-xs text-red-300/60 mt-0.5 italic">"{{ order.return_request.admin_note }}"</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div v-if="orders.length === 0" class="text-center py-20 bg-slate-900 border border-slate-800 rounded-2xl">
                        <div class="text-6xl mb-4 opacity-50">📦</div>
                        <h3 class="text-white font-bold text-xl">Belum ada pesanan</h3>
                        <p class="text-slate-400 mb-6">Yuk mulai belanja layanan internet terbaik!</p>
                        <a :href="route('products.index')" class="bg-cyan-600 hover:bg-cyan-500 text-white px-6 py-2 rounded-full font-bold transition">Lihat Produk</a>
                    </div>

                </div>
            </div>
        </div>

        <Modal :show="showReturnModal" @close="showReturnModal = false">
            <div class="bg-slate-900 text-white p-6 border border-slate-700 rounded-lg">
                <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold">Ajukan Pengembalian</h3>
                    <button @click="showReturnModal = false" class="text-slate-500 hover:text-white">✕</button>
                </div>

                <form @submit.prevent="submitReturn" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Jelaskan Masalah / Kerusakan</label>
                        <textarea v-model="form.reason" rows="3" 
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-red-500 transition"
                            placeholder="Contoh: Router mati total saat dinyalakan..." required></textarea>
                        <div v-if="form.errors.reason" class="text-red-400 text-xs mt-1">{{ form.errors.reason }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Bukti Foto (Wajib)</label>
                        <input type="file" @change="handleImageUpload" accept="image/*" required
                            class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-800 file:text-red-400 hover:file:bg-slate-700 cursor-pointer border border-slate-700 rounded-xl bg-slate-950">
                        <div v-if="imagePreview" class="mt-3">
                            <p class="text-[10px] text-slate-500 mb-1 uppercase">Preview Foto:</p>
                            <img :src="imagePreview" class="h-32 w-auto object-cover rounded-lg border border-slate-700">
                        </div>
                        <div v-if="form.errors.image" class="text-red-400 text-xs mt-1">{{ form.errors.image }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Bukti Video (Opsional)</label>
                        <input type="file" @input="form.video = $event.target.files[0]" accept="video/*"
                            class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer border border-slate-700 rounded-xl bg-slate-950">
                        <p class="text-xs text-slate-500 mt-1">Maksimal 20MB.</p>
                        <div v-if="form.errors.video" class="text-red-400 text-xs mt-1">{{ form.errors.video }}</div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="showReturnModal = false" class="px-4 py-2 text-slate-400 hover:text-white">Batal</button>
                        <button type="submit" :disabled="form.processing" 
                            class="bg-red-600 hover:bg-red-500 text-white px-6 py-2 rounded-xl font-bold transition flex items-center gap-2">
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Kirim Pengajuan</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

    </UserLayout>
</template>