<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Array,
    activeUser: Object,
    messages: Array
});

const form = useForm({ message: '' });
const chatContainer = ref(null);
let polling = null;

const selectUser = (userId) => {
    // Reload halaman dengan parameter user_id untuk load chat
    router.get(route('admin.chat.index'), { user_id: userId }, { preserveState: true, preserveScroll: true });
};

const sendMessage = () => {
    if (!form.message.trim()) return;
    
    form.post(route('admin.chat.store', props.activeUser.id), {
        onSuccess: () => {
            form.reset();
            scrollToBottom();
        },
        preserveScroll: true
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const refreshChat = () => {
    // Jika sedang membuka chat seseorang, refresh pesan saja
    if (props.activeUser) {
        router.reload({ only: ['messages', 'users'] });
    } else {
        router.reload({ only: ['users'] }); // Refresh list user barangkali ada chat baru
    }
};

onMounted(() => {
    scrollToBottom();
    polling = setInterval(refreshChat, 3000); // Polling 3 detik
});

onUnmounted(() => clearInterval(polling));

// Jika messages berubah (ada pesan baru masuk), scroll ke bawah
watch(() => props.messages, () => scrollToBottom(), { deep: true });
</script>

<template>
    <Head title="Live Chat Admin" />

    <div class="h-[calc(100vh-5rem)] flex bg-slate-950 overflow-hidden">
        
        <div class="w-80 bg-slate-900 border-r border-slate-800 flex flex-col">
            <div class="p-4 border-b border-slate-800">
                <h2 class="text-white font-bold text-lg">Pesan Masuk</h2>
            </div>
            <div class="flex-1 overflow-y-auto">
                <div v-for="user in users" :key="user.id" 
                     @click="selectUser(user.id)"
                     class="p-4 border-b border-slate-800 hover:bg-slate-800 transition cursor-pointer flex gap-3 relative"
                     :class="activeUser?.id === user.id ? 'bg-slate-800' : ''">
                    
                    <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center text-white font-bold uppercase shrink-0">
                        {{ user.name.substring(0,2) }}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h3 class="text-white font-bold text-sm truncate">{{ user.name }}</h3>
                            <span class="text-[10px] text-slate-500">{{ new Date(user.messages[0]?.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                        </div>
                        <p class="text-slate-400 text-xs truncate mt-1">
                            <span v-if="user.messages[0]?.is_admin">Anda: </span>
                            {{ user.messages[0]?.message }}
                        </p>
                    </div>

                    <div v-if="user.unread_count > 0" class="absolute top-4 right-4 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-[10px] text-white font-bold">
                        {{ user.unread_count }}
                    </div>
                </div>
                
                <div v-if="users.length === 0" class="p-8 text-center text-slate-500 text-sm">
                    Belum ada pesan masuk.
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-slate-950">
            
            <div v-if="activeUser" class="flex flex-col h-full">
                <div class="h-16 bg-slate-900 border-b border-slate-800 flex items-center px-6 justify-between">
                    <div>
                        <h3 class="text-white font-bold">{{ activeUser.name }}</h3>
                        <p class="text-slate-400 text-xs">{{ activeUser.email }}</p>
                    </div>
                </div>

                <div ref="chatContainer" class="flex-1 p-6 overflow-y-auto space-y-4">
                    <div v-for="msg in messages" :key="msg.id" class="flex" :class="msg.is_admin ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] p-4 rounded-2xl text-sm leading-relaxed relative"
                             :class="msg.is_admin ? 'bg-cyan-600 text-white rounded-tr-none' : 'bg-slate-800 text-slate-200 rounded-tl-none'">
                            {{ msg.message }}
                            <div class="text-[10px] opacity-60 mt-1 text-right">
                                {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-900 border-t border-slate-800">
                    <form @submit.prevent="sendMessage" class="flex gap-4">
                        <input v-model="form.message" type="text" placeholder="Tulis balasan..." 
                               class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyan-500 transition">
                        <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl transition flex items-center gap-2">
                            <span>Kirim</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>

            <div v-else class="flex-1 flex flex-col items-center justify-center text-slate-500">
                <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <p>Pilih pesan di sebelah kiri untuk mulai membalas.</p>
            </div>

        </div>
    </div>
</template>