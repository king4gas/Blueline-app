<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const isOpen = ref(false);
const activeTab = ref('form'); // 'form' atau 'history'
const histories = ref([]);

const form = useForm({
    subject: '',
    description: ''
});

// Buka/Tutup Modal & Fetch History
const toggleWidget = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) fetchHistory();
};

const fetchHistory = async () => {
    try {
        const response = await axios.get(route('reports.history'));
        histories.value = response.data;
    } catch (error) {
        console.error("Gagal mengambil riwayat laporan");
    }
};

const submitReport = () => {
    form.post(route('reports.store'), {
        onSuccess: () => {
            form.reset();
            activeTab.value = 'history';
            fetchHistory(); // Refresh list
        }
    });
};

// === FUNGSI FORMAT TANGGAL ===
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: 'numeric', month: 'short', year: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
};

const getStatusBadge = (status) => {
    if (status === 'pending') return 'bg-yellow-500/20 text-yellow-500 border-yellow-500/50';
    if (status === 'progress') return 'bg-blue-500/20 text-blue-500 border-blue-500/50';
    if (status === 'done') return 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50';
};
const getStatusText = (status) => {
    if (status === 'pending') return 'Menunggu';
    if (status === 'progress') return 'Diproses';
    if (status === 'done') return 'Selesai';
};
</script>

<template>
    <div class="fixed bottom-6 left-6 z-[100] font-sans">
        
        <transition name="fade-slide">
            <div v-if="isOpen" class="absolute bottom-16 left-0 w-80 sm:w-96 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden flex flex-col mb-2">
                
                <div class="bg-cyan-600 p-4 flex justify-between items-center shadow-md">
                    <h3 class="text-white font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                        </svg>
                        Pusat Bantuan
                    </h3>
                    <button @click="toggleWidget" class="text-cyan-100 hover:text-white transition text-xl">✕</button>
                </div>

                <div class="flex border-b border-slate-800 bg-slate-950">
                    <button @click="activeTab = 'form'" :class="activeTab === 'form' ? 'text-cyan-400 border-cyan-400' : 'text-slate-500 border-transparent'" class="flex-1 py-3 text-sm font-bold border-b-2 transition">Buat Laporan</button>
                    <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'text-cyan-400 border-cyan-400' : 'text-slate-500 border-transparent'" class="flex-1 py-3 text-sm font-bold border-b-2 transition">Riwayat</button>
                </div>

                <div v-if="activeTab === 'form'" class="p-5 max-h-[60vh] overflow-y-auto">
                    <form @submit.prevent="submitReport" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-1">Masalah / Topik</label>
                            <input v-model="form.subject" required type="text" placeholder="Contoh: Internet Mati Total" class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-1">Detail Keluhan</label>
                            <textarea v-model="form.description" required rows="4" placeholder="Jelaskan masalah yang Anda alami secara detail..." class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-white text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"></textarea>
                        </div>
                        <button type="submit" :disabled="form.processing" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-sm transition disabled:opacity-50 shadow-lg shadow-cyan-900/30">
                            {{ form.processing ? 'Mengirim...' : 'Kirim Laporan' }}
                        </button>
                    </form>
                </div>

                <div v-if="activeTab === 'history'" class="p-0 max-h-[60vh] overflow-y-auto custom-scrollbar bg-slate-950">
                    <div v-if="histories.length === 0" class="p-8 text-center text-slate-500 text-sm">Belum ada riwayat laporan.</div>
                    
                    <div v-for="item in histories" :key="item.id" class="border-b border-slate-800 p-4 hover:bg-slate-900 transition">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="text-white font-bold text-sm">{{ item.subject }}</h4>
                            <span :class="getStatusBadge(item.status)" class="px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider shrink-0 ml-2">{{ getStatusText(item.status) }}</span>
                        </div>
                        
                        <div class="text-[10px] text-slate-500 font-mono mb-2">
                            {{ formatDate(item.created_at) }}
                        </div>

                        <p class="text-slate-400 text-xs mb-3 line-clamp-2">{{ item.description }}</p>
                        
                        <div v-if="item.admin_note" class="bg-cyan-900/20 border border-cyan-800/50 rounded-lg p-3">
                            <p class="text-cyan-400 text-[10px] font-bold uppercase mb-1">Tanggapan Admin:</p>
                            <p class="text-slate-300 text-xs italic">"{{ item.admin_note }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <button @click="toggleWidget" class="w-14 h-14 bg-cyan-600 hover:bg-cyan-500 text-white rounded-full shadow-[0_0_20px_rgba(8,145,178,0.5)] flex items-center justify-center transition-transform hover:scale-110 active:scale-95 group">
            
            <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            
            <div class="absolute left-full ml-3 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none border border-slate-700">
                Pusat Bantuan
            </div>
        </button>
    </div>
</template>

<style scoped>
.fade-slide-enter-active, .fade-slide-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateY(20px) scale(0.95); }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #020617; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
</style>