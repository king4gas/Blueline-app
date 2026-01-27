<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showingSidebar = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const isActive = (routeName, params = {}) => {
    if (!route().current(routeName)) return false;
    const currentParams = route().params;
    if (Object.keys(params).length > 0) {
        return params.type === currentParams.type;
    }
    return true;
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 flex font-sans text-slate-100">
        
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 transform md:translate-x-0 md:static md:inset-0"
            :class="showingSidebar ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex items-center justify-center h-20 border-b border-slate-800 bg-slate-900/50 backdrop-blur-sm">
                <Link :href="route('admin.dashboard')" class="text-2xl font-black text-white tracking-tighter">
                    BLUE<span class="text-cyan-400">ADMIN</span>
                </Link>
            </div>

            <nav class="mt-6 px-4 space-y-2 overflow-y-auto h-[calc(100vh-5rem)] pb-10 scrollbar-hide">
                
                <Link :href="route('admin.dashboard')" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-bold"
                    :class="isActive('admin.dashboard') ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-900/40' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">📊</span> Dashboard
                </Link>

                <div class="pt-6 pb-2 px-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Katalog</div>

                <Link :href="route('admin.products.index', { type: 'service' })" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-medium"
                    :class="isActive('admin.products.index', { type: 'service' }) ? 'bg-slate-800 text-cyan-400 border border-slate-700' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">🌐</span> Layanan Internet
                </Link>

                <Link :href="route('admin.products.index', { type: 'hardware' })" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-medium"
                    :class="isActive('admin.products.index', { type: 'hardware' }) ? 'bg-slate-800 text-emerald-400 border border-slate-700' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">📦</span> Produk Fisik
                </Link>

                <div class="pt-6 pb-2 px-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Monitoring</div>

                <Link :href="route('admin.subscriptions.index')" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-medium"
                    :class="isActive('admin.subscriptions.index') ? 'bg-slate-800 text-cyan-400 border border-slate-700' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">⏱️</span> Langganan Aktif
                </Link>

                <div class="pt-6 pb-2 px-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Operasional</div>

                <Link :href="route('admin.orders.index')" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-medium"
                    :class="isActive('admin.orders.index') ? 'bg-slate-800 text-white border border-slate-700' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">📋</span> Pesanan Masuk
                </Link>

                <Link :href="route('admin.chat.index')" 
                    class="flex items-center px-4 py-3 rounded-xl transition font-medium mb-1"
                    :class="isActive('admin.chat.index') ? 'bg-slate-800 text-cyan-400 border border-slate-700 shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'">
                    <span class="mr-3">💬</span> Live Chat
                </Link>

            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">
            <header class="h-20 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 flex items-center justify-between px-6 sticky top-0 z-40">
                <button @click="showingSidebar = !showingSidebar" class="md:hidden text-slate-400 hover:text-white">☰</button>
                <div class="hidden md:block text-slate-500 text-sm font-bold">Admin Control Panel</div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-white">{{ user.name }}</div>
                        <div class="text-xs text-slate-500 capitalize">{{ user.role }}</div>
                    </div>
                    <button @click="logout" class="bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white p-2 rounded-lg transition border border-red-600/20">🚪</button>
                </div>
            </header>
            <main class="flex-1 p-6 overflow-x-hidden"><slot /></main>
            <footer class="p-6 text-center text-slate-600 text-xs border-t border-slate-800">&copy; 2026 BlueLine ISP Management System.</footer>
        </div>
        <div v-if="showingSidebar" @click="showingSidebar = false" class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"></div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>