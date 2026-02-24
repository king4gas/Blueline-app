<script setup>
// PASTIKAN IMPORTNYA ADMINLAYOUT, BUKAN USERLAYOUT
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

// WAJIB SET LAYOUT KE ADMIN
defineOptions({ layout: AdminLayout });

const props = defineProps({
    reports: Array
});

const showModal = ref(false);
const selectedReport = ref(null);

const form = useForm({
    status: '',
    admin_note: ''
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', { 
        day: 'numeric', month: 'short', year: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
};

const getStatusBadge = (status) => {
    switch(status) {
        case 'pending': return 'bg-yellow-900/30 text-yellow-400 border-yellow-700 animate-pulse';
        case 'progress': return 'bg-blue-900/30 text-blue-400 border-blue-700';
        case 'done': return 'bg-emerald-900/30 text-emerald-400 border-emerald-700';
        default: return 'bg-slate-800 text-white';
    }
};

const getStatusText = (status) => {
    if (status === 'pending') return 'Menunggu';
    if (status === 'progress') return 'Diproses';
    if (status === 'done') return 'Selesai';
};

const openModal = (report) => {
    selectedReport.value = report;
    form.status = report.status;
    form.admin_note = report.admin_note || '';
    showModal.value = true;
};

const submitUpdate = () => {
    form.patch(route('admin.reports.update', selectedReport.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            Swal.fire({
                icon: 'success', title: 'Berhasil!', background: '#1e293b', color: '#fff', showConfirmButton: false, timer: 1500
            });
        }
    });
};
</script>

<template>
    <Head title="Kelola Pusat Bantuan" />

    <div class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="font-black text-3xl text-white">Pusat Bantuan (Tiket)</h2>
                <p class="text-slate-400">Kelola dan tanggapi keluhan pelanggan.</p>
            </div>
        </div>

        <div class="bg-slate-900 overflow-hidden shadow-xl rounded-2xl border border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-400">
                    <thead class="bg-slate-950 text-slate-200 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Topik</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-if="reports.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada laporan.</td>
                        </tr>
                        <tr v-for="report in reports" :key="report.id" class="hover:bg-slate-800/50">
                            <td class="px-6 py-4 text-white font-bold">{{ report.user.name }}</td>
                            <td class="px-6 py-4">{{ report.subject }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 border rounded-full text-[10px] font-bold uppercase" :class="getStatusBadge(report.status)">
                                    {{ getStatusText(report.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="openModal(report)" class="px-4 py-2 bg-cyan-600 text-white rounded-lg font-bold text-xs">Tanggapi</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div v-if="showModal && selectedReport" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-slate-900 w-full max-w-xl rounded-2xl border border-slate-700 overflow-hidden shadow-2xl">
            <div class="p-6">
                <h3 class="text-lg font-bold text-white mb-4">Update Laporan: {{ selectedReport.subject }}</h3>
                <form @submit.prevent="submitUpdate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-2">Status</label>
                        <select v-model="form.status" class="w-full bg-slate-950 border-slate-700 text-white rounded-lg">
                            <option value="pending">Menunggu</option>
                            <option value="progress">Diproses</option>
                            <option value="done">Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-2">Catatan Admin</label>
                        <textarea v-model="form.admin_note" rows="3" class="w-full bg-slate-950 border-slate-700 text-white rounded-lg"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="text-slate-400">Batal</button>
                        <button type="submit" class="bg-cyan-600 px-4 py-2 rounded-lg text-white font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>