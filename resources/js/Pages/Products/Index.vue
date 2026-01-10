<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

// === FITUR AGAR NAVBAR TIDAK REFRESH ===
defineOptions({ layout: UserLayout });

const props = defineProps({
    products: Array 
});

const user = usePage().props.auth.user;
const searchQuery = ref('');
const selectedCategory = ref('all');

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
});

const handleOrder = (product) => {
    if (!user) {
        Swal.fire({
            title: 'Login Diperlukan', text: "Silakan login untuk memesan produk ini.", icon: 'info', 
            background: '#1e293b', color: '#fff', showCancelButton: true, confirmButtonColor: '#0891b2', confirmButtonText: 'Login Sekarang', cancelButtonText: 'Nanti'
        }).then((result) => {
            if (result.isConfirmed) router.visit(route('login'));
        });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, {
            preserveScroll: true,
            onSuccess: () => { Toast.fire({ icon: 'success', title: 'Berhasil masuk keranjang!' }); },
        });
    }
};

const filteredProducts = computed(() => {
    return props.products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || product.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = selectedCategory.value === 'all' || product.type === selectedCategory.value;
        return matchesSearch && matchesCategory;
    });
});
</script>

<template>
    <Head title="Katalog Produk" />

    <div class="relative bg-slate-900 pt-32 pb-20 overflow-hidden border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-white mb-6 tracking-tight">
                Pilih Paket <span class="text-cyan-400">Terbaik.</span>
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Koneksi internet super cepat dan perangkat jaringan berkualitas untuk kebutuhan rumah maupun bisnis Anda.
            </p>
        </div>
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-cyan-600/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px]"></div>
        </div>
    </div>

    <div class="bg-slate-950 py-10 sticky top-16 z-30 backdrop-blur-md bg-slate-950/80 border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex p-1 bg-slate-900 rounded-xl border border-slate-800">
                    <button @click="selectedCategory = 'all'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300" :class="selectedCategory === 'all' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-900/50' : 'text-slate-400 hover:text-white'">Semua</button>
                    <button @click="selectedCategory = 'service'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300" :class="selectedCategory === 'service' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-900/50' : 'text-slate-400 hover:text-white'">Internet</button>
                    <button @click="selectedCategory = 'hardware'" class="px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300" :class="selectedCategory === 'hardware' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-900/50' : 'text-slate-400 hover:text-white'">Hardware</button>
                </div>

                <div class="relative w-full md:w-96 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500 group-focus-within:text-cyan-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-3 border border-slate-700 rounded-xl leading-5 bg-slate-900 text-slate-100 placeholder-slate-500 focus:outline-none focus:bg-slate-800 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-300 sm:text-sm" placeholder="Cari paket atau perangkat...">
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-950 min-h-screen py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="product in filteredProducts" :key="product.id" class="group bg-slate-900 rounded-[2rem] border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_30px_rgba(8,145,178,0.15)] transition-all duration-500 flex flex-col h-full hover:-translate-y-2">
                    <div class="aspect-[4/3] relative overflow-hidden bg-slate-950 p-6 flex items-center justify-center">
                        <img :src="product.image" class="w-full h-full object-cover rounded-xl group-hover:scale-110 transition duration-700 shadow-lg opacity-90 group-hover:opacity-100">
                        <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur shadow-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-slate-700" :class="product.type === 'service' ? 'text-cyan-400 border-cyan-900/30' : 'text-emerald-400 border-emerald-900/30'">{{ product.type === 'service' ? 'Internet' : 'Hardware' }}</div>
                        <div v-if="product.is_featured" class="absolute top-4 right-4 bg-yellow-500/10 backdrop-blur shadow-sm px-2 py-1 rounded-full border border-yellow-500/20 text-yellow-400">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-xl font-bold mb-2 text-white line-clamp-1 group-hover:text-cyan-400 transition duration-300">{{ product.name }}</h3>
                        <p class="text-slate-400 text-sm mb-6 line-clamp-2 leading-relaxed">{{ product.description }}</p>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-800">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Harga</span>
                                <span class="text-white font-black text-lg">{{ formatRupiah(product.price) }}</span>
                            </div>
                            <button @click="handleOrder(product)" class="w-12 h-12 rounded-full bg-cyan-600 text-white flex items-center justify-center hover:bg-cyan-500 transition-all duration-300 shadow-lg shadow-cyan-900/50 hover:shadow-cyan-500/40 transform active:scale-95 group/btn" title="Tambah ke Keranjang">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover/btn:rotate-90 transition duration-300"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-900 mb-6">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Produk tidak ditemukan</h3>
                <p class="text-slate-500">Coba kata kunci lain atau ubah filter kategori.</p>
                <button @click="searchQuery = ''; selectedCategory = 'all'" class="mt-6 text-cyan-400 font-bold hover:underline">Reset Filter</button>
            </div>
        </div>
    </div>
</template>