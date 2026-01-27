<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'; // Pastikan Anda punya AdminLayout
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subscriptions: Array
});
</script>

<template>
    <Head title="Monitoring Langganan" />

    <div class="py-12 bg-slate-950 min-h-screen text-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="font-black text-3xl text-white">Monitoring Langganan</h2>
                    <p class="text-slate-400">Pantau masa aktif paket internet pelanggan.</p>
                </div>
                <div class="px-4 py-2 bg-slate-900 rounded-lg border border-slate-800 text-sm">
                    Total Pelanggan Aktif: <span class="text-cyan-400 font-bold">{{ subscriptions.filter(s => s.status === 'Active').length }}</span>
                </div>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Paket Layanan</th>
                                <th class="px-6 py-4">Mulai / Berakhir</th>
                                <th class="px-6 py-4 text-center">Sisa Waktu</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="sub in subscriptions" :key="sub.user_id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white">{{ sub.user_name }}</div>
                                    <div class="text-xs">{{ sub.user_email }}</div>
                                    <div class="text-[10px] text-cyan-600 font-mono mt-1">{{ sub.invoice }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full bg-blue-900/30 text-blue-400 border border-blue-800 text-xs font-bold">
                                        {{ sub.service_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white">{{ sub.start_date }}</div>
                                    <div class="text-xs text-slate-500">s.d {{ sub.expired_date }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="font-bold text-lg" :class="sub.days_remaining < 5 ? 'text-red-400' : 'text-white'">
                                        {{ sub.days_remaining > 0 ? sub.days_remaining : 0 }}
                                    </div>
                                    <div class="text-[10px] uppercase">Hari</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span v-if="sub.status === 'Active'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                        Expired
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="subscriptions.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    Belum ada data pelanggan berlangganan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>