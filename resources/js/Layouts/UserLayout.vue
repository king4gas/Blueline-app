<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="min-h-screen font-sans text-slate-100 selection:bg-cyan-500 selection:text-white bg-slate-950">
        
        <nav class="fixed w-full z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800 shadow-lg shadow-blue-900/5 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    
                    <div class="flex items-center gap-10">
                        <Link :href="route('home')" class="flex items-center gap-2 group">
                            <img 
                                src="/images/blueline-logo.png" 
                                alt="Blueline Logo" 
                                class="h-10 w-auto object-contain brightness-0 invert transition duration-300"
                            >
                        </Link>

                        <div class="hidden md:flex space-x-8">
                            <Link :href="route('home')" class="text-sm font-medium text-slate-400 hover:text-cyan-400 transition" :class="{'text-cyan-400 font-bold': route().current('home')}">
                                Beranda
                            </Link>
                            <Link :href="route('products.index')" class="text-sm font-medium text-slate-400 hover:text-cyan-400 transition" :class="{'text-cyan-400 font-bold': route().current('products.*')}">
                                Produk
                            </Link>
                            <Link :href="route('about')" class="text-sm font-medium text-slate-400 hover:text-cyan-400 transition" :class="{'text-cyan-400 font-bold': route().current('about')}">
                                Tentang
                            </Link>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-6">
                        
                        <Link :href="route('cart.index')" class="relative text-slate-400 hover:text-cyan-400 transition group p-2 rounded-full hover:bg-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            <span v-if="$page.props.cartCount > 0" class="absolute top-1 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-slate-900">
                                {{ $page.props.cartCount }}
                            </span>
                        </Link>

                        <div class="h-6 w-px bg-slate-700"></div>

                        <div v-if="user" class="flex items-center gap-4">
                            <div class="text-right hidden lg:block">
                                <div class="text-sm font-bold text-white">{{ user.name }}</div>
                                <Link :href="route('my-orders')" class="text-xs text-cyan-400 hover:underline font-medium">Lihat Pesanan</Link>
                            </div>
                            <button @click="logout" class="text-slate-400 hover:text-red-400 transition p-2 hover:bg-slate-800 rounded-full" title="Keluar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            </button>
                        </div>

                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-slate-300 hover:text-white transition">Masuk</Link>
                            <Link :href="route('register')" class="px-5 py-2.5 rounded-full bg-cyan-600 text-white text-sm font-bold hover:bg-cyan-500 transition shadow-[0_0_15px_rgba(8,145,178,0.5)] active:scale-95">
                                Daftar
                            </Link>
                        </template>
                    </div>

                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-slate-400 hover:text-white p-2">
                             <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="showingNavigationDropdown" class="md:hidden bg-slate-900 border-t border-slate-800 absolute w-full shadow-xl">
                 <div class="pt-2 pb-4 space-y-1 px-4">
                    <Link :href="route('home')" class="block py-3 text-slate-300 font-bold border-b border-slate-800">Beranda</Link>
                    <Link :href="route('products.index')" class="block py-3 text-slate-300 font-bold border-b border-slate-800">Produk</Link>
                    <div v-if="user" class="py-4 border-b border-slate-800">
                        <div class="font-bold text-white">{{ user.name }}</div>
                        <Link :href="route('my-orders')" class="text-sm text-cyan-400 mt-1 block">Pesanan Saya</Link>
                        <button @click="logout" class="mt-3 text-red-400 text-sm font-bold">Logout</button>
                    </div>
                    <div v-else class="py-4 flex flex-col gap-3">
                         <Link :href="route('login')" class="block py-2 text-slate-300 font-bold">Masuk</Link>
                         <Link :href="route('register')" class="block py-3 text-center bg-cyan-600 text-white rounded-lg font-bold">Daftar Akun</Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-20">
            <slot />
        </main>
        
        <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 pt-16 pb-10 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                 <div>
                    <div class="mb-6">
                        <img src="/images/blueline-logo.png" alt="Blueline" class="h-8 w-auto brightness-0 invert opacity-80 hover:opacity-100 transition">
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">Menyediakan koneksi internet fiber optik tercepat dan stabil dengan teknologi masa depan.</p>
                 </div>
                 
                 <div>
                    <h3 class="font-bold text-white mb-4">Perusahaan</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><Link :href="route('about')" class="hover:text-cyan-400 transition">Tentang Kami</Link></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Karir</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Blog</a></li>
                    </ul>
                 </div>

                 <div>
                    <h3 class="font-bold text-white mb-4">Dukungan</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-cyan-400 transition">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Cek Coverage</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Syarat & Ketentuan</a></li>
                    </ul>
                 </div>

                 <div>
                    <h3 class="font-bold text-white mb-4">Hubungi Kami</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2">📞 (021) 555-0123</li>
                        <li class="flex items-center gap-2">📧 support@blueline.id</li>
                        <li class="flex items-center gap-2">📍 Jakarta, Indonesia</li>
                    </ul>
                 </div>
            </div>
            <div class="text-center text-slate-600 text-xs border-t border-slate-800 pt-8">
                &copy; 2024 Blueline Network. All rights reserved.
            </div>
        </footer>
    </div>
</template>