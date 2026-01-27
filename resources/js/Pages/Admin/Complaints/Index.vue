<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    complaints: Array
});

// Helper Tanggal
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' });

// Helper Warna Badge
const getStatusBadge = (status) => {
    switch(status) {
        case 'pending': return 'bg-red-900/30 text-red-400 border-red-500/50 animate-pulse';
        case 'processing': return 'bg-yellow-900/30 text-yellow-400 border-yellow-500/50';
        case 'resolved': return 'bg-emerald-900/30 text-emerald-400 border-emerald-500/50';
        default: return 'bg-slate-800 text-white';
    }
};

// Fungsi Ubah Status
const changeStatus = (complaint, newStatus) => {
    router.post(route('admin.complaints.update', complaint.id), { status: newStatus }, {
        onSuccess: () => {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#1e293b', color: '#fff' });
            Toast.fire({ icon: 'success', title: 'Status diperbarui!' });
        }
    });
};

// Fungsi Hapus
const deleteComplaint = (id) => {
    Swal.fire({
        title: 'Hapus Laporan?',
        text: "Data tidak bisa dikembalikan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        background: '#1e293b', color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.complaints.destroy', id));
        }
    });
};
</script>

<template>
    <Head title="Laporan Gangguan" />

    <div class="py-12 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="font-black text-3xl text-white">Laporan Gangguan (CS)</h2>
                    <p class="text-slate-400">Daftar tiket bantuan dari pelanggan.</p>
                </div>
                
                <div class="px-4 py-2 bg-slate-900 rounded-lg border border-slate-800 text-sm text-slate-300">
                    Laporan Menunggu: 
                    <span class="text-red-400 font-bold ml-1 text-lg">
                        {{ complaints.filter(c => c.status === 'pending').length }}
                    </span>
                </div>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4 w-1/3">Detail Masalah</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="item in complaints" :key="item.id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    {{ formatDate(item.created_at) }}
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="font-bold text-white">{{ item.user.name }}</div>
                                    <div class="text-xs text-slate-500">{{ item.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="font-bold text-cyan-400 mb-1 text-xs uppercase tracking-wider">{{ item.subject }}</div>
                                    <p class="text-slate-300 text-sm leading-relaxed bg-slate-950/50 p-3 rounded-lg border border-slate-800/50">
                                        "{{ item.message }}"
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center align-top">
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase border tracking-wider" 
                                          :class="getStatusBadge(item.status)">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center align-top">
                                    <div class="flex justify-center gap-2">
                                        
                                        <button v-if="item.status === 'pending'" 
                                                @click="changeStatus(item, 'processing')" 
                                                title="Proses Laporan"
                                                class="p-2 bg-yellow-600/20 text-yellow-500 hover:bg-yellow-600 hover:text-white rounded-lg transition border border-yellow-600/30">
                                            ⚙️ Proses
                                        </button>
                                        
                                        <button v-if="item.status !== 'resolved'" 
                                                @click="changeStatus(item, 'resolved')" 
                                                title="Tandai Selesai"
                                                class="p-2 bg-emerald-600/20 text-emerald-500 hover:bg-emerald-600 hover:text-white rounded-lg transition border border-emerald-600/30">
                                            ✅ Selesai
                                        </button>
                                        
                                        <button @click="deleteComplaint(item.id)" title="Hapus" class="p-2 bg-slate-800 hover:bg-red-600 hover:text-white text-slate-500 rounded-lg transition border border-slate-700">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="complaints.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="text-4xl mb-2 opacity-30">📭</div>
                                    Tidak ada laporan gangguan saat ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>