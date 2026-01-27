<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showingNavigationDropdown = ref(false);

// === LOGIC NAVBAR & AUTH ===
const logout = () => {
    router.post(route('logout'));
};

// === LOGIC CHAT ===
const isChatOpen = ref(false);
const chatMessages = ref([]);
const chatInput = ref('');
const chatContainer = ref(null);
const isSending = ref(false);
let pollingInterval = null;

const toggleChat = () => {
    // Cek Login Dulu
    if (!user.value) {
        Swal.fire({
            icon: 'info',
            title: 'Login Diperlukan',
            text: 'Silakan login untuk chat dengan Admin.',
            background: '#1e293b', color: '#fff',
            confirmButtonColor: '#0891b2'
        });
        return;
    }

    isChatOpen.value = !isChatOpen.value;
    if (isChatOpen.value) {
        fetchMessages();
        startPolling();
        scrollToBottom();
    } else {
        stopPolling();
    }
};

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('chat.index'));
        if (JSON.stringify(response.data) !== JSON.stringify(chatMessages.value)) {
            chatMessages.value = response.data;
            scrollToBottom();
        }
    } catch (error) {
        console.error("Gagal memuat chat");
    }
};

const sendMessage = async () => {
    if (!chatInput.value.trim()) return;
    
    const messageToSend = chatInput.value;
    chatInput.value = ''; 
    isSending.value = true;

    // Optimistic Update
    chatMessages.value.push({
        id: 'temp-' + Date.now(),
        message: messageToSend,
        is_admin: 0,
        created_at: new Date().toISOString()
    });
    scrollToBottom();

    try {
        await axios.post(route('chat.store'), { message: messageToSend });
        fetchMessages(); 
    } catch (error) {
        console.error("Gagal kirim");
    } finally {
        isSending.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const startPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(fetchMessages, 3000); 
};

const stopPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval);
};

onUnmounted(() => stopPolling());
</script>

<template>
    <div class="min-h-screen bg-slate-950 flex flex-col font-sans">
        
        <nav class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')" class="font-black text-2xl text-white tracking-tighter">
                                BLUE<span class="text-cyan-400">LINE</span>
                            </Link>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link :href="route('home')" :class="route().current('home') ? 'text-cyan-400 border-cyan-400' : 'text-slate-400 border-transparent hover:text-white hover:border-slate-300'" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold transition duration-150 ease-in-out">
                                Home
                            </Link>
                            <Link :href="route('products.index')" :class="route().current('products.index') ? 'text-cyan-400 border-cyan-400' : 'text-slate-400 border-transparent hover:text-white hover:border-slate-300'" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold transition duration-150 ease-in-out">
                                Produk & Layanan
                            </Link>
                            <Link :href="route('about')" :class="route().current('about') ? 'text-cyan-400 border-cyan-400' : 'text-slate-400 border-transparent hover:text-white hover:border-slate-300'" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold transition duration-150 ease-in-out">
                                Tentang Kami
                            </Link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6 gap-4">
                        
                        <Link v-if="user" :href="route('cart.index')" class="relative text-slate-400 hover:text-cyan-400 transition p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </Link>

                        <div class="ml-3 relative" v-if="user">
                            <div class="flex items-center gap-4">
                                <span class="text-white text-sm font-bold text-right hidden lg:block">
                                    Halo, {{ user.name }}
                                </span>
                                <div class="flex gap-2">
                                    <Link :href="route('subscription.index')" class="px-3 py-2 text-xs font-bold text-white bg-cyan-700 rounded-lg hover:bg-cyan-600 transition border border-cyan-600">
                                        Layanan Saya
                                    </Link>
                                    <Link :href="route('my-orders')" class="px-3 py-2 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                                        Pesanan
                                    </Link>
                                    <button @click="logout" class="px-3 py-2 text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-500 transition shadow-lg shadow-red-900/20">
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else class="flex gap-3">
                            <Link :href="route('login')" class="text-slate-300 hover:text-white font-bold text-sm px-3 py-2">Masuk</Link>
                            <Link :href="route('register')" class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-sm px-5 py-2 rounded-full transition shadow-lg shadow-cyan-900/50">Daftar</Link>
                        </div>
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-slate-900 border-b border-slate-800">
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('home')" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-400 hover:text-white hover:bg-slate-800 hover:border-cyan-400">Home</Link>
                    <Link :href="route('products.index')" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-400 hover:text-white hover:bg-slate-800 hover:border-cyan-400">Produk</Link>
                </div>

                <div class="pt-4 pb-4 border-t border-slate-800">
                    <div v-if="user" class="px-4">
                        <div class="font-medium text-base text-white">{{ user.name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ user.email }}</div>
                        <div class="mt-3 space-y-2">
                            <Link :href="route('subscription.index')" class="block px-4 py-2 text-base font-medium text-cyan-400 hover:text-white hover:bg-slate-800 rounded-md">Layanan Saya</Link>
                            <Link :href="route('my-orders')" class="block px-4 py-2 text-base font-medium text-slate-400 hover:text-white hover:bg-slate-800 rounded-md">Pesanan Saya</Link>
                            <button @click="logout" class="w-full text-left block px-4 py-2 text-base font-medium text-red-400 hover:text-red-300 hover:bg-slate-800 rounded-md">Logout</button>
                        </div>
                    </div>
                    <div v-else class="px-4 space-y-2">
                        <Link :href="route('login')" class="block text-center w-full py-2 bg-slate-800 text-white rounded-lg">Masuk</Link>
                        <Link :href="route('register')" class="block text-center w-full py-2 bg-cyan-600 text-white rounded-lg">Daftar</Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <slot />
        </main>

        <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 py-8">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; 2026 BlueLine. Jaringan Masa Depan.</p>
            </div>
        </footer>

        <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
            
            <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-10 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-10 scale-95">
                <div v-if="isChatOpen" class="bg-slate-900 w-80 sm:w-96 h-[500px] rounded-2xl shadow-2xl border border-slate-700 flex flex-col overflow-hidden mb-4">
                    
                    <div class="bg-slate-800 p-4 flex justify-between items-center border-b border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">CS</div>
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-800"></div>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm">Customer Support</h3>
                                <p class="text-slate-400 text-xs">Online</p>
                            </div>
                        </div>
                        <button @click="toggleChat" class="text-slate-400 hover:text-white transition">✕</button>
                    </div>

                    <div ref="chatContainer" class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-950/50">
                        <div v-if="chatMessages.length === 0" class="text-center text-slate-500 text-xs py-10">
                            <p>👋 Halo! Ada yang bisa kami bantu?</p>
                        </div>

                        <div v-for="msg in chatMessages" :key="msg.id" class="flex" :class="msg.is_admin ? 'justify-start' : 'justify-end'">
                            <div class="max-w-[80%] p-3 rounded-2xl text-sm leading-relaxed" 
                                 :class="msg.is_admin ? 'bg-slate-800 text-slate-200 rounded-tl-none' : 'bg-cyan-600 text-white rounded-tr-none'">
                                {{ msg.message }}
                                <div class="text-[10px] opacity-50 mt-1 text-right">
                                    {{ new Date(msg.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-800 border-t border-slate-700">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <input v-model="chatInput" type="text" placeholder="Ketik pesan..." 
                                   class="flex-1 bg-slate-950 border border-slate-700 rounded-full px-4 py-2 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <button type="submit" class="w-10 h-10 bg-cyan-600 hover:bg-cyan-500 rounded-full flex items-center justify-center text-white transition shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </transition>

            <button @click="toggleChat" class="w-14 h-14 bg-cyan-600 hover:bg-cyan-500 text-white rounded-full shadow-[0_0_20px_rgba(8,145,178,0.5)] flex items-center justify-center transition-all duration-300 hover:scale-110 border-2 border-slate-900 group relative">
                <span v-if="!isChatOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </span>
                <span v-else>
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </span>
                
                <div class="absolute right-full mr-3 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none border border-slate-700">
                    Live Chat
                </div>
            </button>
        </div>

    </div>
</template>