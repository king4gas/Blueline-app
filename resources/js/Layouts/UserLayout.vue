<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// === 1. LOGIC UMUM (NAVBAR & AUTH) ===
const page = usePage();
const user = computed(() => page.props.auth.user);
const showingNavigationDropdown = ref(false);

// Logout Function
const logout = () => {
    router.post(route('logout'));
};

// === 2. LOGIC FITUR CS / LAPORAN MASALAH ===
const showCsModal = ref(false);

const csForm = useForm({
    subject: '',
    message: ''
});

// Fungsi Buka Modal (Cek Login Dulu)
const openCsModal = () => {
    if (!user.value) {
        Swal.fire({
            icon: 'info',
            title: 'Login Diperlukan',
            text: 'Silakan login terlebih dahulu untuk melaporkan kendala.',
            background: '#1e293b', // Dark theme alert
            color: '#fff',
            showCancelButton: true,
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Nanti',
            confirmButtonColor: '#0891b2',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('login'));
            }
        });
        return;
    }
    showCsModal.value = true;
};

// Fungsi Kirim Laporan
const submitComplaint = () => {
    csForm.post(route('complaints.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCsModal.value = false;
            csForm.reset();
            Swal.fire({
                title: 'Laporan Terkirim!',
                text: 'Tim support kami akan segera menghubungi Anda.',
                icon: 'success',
                background: '#1e293b',
                color: '#fff',
                confirmButtonColor: '#0891b2'
            });
        },
        onError: () => {
            // Error validation akan muncul di form
            Swal.fire({
                title: 'Gagal',
                text: 'Mohon lengkapi formulir dengan benar.',
                icon: 'error',
                background: '#1e293b',
                color: '#fff'
            });
        }
    });
};
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
                                <span class="text-white text-sm font-bold text-right">
                                    Halo, {{ user.name }}
                                </span>
                                <div class="flex gap-2">
                                    <Link :href="route('my-orders')" class="px-4 py-2 text-xs font-bold text-white bg-slate-800 rounded-lg hover:bg-slate-700 transition border border-slate-700">Pesanan Saya</Link>
                                    <button @click="logout" class="px-4 py-2 text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-500 transition shadow-lg shadow-red-900/20">Logout</button>
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
                        <div class="mt-3 space-y-1">
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

        <div class="fixed bottom-6 right-6 z-50 group">
            <div class="absolute bottom-full mb-3 right-0 bg-slate-800 text-white text-xs px-3 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none border border-slate-700">
                Laporkan Masalah
            </div>
            
            <button 
                @click="openCsModal"
                class="w-14 h-14 bg-blue-600 hover:bg-blue-500 text-white rounded-full shadow-[0_0_20px_rgba(37,99,235,0.5)] flex items-center justify-center transition-all duration-300 hover:scale-110 animate-bounce-slow border-2 border-slate-900"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </button>
        </div>

        <div v-if="showCsModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showCsModal = false"></div>

            <div class="bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl relative z-10 overflow-hidden animate-in fade-in zoom-in duration-200 border border-slate-700">
                
                <div class="bg-slate-800 p-6 flex justify-between items-center border-b border-slate-700">
                    <div>
                        <h3 class="font-bold text-lg text-white">Pusat Bantuan</h3>
                        <p class="text-slate-400 text-xs">Laporkan kendala layanan Anda</p>
                    </div>
                    <button @click="showCsModal = false" class="text-slate-400 hover:text-white transition">
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitComplaint" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-400 mb-1">Perihal Masalah</label>
                            <input 
                                v-model="csForm.subject" 
                                type="text" 
                                class="w-full bg-slate-950 border-slate-700 text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder-slate-600" 
                                placeholder="Contoh: Internet Mati Total" 
                                required
                            >
                            <div v-if="csForm.errors.subject" class="text-red-500 text-xs mt-1">{{ csForm.errors.subject }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-400 mb-1">Detail Kendala</label>
                            <textarea 
                                v-model="csForm.message" 
                                rows="4" 
                                class="w-full bg-slate-950 border-slate-700 text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder-slate-600" 
                                placeholder="Jelaskan kronologi atau detail masalah..." 
                                required
                            ></textarea>
                            <div v-if="csForm.errors.message" class="text-red-500 text-xs mt-1">{{ csForm.errors.message }}</div>
                        </div>

                        <div class="pt-2">
                            <button 
                                type="submit" 
                                :disabled="csForm.processing"
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-900/50 flex justify-center items-center gap-2 disabled:opacity-70"
                            >
                                <span v-if="csForm.processing">Mengirim...</span>
                                <span v-else>Kirim Laporan &rarr;</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-slate-950 p-4 text-center border-t border-slate-800">
                    <p class="text-xs text-slate-500">Fast Response via WhatsApp: <a href="#" class="text-green-500 font-bold hover:underline">0812-3456-7890</a></p>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Animasi Bouncing Lambat untuk Tombol CS */
@keyframes bounce-slow {
  0%, 100% { transform: translateY(-5%); }
  50% { transform: translateY(0); }
}
.animate-bounce-slow {
  animation: bounce-slow 3s infinite ease-in-out;
}
</style>