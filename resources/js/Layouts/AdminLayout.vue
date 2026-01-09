<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const user = usePage().props.auth.user;

const logout = () => {
    router.post(route('logout'));
};

// Helper untuk Active State Menu
const isActive = (routeStr) => {
    return usePage().url.startsWith(routeStr);
};
</script>

<template>
    <div class="min-h-screen bg-[#F1F5F9] flex font-sans">
        
        <aside class="w-72 bg-slate-900 text-white flex-col hidden md:flex shadow-xl fixed h-full z-20">
            <div class="h-20 flex items-center px-8 border-b border-slate-800">
                <Link :href="route('admin.dashboard')" class="text-2xl font-black tracking-tight flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-sm">B</span>
                    Blueline<span class="text-blue-600">.</span>
                </Link>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>
                
                <Link :href="route('admin.dashboard')" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="isActive('/admin/dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="text-lg">📊</span>
                    <span class="font-medium">Dashboard</span>
                </Link>

                <Link :href="route('admin.orders.index')" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="isActive('/admin/orders') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="text-lg">🛒</span>
                    <span class="font-medium">Pesanan Masuk</span>
                </Link>

                <Link :href="route('admin.products.index')" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
                    :class="isActive('/admin/products') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="text-lg">📦</span>
                    <span class="font-medium">Produk & Layanan</span>
                </Link>

                <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mt-8 mb-2">Lainnya</p>
                
                <Link :href="route('home')" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <span class="text-lg">🏠</span>
                    <span class="font-medium">Lihat Website</span>
                </Link>

                <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-900/20 hover:text-red-300 transition-all text-left">
                    <span class="text-lg">🚪</span>
                    <span class="font-medium">Log Out</span>
                </button>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-slate-300">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">{{ user.name }}</div>
                        <div class="text-xs text-slate-500">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 md:ml-72 flex flex-col min-h-screen transition-all duration-300">
            
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 md:hidden sticky top-0 z-10">
                <span class="font-bold text-xl text-slate-900">Blueline Admin</span>
                <button @click="showingSidebar = !showingSidebar" class="p-2 text-gray-500 rounded hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <div v-if="showingSidebar" class="fixed inset-0 bg-black/50 z-30 md:hidden" @click="showingSidebar = false"></div>
            <aside v-if="showingSidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white z-40 p-4 space-y-4 md:hidden shadow-2xl">
                 <div class="text-xl font-bold mb-6 px-2">Menu Admin</div>
                 <Link :href="route('admin.dashboard')" class="block px-4 py-2 hover:bg-slate-800 rounded">Dashboard</Link>
                 <Link :href="route('admin.orders.index')" class="block px-4 py-2 hover:bg-slate-800 rounded">Pesanan</Link>
                 <Link :href="route('admin.products.index')" class="block px-4 py-2 hover:bg-slate-800 rounded">Produk</Link>
                 <button @click="logout" class="block w-full text-left px-4 py-2 text-red-400 hover:bg-slate-800 rounded">Logout</button>
            </aside>

            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <div class="flex justify-between items-end mb-8" v-if="$slots.header">
                    <h2 class="text-3xl font-bold text-slate-800">
                        <slot name="header" />
                    </h2>
                    </div>

                <slot />
            </main>
        </div>
    </div>
</template>