<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="min-h-screen font-sans text-slate-900 selection:bg-blue-600 selection:text-white bg-[#F8FAFC]">
        
        <nav class="fixed w-full z-50 bg-white border-b border-slate-200 shadow-sm py-3 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-14 items-center">
                    
                    <div class="flex items-center gap-10">
                        <Link :href="route('home')" class="flex items-center gap-2 group">
                            <img 
                                src="/images/image.png" 
                                alt="Blueline Logo" 
                                class="h-10 w-auto object-contain hover:scale-105 transition duration-300"
                            >
                        </Link>

                        <div class="hidden md:flex space-x-8">
                            <Link :href="route('home')" class="text-sm font-bold text-slate-600 hover:text-blue-500 transition" :class="{'text-blue-600': route().current('home')}">
                                Beranda
                            </Link>
                            <Link :href="route('products.index')" class="text-sm font-bold text-slate-600 hover:text-blue-500 transition" :class="{'text-blue-600': route().current('products.*')}">
                                Produk
                            </Link>
                            <Link :href="route('about')" class="text-sm font-bold text-slate-600 hover:text-blue-500 transition">
                                Tentang
                            </Link>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-6">
                        
                        <Link :href="route('cart.index')" class="relative text-slate-500 hover:text-blue-600 transition group p-2 rounded-full hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            <span v-if="$page.props.cartCount > 0" class="absolute top-1 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                                {{ $page.props.cartCount }}
                            </span>
                        </Link>

                        <div class="h-6 w-px bg-slate-200"></div>

                        <div v-if="user" class="flex items-center gap-4">
                            <div class="text-right hidden lg:block">
                                <div class="text-sm font-bold text-slate-900">{{ user.name }}</div>
                                <Link :href="route('my-orders')" class="text-xs text-blue-600 hover:underline font-medium">Lihat Pesanan</Link>
                            </div>
                            <button @click="logout" class="text-slate-400 hover:text-red-500 transition p-2 hover:bg-red-50 rounded-full" title="Keluar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            </button>
                        </div>

                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition">Masuk</Link>
                            <Link :href="route('register')" class="px-5 py-2.5 rounded-full bg-slate-900 text-white text-sm font-bold hover:bg-blue-600 transition shadow-lg shadow-slate-900/20 active:scale-95">
                                Daftar Sekarang
                            </Link>
                        </template>
                    </div>

                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-slate-500 hover:text-slate-900 p-2">
                             <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="showingNavigationDropdown" class="md:hidden bg-white border-t border-slate-100 absolute w-full shadow-xl">
                 <div class="pt-2 pb-4 space-y-1 px-4">
                    <Link :href="route('home')" class="block py-3 text-slate-600 font-bold border-b border-slate-50">Beranda</Link>
                    <Link :href="route('products.index')" class="block py-3 text-slate-600 font-bold border-b border-slate-50">Produk</Link>
                    <div v-if="user" class="py-4 border-b border-slate-50">
                        <div class="font-bold text-slate-900">{{ user.name }}</div>
                        <Link :href="route('my-orders')" class="text-sm text-blue-600 mt-1 block">Pesanan Saya</Link>
                        <button @click="logout" class="mt-3 text-red-500 text-sm font-bold">Logout</button>
                    </div>
                    <div v-else class="py-4 flex flex-col gap-3">
                         <Link :href="route('login')" class="block py-2 text-slate-600 font-bold">Masuk</Link>
                         <Link :href="route('register')" class="block py-3 text-center bg-slate-900 text-white rounded-lg font-bold">Daftar Akun</Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-20">
            <slot />
        </main>
        
        <footer class="bg-white text-slate-600 border-t border-slate-200 pt-16 pb-10 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                 <div>
                    <div class="mb-6">
                        <img src="/images/blueline-logo.png" alt="Blueline" class="h-8 w-auto opacity-80 grayscale hover:grayscale-0 transition">
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">Menyediakan koneksi internet fiber optik tercepat dan stabil untuk rumah & bisnis Anda.</p>
                 </div>
                 
                 <div>
                    <h3 class="font-bold text-slate-900 mb-4">Perusahaan</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><Link :href="route('about')" class="hover:text-blue-600">Tentang Kami</Link></li>
                        <li><a href="#" class="hover:text-blue-600">Karir</a></li>
                        <li><a href="#" class="hover:text-blue-600">Blog</a></li>
                    </ul>
                 </div>

                 <div>
                    <h3 class="font-bold text-slate-900 mb-4">Dukungan</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-blue-600">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-blue-600">Cek Coverage</a></li>
                        <li><a href="#" class="hover:text-blue-600">Syarat & Ketentuan</a></li>
                    </ul>
                 </div>

                 <div>
                    <h3 class="font-bold text-slate-900 mb-4">Hubungi Kami</h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2">📞 (021) 555-0123</li>
                        <li class="flex items-center gap-2">📧 support@blueline.id</li>
                        <li class="flex items-center gap-2">📍 Jakarta, Indonesia</li>
                    </ul>
                 </div>
            </div>
            <div class="text-center text-slate-400 text-xs border-t border-slate-100 pt-8">
                &copy; 2024 Blueline Network. All rights reserved.
            </div>
        </footer>
    </div>
</template>