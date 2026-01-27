<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    orders: Array,
    filterStatus: String
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

// === STATUS BADGE HELPER ===
const getStatusBadge = (status) => {
    switch(status) {
        case 'pending_payment': return 'bg-slate-800 text-slate-400 border-slate-700';
        case 'pending_verification': return 'bg-yellow-900/30 text-yellow-400 border-yellow-700';
        case 'verified': return 'bg-blue-900/30 text-blue-400 border-blue-700';
        case 'shipped': return 'bg-cyan-900/30 text-cyan-400 border-cyan-700';
        case 'completed': return 'bg-emerald-900/30 text-emerald-400 border-emerald-700';
        case 'return_requested': return 'bg-red-900/30 text-red-400 border-red-500 animate-pulse'; // Kedip-kedip biar notice
        case 'returned': return 'bg-red-900/30 text-red-400 border-red-700 line-through';
        case 'return_rejected': return 'bg-slate-800 text-slate-400 border-slate-700';
        default: return 'bg-slate-800 text-white';
    }
};

// === LOGIC MODAL CEK RETUR ===
const showReturnModal = ref(false);
const selectedOrder = ref(null);
const rejectionReason = ref('');

const openReturnModal = (order) => {
    selectedOrder.value = order;
    rejectionReason.value = '';
    showReturnModal.value = true;
};

const handleReturnDecision = (decision) => {
    if (decision === 'reject') {
        // Jika tolak, wajib isi alasan
        Swal.fire({
            title: 'Tolak Pengembalian?',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Contoh: Bukti tidak valid, kerusakan akibat kurir...',
            inputAttributes: { autocapitalize: 'off' },
            showCancelButton: true,
            confirmButtonText: 'Tolak Retur',
            confirmButtonColor: '#d33',
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDecision('reject', result.value);
            }
        });
    } else {
        // Jika terima
        Swal.fire({
            title: 'Setujui Retur?',
            text: "Status pesanan akan berubah menjadi 'Dikembalikan'.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            confirmButtonColor: '#0891b2',
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDecision('approve', null);
            }
        });
    }
};

const submitDecision = (decision, reason) => {
    router.post(route('admin.orders.verify_return', selectedOrder.value.id), {
        decision: decision,
        rejection_reason: reason
    }, {
        onSuccess: () => {
            showReturnModal.value = false;
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Keputusan retur telah disimpan.', background: '#1e293b', color: '#fff' });
        }
    });
};

// === UPDATE STATUS NORMAL ===
const updateStatus = (order, newStatus) => {
    router.patch(route('admin.orders.update', order.id), { status: newStatus });
};
</script>

<template>
    <Head title="Kelola Pesanan" />

    <div class="py-12 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h2 class="font-black text-3xl text-white">Kelola Pesanan</h2>
                    <p class="text-slate-400">Pantau transaksi dan permintaan retur.</p>
                </div>
            </div>

            <div class="flex overflow-x-auto gap-2 mb-6 border-b border-slate-800 pb-2">
                <Link :href="route('admin.orders.index', { status: 'all' })" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap"
                    :class="filterStatus === 'all' ? 'bg-cyan-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    Semua
                </Link>
                <Link :href="route('admin.orders.index', { status: 'pending_verification' })" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap"
                    :class="filterStatus === 'pending_verification' ? 'bg-yellow-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    Perlu Verifikasi
                </Link>
                <Link :href="route('admin.orders.index', { status: 'verified' })" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap"
                    :class="filterStatus === 'verified' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    Siap Kirim
                </Link>
                <Link :href="route('admin.orders.index', { status: 'return_requested' })" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap flex items-center gap-2"
                    :class="filterStatus === 'return_requested' ? 'bg-red-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    ⚠️ Permintaan Retur
                </Link>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Invoice / Customer</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-mono text-cyan-400 font-bold">{{ order.invoice_number }}</div>
                                    <div class="text-white font-bold mt-1">{{ order.user.name }}</div>
                                    <div class="text-xs text-slate-500">{{ order.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-white">
                                    {{ formatRupiah(order.total_price) }}
                                    <div class="text-xs text-slate-500 font-normal">{{ order.items.length }} Item</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusBadge(order.status)">
                                        {{ order.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    
                                    <button v-if="order.status === 'return_requested'" 
                                            @click="openReturnModal(order)"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg font-bold text-xs shadow-lg shadow-red-900/50 transition animate-pulse">
                                        🔍 Cek Pengajuan
                                    </button>

                                    <div v-else-if="order.status === 'pending_verification'" class="flex gap-2 justify-center">
                                        <a :href="'/storage/' + order.payment_proof" target="_blank" class="px-3 py-2 bg-slate-800 text-slate-300 rounded-lg text-xs hover:bg-slate-700">Bukti</a>
                                        <button @click="updateStatus(order, 'verified')" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-500">Terima</button>
                                    </div>

                                    <button v-else-if="order.status === 'verified'" @click="updateStatus(order, 'shipped')" class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-xs font-bold hover:bg-cyan-500">
                                        Kirim Barang 📦
                                    </button>

                                    <button v-else-if="order.status === 'shipped'" @click="updateStatus(order, 'completed')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-500">
                                        Selesai ✅
                                    </button>

                                    <span v-else class="text-slate-600 text-xs italic">Tidak ada aksi</span>
                                </td>
                            </tr>
                            <tr v-if="orders.length === 0">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada pesanan ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showReturnModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity" @click="showReturnModal = false"></div>

            <div class="bg-slate-900 w-full max-w-2xl rounded-2xl shadow-2xl relative z-10 border border-red-500/30 overflow-hidden animate-in zoom-in duration-200">
                
                <div class="bg-red-900/20 p-6 border-b border-red-500/20 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-red-400">Verifikasi Pengembalian</h3>
                        <p class="text-red-200/50 text-sm mt-1">Invoice: {{ selectedOrder?.invoice_number }}</p>
                    </div>
                    <button @click="showReturnModal = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Foto Bukti Kerusakan</p>
                        <div class="aspect-square bg-slate-950 rounded-xl border border-slate-800 overflow-hidden relative group">
                            <a :href="'/storage/' + selectedOrder?.return_evidence" target="_blank">
                                <img :src="'/storage/' + selectedOrder?.return_evidence" class="w-full h-full object-cover hover:scale-110 transition duration-500 cursor-zoom-in">
                            </a>
                        </div>
                        <p class="text-center text-xs text-slate-500 mt-2">(Klik foto untuk memperbesar)</p>
                    </div>

                    <div class="flex flex-col">
                        <div class="mb-6">
                            <p class="text-slate-400 text-xs font-bold uppercase mb-2">Alasan Pengembalian</p>
                            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-white italic">
                                "{{ selectedOrder?.return_reason }}"
                            </div>
                        </div>

                        <div class="mt-auto space-y-3">
                            <p class="text-slate-400 text-xs font-bold uppercase">Keputusan Admin</p>
                            
                            <button @click="handleReturnDecision('approve')" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-900/50 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Terima Pengembalian
                            </button>
                            
                            <button @click="handleReturnDecision('reject')" class="w-full py-3 bg-red-600 hover:bg-red-500 text-white rounded-xl font-bold shadow-lg shadow-red-900/50 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                Tolak Pengembalian
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>