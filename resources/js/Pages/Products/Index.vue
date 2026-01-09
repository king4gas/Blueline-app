<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({ products: Array });
const user = usePage().props.auth.user;
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n);

const selectedCategory = ref('all');

const filteredProducts = computed(() => {
    if (selectedCategory.value === 'all') return props.products;
    return props.products.filter(product => product.type === selectedCategory.value);
});

// Helper Toast
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

const handleOrder = (product) => {
    if (!user) {
        Swal.fire({
            title: 'Login Diperlukan',
            text: "Anda harus login untuk mulai belanja.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Login',
            cancelButtonText: 'Nanti'
        }).then((result) => {
            if (result.isConfirmed) router.visit(route('login'));
        });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, {
            preserveScroll: true,
            onSuccess: () => {
                Toast.fire({ icon: 'success', title: 'Berhasil masuk keranjang!' });
            },
        });
    }
};
</script>

<template>
    <Head title="Katalog Produk & Layanan" />

    <UserLayout>
        <div class="min-h-screen bg-[#F8FAFC]">
            <div class="relative bg-white border-b border-gray-100 pt-16 pb-12 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
                        Katalog <span class="text-blue-600">Blueline.</span>
                    </h1>
                    <p class="text-lg text-slate-500 max-w-2xl mx-auto">Jelajahi pilihan paket internet berkecepatan tinggi dan perangkat keras.</p>
                </div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-blue-50 rounded-full blur-[80px] opacity-60 -z-0"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex justify-center gap-2 mb-12 overflow-x-auto pb-4 md:pb-0 scrollbar-hide">
                    <button @click="selectedCategory = 'all'" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap" :class="selectedCategory === 'all' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-gray-100 border border-gray-200'">Semua Produk</button>
                    <button @click="selectedCategory = 'service'" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap" :class="selectedCategory === 'service' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-gray-100 border border-gray-200'">📡 Internet & Layanan</button>
                    <button @click="selectedCategory = 'hardware'" class="px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap" :class="selectedCategory === 'hardware' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-500 hover:bg-gray-100 border border-gray-200'">🖥️ Hardware</button>
                </div>

                <div v-if="filteredProducts.length === 0" class="text-center py-24">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">🔍</div>
                    <h3 class="text-xl font-bold text-slate-900">Tidak ada produk ditemukan</h3>
                    <p class="text-slate-500 mt-2">Coba ganti filter kategori lain.</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="product in filteredProducts" :key="product.id" class="group relative bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 flex flex-col overflow-hidden">
                        <div class="aspect-[4/3] bg-gray-100 relative overflow-hidden">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700">
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span v-if="product.type === 'service'" class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-indigo-600 shadow-sm">Internet</span>
                                <span v-else class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-emerald-600 shadow-sm">Hardware</span>
                            </div>
                            <div class="absolute bottom-4 left-4">
                                <span v-if="product.type === 'hardware'" class="bg-black/60 backdrop-blur text-white px-3 py-1 rounded-full text-xs font-medium border border-white/20">Stok: {{ product.stock }}</span>
                                <span v-else class="bg-blue-600/80 backdrop-blur text-white px-3 py-1 rounded-full text-xs font-medium border border-white/20">🚀 {{ product.speed }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition line-clamp-1">{{ product.name }}</h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-6 line-clamp-2">{{ product.description }}</p>
                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">Harga</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl font-bold text-slate-900">{{ formatRupiah(product.price) }}</span>
                                        <span v-if="product.type === 'service'" class="text-xs text-slate-400 font-medium">/bln</span>
                                    </div>
                                </div>
                                <button @click="handleOrder(product)" class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 transition-all duration-300 shadow-lg shadow-slate-900/20 transform active:scale-95 group/btn" title="Tambah ke Keranjang">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover/btn:rotate-90 transition duration-300"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>