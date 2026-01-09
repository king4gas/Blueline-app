<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="min-h-screen bg-[#F8FAFC] font-sans text-slate-800 flex flex-col selection:bg-blue-100 selection:text-blue-900">
        
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20"> <div class="flex items-center gap-10">
                        <Link :href="route('home')" class="flex items-center gap-2">
                            <img src="/images/image.png" alt="Blueline Logo" class="h-10 w-auto object-contain">
                        </Link>
                        <div class="hidden md:flex space-x-8">
                            <Link :href="route('home')" 
                                  class="text-sm font-medium transition hover:text-blue-600"
                                  :class="$page.url === '/' ? 'text-blue-600' : 'text-slate-500'">
                                Beranda
                            </Link>
                            <Link :href="route('products.index')" 
                                  class="text-sm font-medium transition hover:text-blue-600"
                                  :class="$page.url.startsWith('/products') ? 'text-blue-600' : 'text-slate-500'">
                                Produk
                            </Link>
                            <Link :href="route('about')" 
                                class="text-sm font-medium transition hover:text-blue-600"
                                :class="$page.url === '/about' ? 'text-blue-600 font-bold' : 'text-slate-500'">
                                Tentang Kami
                            </Link>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-6">
                        
                        <div v-if="user" class="flex items-center gap-6">
                            <Link :href="route('cart.index')" class="relative text-slate-500 hover:text-blue-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <span v-if="$page.props.cartCount > 0" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white ring-2 ring-white">
                                    {{ $page.props.cartCount }}
                                </span>
                            </Link>

                            <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                                <div class="text-right hidden lg:block">
                                    <div class="text-sm font-bold text-slate-900">{{ user.name }}</div>
                                    <Link :href="route('my-orders')" class="text-xs text-blue-600 hover:underline">Lihat Pesanan</Link>
                                </div>
                                <button @click="logout" class="p-2 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition" title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                </button>
                            </div>
                        </div>

                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-slate-600 hover:text-blue-600">Masuk</Link>
                            <Link :href="route('register')" class="px-5 py-2.5 rounded-full bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">
                                Daftar Sekarang
                            </Link>
                        </template>
                    </div>

                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="showingNavigationDropdown" class="md:hidden bg-white border-t border-gray-100 absolute w-full shadow-xl">
                <div class="pt-2 pb-4 space-y-1 px-4">
                    <Link :href="route('home')" class="block py-3 text-base font-medium text-slate-600 border-b border-gray-50">Beranda</Link>
                    <Link :href="route('products.index')" class="block py-3 text-base font-medium text-slate-600 border-b border-gray-50">Produk</Link>
                    <Link :href="route('cart.index')" class="block py-3 text-base font-medium text-slate-600 border-b border-gray-50">Keranjang ({{ $page.props.cartCount }})</Link>
                    
                    <div v-if="user" class="pt-4">
                        <div class="font-bold text-slate-900">{{ user.name }}</div>
                        <div class="text-sm text-slate-500 mb-4">{{ user.email }}</div>
                        <Link :href="route('my-orders')" class="block w-full text-center py-2 rounded-lg bg-blue-50 text-blue-600 font-bold mb-2">Pesanan Saya</Link>
                        <button @click="logout" class="block w-full text-center py-2 rounded-lg bg-gray-100 text-gray-600 font-bold">Log Out</button>
                    </div>
                    <div v-else class="pt-4 flex flex-col gap-2">
                         <Link :href="route('login')" class="block w-full text-center py-2 rounded-lg bg-gray-100 font-bold">Masuk</Link>
                         <Link :href="route('register')" class="block w-full text-center py-2 rounded-lg bg-slate-900 text-white font-bold">Daftar</Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow pt-20">
            <slot />
        </main>
        
        <footer class="bg-white border-t border-gray-200 mt-20">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">B</div>
                        <span class="text-xl font-bold text-slate-900">Blueline.</span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Penyedia layanan internet fiber optik dan hardware jaringan premium untuk kebutuhan digital masa depan.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Layanan</h4>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-blue-600 transition">Internet Rumah</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Dedicated Corporate</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Instalasi Hardware</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Perusahaan</h4>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-blue-600 transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Karir</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Hubungi Kami</h4>
                    <p class="text-slate-500 text-sm mb-2">support@blueline.id</p>
                    <p class="text-slate-500 text-sm font-mono">+62 812-3456-7890</p>
                    <div class="flex gap-4 mt-4">
                        <div class="w-8 h-8 bg-gray-100 rounded-full hover:bg-blue-50 hover:text-blue-600 transition cursor-pointer flex items-center justify-center">📷</div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full hover:bg-blue-50 hover:text-blue-600 transition cursor-pointer flex items-center justify-center">🐦</div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100 py-6 text-center text-slate-400 text-xs">
                &copy; 2024 Blueline Network. Dibuat dengan presisi di Bali.
            </div>
        </footer>
    </div>
</template>