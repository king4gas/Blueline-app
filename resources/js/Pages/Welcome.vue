<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({ featuredProducts: Array });
const user = usePage().props.auth.user;
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
});

const handleOrder = (product) => {
    if (!user) {
        Swal.fire({
            title: 'Login Diperlukan', text: "Silakan login untuk memesan.", icon: 'info', showCancelButton: true, confirmButtonColor: '#2563EB', confirmButtonText: 'Login'
        }).then((result) => { if (result.isConfirmed) router.visit(route('login')); });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, { preserveScroll: true, onSuccess: () => Toast.fire({ icon: 'success', title: 'Masuk keranjang!' }) });
    }
};
</script>

<template>
    <Head title="Internet Masa Depan" />

    <UserLayout>
        
        <div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
            
            <img 
                src="/images/it.png" 
                class="absolute top-0 left-0 w-full h-full object-cover z-0 animate-pulse-slow" 
                alt="Global Tech Network"
            >
            
            <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px] z-0"></div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-[#F8FAFC] via-transparent to-transparent z-0"></div>
            
            <div class="relative z-10 text-center px-4 max-w-6xl mx-auto mt-0 md:mt-10">
                <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-blue-600 text-white text-xs font-bold uppercase tracking-widest mb-8 shadow-xl shadow-blue-600/20 animate-fade-in-up">
                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>Jaringan Fiber Optik Tercepat
                </div>
                <h1 class="text-6xl md:text-8xl font-black text-slate-900 tracking-tight mb-8 drop-shadow-xl leading-tight">
                    Koneksi Tanpa <br><span class="text-blue-700 drop-shadow-md">Kompromi.</span>
                </h1>
                <p class="text-xl md:text-2xl text-slate-800 mb-12 max-w-3xl mx-auto font-bold leading-relaxed drop-shadow-sm">
                    Streaming 4K, Gaming Low Latency, dan Work From Home tanpa hambatan dengan infrastruktur fiber optik murni dari Blueline.
                </p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    <Link :href="route('products.index')" class="group relative px-10 py-4 bg-blue-600 text-white rounded-full font-bold text-lg overflow-hidden shadow-xl shadow-blue-600/30 hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition duration-700 ease-in-out"></div>
                        <span class="relative">Lihat Paket Internet &rarr;</span>
                    </Link>
                    <a href="#coverage" class="px-10 py-4 bg-white/90 backdrop-blur text-slate-800 border-2 border-blue-100 rounded-full font-bold text-lg hover:bg-blue-50 hover:border-blue-300 transition shadow-lg">Cek Ketersediaan</a>
                </div>
            </div>
        </div>

        <div id="coverage" class="py-32 bg-[#F8FAFC] relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl font-black text-slate-900 mb-4">Jangkauan Luas</h2>
                    <p class="text-slate-500 text-lg">Hadir melayani pusat bisnis dan pariwisata Indonesia.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="(city, index) in [
                        {name: 'Bali', img: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800', desc: 'Denpasar, Kuta, Ubud'},
                        {name: 'Malang', img: 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=800', desc: 'Malang Kota & Batu'},
                        {name: 'Lombok', img: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800', desc: 'Mataram & Mandalika'}
                    ]" :key="index" class="group relative h-[450px] rounded-[2rem] overflow-hidden cursor-pointer shadow-2xl shadow-slate-200 hover:-translate-y-2 transition-all duration-500">
                        <img :src="city.img" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-white via-white/80 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8 w-full">
                            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl border border-white shadow-lg">
                                <h3 class="text-2xl font-black text-slate-900 mb-1">{{ city.name }}</h3>
                                <p class="text-blue-600 font-bold text-sm">{{ city.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-32 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900 mb-2">Produk Unggulan</h2>
                        <p class="text-slate-500 text-lg">Paket internet dan perangkat terbaik bulan ini.</p>
                    </div>
                    <Link :href="route('products.index')" class="text-blue-600 font-bold text-lg hover:underline decoration-2 underline-offset-4 flex items-center gap-2">Lihat Katalog Lengkap <span>&rarr;</span></Link>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="product in featuredProducts" :key="product.id" class="group bg-slate-50 rounded-[2rem] border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-900/5 transition duration-500 flex flex-col">
                        <div class="aspect-[4/3] relative overflow-hidden bg-white p-6 flex items-center justify-center">
                            <img :src="product.image" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition duration-500 shadow-sm">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur shadow-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-gray-100" :class="product.type === 'service' ? 'text-blue-600' : 'text-emerald-600'">{{ product.type === 'service' ? 'Internet' : 'Hardware' }}</div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-lg font-bold mb-2 text-slate-900 line-clamp-1 group-hover:text-blue-600 transition">{{ product.name }}</h3>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2 leading-relaxed">{{ product.description }}</p>
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-200">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-400 font-bold uppercase">Harga</span>
                                    <span class="text-slate-900 font-black text-lg">{{ formatRupiah(product.price) }}</span>
                                </div>
                                <button @click="handleOrder(product)" class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 transition shadow-lg transform active:scale-95 group/btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover/btn:rotate-90 transition duration-300"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="py-32 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Our Expertise</h2>
                    <p class="text-slate-500 text-lg max-w-2xl mx-auto">Kenapa ratusan perusahaan mempercayakan infrastruktur digital mereka kepada Blueline.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="group relative rounded-[2rem] overflow-hidden shadow-xl h-[500px] border border-slate-100 hover:border-transparent transition-all duration-500 hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.3] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                        <div class="relative z-10 h-full p-10 flex flex-col items-center text-center bg-white group-hover:bg-transparent transition-colors duration-300">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#FF3B30]" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.056-1.35-.166-2A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 002 0V7z" clip-rule="evenodd" /></svg>
                            </div>
                            <h3 class="text-3xl font-black mb-8 text-slate-900 group-hover:text-white transition-colors duration-300">Secure Business</h3>
                            <ul class="space-y-4 text-lg font-medium text-left inline-block text-slate-600 group-hover:text-gray-200 transition-colors duration-300">
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-300 group-hover:bg-white rounded-full transition-colors"></span>Cyber Security</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-[#FF3B30] rounded-full"></span>A Trusted Partner</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-300 group-hover:bg-white rounded-full transition-colors"></span>Application Security</li>
                            </ul>
                        </div>
                    </div>

                    <div class="group relative rounded-[2rem] overflow-hidden shadow-xl h-[500px] border border-slate-100 hover:border-transparent transition-all duration-500 hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.3] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                        <div class="relative z-10 h-full p-10 flex flex-col items-center text-center bg-white group-hover:bg-transparent transition-colors duration-300">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#FF3B30]" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
                            </div>
                            <h3 class="text-3xl font-black text-slate-900 group-hover:text-white mb-8 transition-colors duration-300">Best Support</h3>
                            <ul class="space-y-4 text-lg font-medium text-slate-600 group-hover:text-gray-200 text-left inline-block transition-colors duration-300">
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-900 group-hover:bg-white rounded-full transition-colors"></span>Customer Support 24/7</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-[#FF3B30] rounded-full"></span>Experts Technical Support</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-900 group-hover:bg-white rounded-full transition-colors"></span>Service Level Agreement</li>
                            </ul>
                        </div>
                    </div>

                    <div class="group relative rounded-[2rem] overflow-hidden shadow-xl h-[500px] border border-slate-100 hover:border-transparent transition-all duration-500 hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.3] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                        <div class="relative z-10 h-full p-10 flex flex-col items-center text-center bg-white group-hover:bg-transparent transition-colors duration-300">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#FF3B30]" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" /><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" /></svg>
                            </div>
                            <h3 class="text-3xl font-black text-slate-900 group-hover:text-white mb-8 transition-colors duration-300">Exclusive Product</h3>
                            <ul class="space-y-4 text-lg font-medium text-slate-600 group-hover:text-gray-200 text-left inline-block transition-colors duration-300">
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-900 group-hover:bg-white rounded-full transition-colors"></span>Value Added Services</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-[#FF3B30] rounded-full"></span>Manage Services</li>
                                <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-900 group-hover:bg-white rounded-full transition-colors"></span>Best Over Price</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </UserLayout>
</template>