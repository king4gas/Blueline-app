<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'; // Tambah usePage
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue'; // Import ref untuk modal
import Swal from 'sweetalert2';

defineOptions({ layout: UserLayout });

const props = defineProps({
    products: Array
});

const page = usePage();
const user = page.props.auth.user;

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// === LOGIC MODAL DETAIL PRODUK ===
const showModal = ref(false);
const selectedProduct = ref(null);

const openModal = (product) => {
    selectedProduct.value = product;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => {
        selectedProduct.value = null;
    }, 200); // Delay sedikit biar animasi close mulus
};

// === LOGIC ADD TO CART ===
const addToCart = (product) => {
    if (!user) {
        // Tutup modal dulu jika ada
        showModal.value = false;
        
        Swal.fire({
            title: 'Login Diperlukan',
            text: "Silakan login untuk memesan produk ini.",
            icon: 'info',
            background: '#1e293b',
            color: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#0891b2',
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) router.visit(route('login'));
        });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false; // Tutup modal setelah berhasil
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
                });
                Toast.fire({ icon: 'success', title: 'Berhasil masuk keranjang!' });
            }
        });
    }
};
</script>

<template>
    <Head title="Produk & Layanan" />

    <div class="min-h-screen bg-slate-950 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto mb-12 text-center">
            <h1 class="text-4xl font-black text-white mb-4 tracking-tight">
                Katalog <span class="text-cyan-400">Digital</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto text-lg">
                Solusi internet fiber optik berkecepatan tinggi dan perangkat jaringan terbaik untuk kebutuhan Anda.
            </p>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div v-for="product in products" :key="product.id" 
                class="group bg-slate-900 rounded-[2rem] border border-slate-800 overflow-hidden hover:border-cyan-500/50 hover:shadow-[0_0_30px_rgba(8,145,178,0.2)] transition duration-500 flex flex-col cursor-pointer"
                @click="openModal(product)"
            >
                <div class="aspect-[4/3] relative overflow-hidden bg-slate-950 p-6 flex items-center justify-center">
                    <img :src="product.image" class="w-full h-full object-cover rounded-xl group-hover:scale-110 transition duration-700 shadow-lg opacity-90 group-hover:opacity-100">
                    
                    <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur shadow-lg px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border border-slate-700 z-10" 
                         :class="product.type === 'service' ? 'text-cyan-400' : 'text-emerald-400'">
                        {{ product.type === 'service' ? 'Layanan Internet' : 'Perangkat Keras' }}
                    </div>

                    <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                        <span class="px-4 py-2 bg-white text-slate-900 rounded-full font-bold text-sm transform translate-y-4 group-hover:translate-y-0 transition duration-300 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Lihat Detail
                        </span>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow relative z-20 bg-slate-900">
                    <h3 class="text-lg font-bold mb-2 text-white line-clamp-1 group-hover:text-cyan-400 transition">{{ product.name }}</h3>
                    <p class="text-slate-400 text-sm mb-4 line-clamp-2 leading-relaxed">{{ product.description }}</p>
                    
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-800">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-bold uppercase">Harga</span>
                            <span class="text-white font-black text-lg">{{ formatRupiah(product.price) }}</span>
                        </div>
                        <button @click.stop="addToCart(product)" class="w-10 h-10 rounded-full bg-slate-800 text-cyan-400 border border-slate-700 flex items-center justify-center hover:bg-cyan-600 hover:text-white hover:border-cyan-600 transition shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 px-4">
            <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <div class="bg-slate-900 w-full max-w-4xl rounded-[2rem] shadow-2xl relative z-10 overflow-hidden animate-in fade-in zoom-in duration-300 border border-slate-700 flex flex-col md:flex-row max-h-[90vh] md:max-h-auto overflow-y-auto md:overflow-visible">
                
                <button @click="closeModal" class="absolute top-4 right-4 z-50 bg-black/50 text-white p-2 rounded-full md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>

                <div class="w-full md:w-1/2 bg-slate-950 p-8 flex items-center justify-center relative">
                    <img :src="selectedProduct.image" class="w-full h-64 md:h-full object-cover rounded-2xl shadow-2xl border border-slate-800">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border bg-slate-900/90 backdrop-blur"
                            :class="selectedProduct.type === 'service' ? 'text-cyan-400 border-cyan-500/30' : 'text-emerald-400 border-emerald-500/30'">
                            {{ selectedProduct.type === 'service' ? 'Service' : 'Hardware' }}
                        </span>
                    </div>
                </div>

                <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col bg-slate-900">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-2xl md:text-3xl font-black text-white leading-tight">{{ selectedProduct.name }}</h2>
                        <button @click="closeModal" class="hidden md:block text-slate-500 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>

                    <div class="text-3xl font-black text-cyan-400 mb-6">
                        {{ formatRupiah(selectedProduct.price) }}
                        <span v-if="selectedProduct.type === 'service'" class="text-sm text-slate-500 font-normal">/bulan</span>
                    </div>

                    <div class="space-y-4 mb-8 flex-grow">
                        <div>
                            <h4 class="text-sm font-bold text-slate-300 uppercase mb-2">Deskripsi</h4>
                            <p class="text-slate-400 leading-relaxed text-sm md:text-base">{{ selectedProduct.description }}</p>
                        </div>
                        
                        <div class="p-4 bg-slate-950 rounded-xl border border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-cyan-400">
                                <svg v-if="selectedProduct.type === 'service'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 uppercase font-bold">{{ selectedProduct.type === 'service' ? 'Kecepatan' : 'Stok Tersedia' }}</div>
                                <div class="text-white font-bold text-lg">
                                    {{ selectedProduct.type === 'service' ? (selectedProduct.speed || 'Up to 100 Mbps') : (selectedProduct.stock + ' Unit') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-auto">
                        <button @click="addToCart(selectedProduct)" class="flex-1 py-4 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl font-bold shadow-lg shadow-cyan-900/50 transition transform active:scale-95 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Masukkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>