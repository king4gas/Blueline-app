<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({ featuredProducts: Array });
const user = usePage().props.auth.user;
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n);

// Konfigurasi Toast Notification
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
            text: "Silakan login untuk mulai berbelanja!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Nanti'
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('login'));
            }
        });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, {
            preserveScroll: true,
            onSuccess: () => {
                Toast.fire({
                    icon: 'success',
                    title: 'Produk masuk keranjang'
                });
            },
        });
    }
};
</script>

<template>
    <Head title="Internet Bali, Malang, Lombok" />

    <UserLayout>
        <div class="relative overflow-hidden bg-white">
            <div class="max-w-7xl mx-auto pt-20 pb-24 px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Kini Hadir di 3 Kota Besar
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-6 leading-tight">
                    Koneksi Tercepat di <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Bali, Malang & Lombok.</span>
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mb-10 leading-relaxed">
                    Nikmati pengalaman internet fiber optik tanpa batas kuota (Unlimited) dan perangkat jaringan premium.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <Link :href="route('products.index')" class="px-8 py-4 rounded-full bg-slate-900 text-white font-bold text-lg hover:bg-slate-800 transition shadow-xl shadow-slate-900/20 transform hover:-translate-y-1">
                        Cek Paket Internet
                    </Link>
                    <a href="#coverage" class="px-8 py-4 rounded-full bg-white text-slate-700 border border-gray-200 font-bold text-lg hover:bg-gray-50 transition">
                        Lihat Area
                    </a>
                </div>
            </div>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-gradient-to-b from-blue-50 to-white rounded-full blur-[120px] -z-0 pointer-events-none opacity-60"></div>
        </div>

        <div id="coverage" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-900">Jangkauan Area Kami</h2>
                    <p class="text-slate-500 mt-2">Melayani kebutuhan digital di pulau dewata hingga kota bunga.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group relative h-96 rounded-3xl overflow-hidden cursor-pointer shadow-lg">
                        <img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8 text-white">
                            <div class="text-xs font-bold uppercase tracking-wider mb-2 text-blue-300">Pusat Layanan</div>
                            <h3 class="text-3xl font-bold mb-2">Bali</h3>
                            <p class="text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition duration-500 translate-y-4 group-hover:translate-y-0">Melayani Denpasar, Badung, Gianyar, hingga Tabanan.</p>
                        </div>
                    </div>
                    <div class="group relative h-96 rounded-3xl overflow-hidden cursor-pointer shadow-lg">
                        <img src="https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8 text-white">
                            <div class="text-xs font-bold uppercase tracking-wider mb-2 text-emerald-300">Expansi Baru</div>
                            <h3 class="text-3xl font-bold mb-2">Malang</h3>
                            <p class="text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition duration-500 translate-y-4 group-hover:translate-y-0">Koneksi cepat untuk mahasiswa dan bisnis kreatif.</p>
                        </div>
                    </div>
                    <div class="group relative h-96 rounded-3xl overflow-hidden cursor-pointer shadow-lg">
                        <img src="https://images.unsplash.com/photo-1571369970878-a28a3818e6c7?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8 text-white">
                            <div class="text-xs font-bold uppercase tracking-wider mb-2 text-purple-300">Area Wisata</div>
                            <h3 class="text-3xl font-bold mb-2">Lombok</h3>
                            <p class="text-sm text-gray-300 opacity-0 group-hover:opacity-100 transition duration-500 translate-y-4 group-hover:translate-y-0">Mendukung pariwisata Mandalika dan bisnis lokal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Produk Pilihan</h2>
                    <p class="text-slate-500 mt-2">Paket internet dan hardware terbaik bulan ini.</p>
                </div>
                <Link :href="route('products.index')" class="flex items-center gap-2 text-blue-600 font-bold hover:gap-3 transition-all">
                    Lihat Katalog Lengkap <span class="text-xl">&rarr;</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="product in featuredProducts" :key="product.id" class="group relative bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <img :src="product.image" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500">
                        <div v-if="product.type === 'service'" class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-indigo-600 shadow-sm">Internet</div>
                        <div v-else class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-emerald-600 shadow-sm">Hardware</div>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="font-bold text-slate-900 mb-1 group-hover:text-blue-600 transition truncate">{{ product.name }}</h3>
                        <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ product.description }}</p>
                        <div class="mt-auto flex items-center justify-between">
                            <span class="text-lg font-bold text-slate-900">{{ formatRupiah(product.price) }}</span>
                            <button @click="handleOrder(product)" class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 transition shadow-lg shadow-slate-900/20 transform active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 bg-blue-600 rounded-3xl p-12 text-center text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-4">Butuh Penawaran Khusus Kantor?</h2>
                    <p class="text-blue-100 mb-8 max-w-2xl mx-auto">Kami menyediakan layanan Dedicated Internet 1:1.</p>
                    <a href="#" class="px-8 py-3 bg-white text-blue-600 rounded-full font-bold hover:bg-gray-100 transition">Hubungi Sales</a>
                </div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
            </div>
        </div>
    </UserLayout>
</template>