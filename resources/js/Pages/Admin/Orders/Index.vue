<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
        case 'return_requested': return 'bg-red-900/30 text-red-400 border-red-500 animate-pulse';
        case 'returned': return 'bg-purple-900/30 text-purple-400 border-purple-700'; 
        case 'return_rejected': return 'bg-slate-800 text-slate-400 border-slate-700 line-through';
        default: return 'bg-slate-800 text-white';
    }
};

// === LOGIC MODAL RETUR SESUAI FLOWCHART ===
const showReturnModal = ref(false);
const selectedOrder = ref(null);

const openReturnModal = (order) => {
    selectedOrder.value = order;
    showReturnModal.value = true;
};

// Logic Tombol Bertahap
const handleReturnProcess = (action) => {
    if (!selectedOrder.value) return;

    // 1. TAHAP AWAL: Admin Memutuskan (Approve/Reject)
    if (action === 'approve') {
        Swal.fire({
            title: 'Terima Pengajuan?',
            text: "User akan diinstruksikan untuk mengirim barang ke gudang.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terima',
            confirmButtonColor: '#059669',
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) submitDecision('approved', null);
        });
    } 
    else if (action === 'reject') {
        Swal.fire({
            title: 'Tolak Pengajuan?',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Jelaskan kenapa ditolak...',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            confirmButtonColor: '#d33',
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) submitDecision('rejected', result.value);
        });
    }
    // 2. TAHAP KEDUA: Barang Sampai di Gudang (Item Received)
    else if (action === 'item_received') {
        Swal.fire({
            title: 'Barang Sudah Sampai?',
            text: "Pastikan Anda sudah memegang fisik barang retur dari user.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Barang Diterima',
            confirmButtonColor: '#7c3aed', // Purple
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) submitDecision('item_received', null);
        });
    }
    // 3. TAHAP AKHIR: Selesaikan Masalah (Completed)
    else if (action === 'complete') {
        Swal.fire({
            title: 'Selesaikan Kasus?',
            text: "Klik ini jika barang pengganti sudah dikirim atau dana sudah direfund.",
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Selesai & Tutup Kasus',
            confirmButtonColor: '#059669',
            background: '#1e293b', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) submitDecision('completed', null);
        });
    }
};

const submitDecision = (status, note) => {
    router.post(route('admin.orders.verify_return', selectedOrder.value.id), {
        status: status,
        admin_note: note
    }, {
        onSuccess: () => {
            showReturnModal.value = false;
            Swal.fire({ icon: 'success', title: 'Status Diperbarui', background: '#1e293b', color: '#fff', timer: 1500, showConfirmButton: false });
        }
    });
};

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
                    <p class="text-slate-400">Pantau transaksi dan progress retur barang.</p>
                </div>
            </div>

            <div class="flex overflow-x-auto gap-2 mb-6 border-b border-slate-800 pb-2">
                <Link :href="route('admin.orders.index')" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap"
                    :class="!filterStatus ? 'bg-cyan-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    Semua
                </Link>
                <Link :href="route('admin.orders.index', { status: 'return_requested' })" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition whitespace-nowrap flex items-center gap-2"
                    :class="filterStatus === 'return_requested' ? 'bg-red-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-900'">
                    ⚠️ Proses Retur
                </Link>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-950 text-slate-200 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Status Retur</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 font-mono text-cyan-400 font-bold">#{{ order.invoice_number || order.id }}</td>
                                <td class="px-6 py-4 text-white font-bold">{{ order.user.name }}</td>
                                <td class="px-6 py-4">
                                    <div v-if="order.return_request">
                                        <span v-if="order.return_request.status === 'pending'" class="px-3 py-1 bg-yellow-500/20 text-yellow-500 border border-yellow-500/50 rounded-full text-xs font-bold animate-pulse">
                                            ⏳ Menunggu Verifikasi
                                        </span>
                                        <span v-else-if="order.return_request.status === 'approved'" class="px-3 py-1 bg-blue-500/20 text-blue-500 border border-blue-500/50 rounded-full text-xs font-bold">
                                            🚚 Tunggu Barang User
                                        </span>
                                        <span v-else-if="order.return_request.status === 'item_received'" class="px-3 py-1 bg-purple-500/20 text-purple-500 border border-purple-500/50 rounded-full text-xs font-bold">
                                            📦 Barang Diterima
                                        </span>
                                        <span v-else-if="order.return_request.status === 'completed'" class="px-3 py-1 bg-emerald-500/20 text-emerald-500 border border-emerald-500/50 rounded-full text-xs font-bold">
                                            ✅ Retur Selesai
                                        </span>
                                        <span v-else class="px-3 py-1 bg-red-500/20 text-red-500 border border-red-500/50 rounded-full text-xs font-bold">
                                            ❌ Ditolak
                                        </span>
                                    </div>
                                    <span v-else class="text-slate-600">-</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button v-if="order.return_request && order.return_request.status !== 'completed' && order.return_request.status !== 'rejected'" 
                                            @click="openReturnModal(order)"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg font-bold text-xs shadow-lg shadow-red-900/50 transition flex items-center gap-2 mx-auto">
                                        ⚙️ Proses Retur
                                    </button>
                                    
                                    <div v-else-if="!order.return_request && order.status === 'pending_verification'">
                                        <button @click="updateStatus(order, 'verified')" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-500">Terima Order</button>
                                    </div>
                                    
                                    <span v-else class="text-slate-600 text-xs italic">Selesai</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showReturnModal && selectedOrder" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity" @click="showReturnModal = false"></div>

            <div class="bg-slate-900 w-full max-w-4xl rounded-2xl shadow-2xl relative z-10 border border-slate-700 overflow-hidden animate-in zoom-in duration-200">
                
                <div class="bg-slate-800 p-6 border-b border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white">Proses Retur Barang</h3>
                        <p class="text-slate-400 text-sm mt-1">Status Saat Ini: <span class="text-cyan-400 font-bold uppercase">{{ selectedOrder.return_request.status.replace('_', ' ') }}</span></p>
                    </div>
                    <button @click="showReturnModal = false" class="text-slate-400 hover:text-white text-2xl">✕</button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Bukti Dari User</p>
                        <div class="aspect-video bg-black rounded-xl border border-slate-700 overflow-hidden relative group">
                            <a v-if="selectedOrder.return_request?.image" :href="selectedOrder.return_request.image" target="_blank">
                                <img :src="selectedOrder.return_request.image" class="w-full h-full object-contain">
                            </a>
                        </div>
                        <div class="mt-4 bg-slate-950 p-4 rounded-xl border border-slate-800">
                            <p class="text-xs text-slate-500 font-bold uppercase mb-1">Keluhan User:</p>
                            <p class="text-white italic text-sm">"{{ selectedOrder.return_request?.reason }}"</p>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center bg-slate-950/50 p-6 rounded-xl border border-slate-800">
                        
                        <div v-if="selectedOrder.return_request.status === 'pending'" class="space-y-4">
                            <div class="text-center mb-6">
                                <div class="bg-yellow-500/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl">🔍</span>
                                </div>
                                <h4 class="text-white font-bold">Verifikasi Pengajuan</h4>
                                <p class="text-slate-400 text-sm">Cek bukti foto di samping. Apakah valid?</p>
                            </div>
                            <button @click="handleReturnProcess('approve')" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold">
                                ✅ Terima & Minta Kirim Barang
                            </button>
                            <button @click="handleReturnProcess('reject')" class="w-full py-3 bg-red-600 hover:bg-red-500 text-white rounded-xl font-bold">
                                ❌ Tolak Pengajuan
                            </button>
                        </div>

                        <div v-else-if="selectedOrder.return_request.status === 'approved'" class="space-y-4">
                            <div class="text-center mb-6">
                                <div class="bg-blue-500/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                                    <span class="text-2xl">🚚</span>
                                </div>
                                <h4 class="text-white font-bold">Menunggu Barang User</h4>
                                <p class="text-slate-400 text-sm">User sedang mengirim barang. Klik tombol di bawah JIKA barang fisik sudah sampai di tangan Anda.</p>
                            </div>
                            <button @click="handleReturnProcess('item_received')" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold shadow-lg shadow-purple-900/50">
                                📦 Konfirmasi Barang Sampai
                            </button>
                        </div>

                        <div v-else-if="selectedOrder.return_request.status === 'item_received'" class="space-y-4">
                            <div class="text-center mb-6">
                                <div class="bg-purple-500/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl">🛠️</span>
                                </div>
                                <h4 class="text-white font-bold">Pengecekan & Penggantian</h4>
                                <p class="text-slate-400 text-sm">Barang sudah diterima. Silakan cek fisik, ganti barang baru, atau refund.</p>
                            </div>
                            <button @click="handleReturnProcess('complete')" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-900/50">
                                ✨ Selesaikan Kasus Retur
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</template>