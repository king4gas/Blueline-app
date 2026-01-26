<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';
import { ref } from 'vue'; // Tambahkan import ref

// === CONFIG LAYOUT ===
defineOptions({ layout: UserLayout });

const props = defineProps({ 
    featuredProducts: Array,
    feedbacks: Array 
});

const user = usePage().props.auth.user;
const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// === LOGIC TOAST & ORDER ===
const Toast = Swal.mixin({
    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
});

const handleOrder = (product) => {
    if (!user) {
        Swal.fire({
            title: 'Login Diperlukan', text: "Silakan login untuk memesan.", icon: 'info', background: '#1e293b', color: '#fff', showCancelButton: true, confirmButtonColor: '#0891b2', confirmButtonText: 'Login'
        }).then((result) => { if (result.isConfirmed) router.visit(route('login')); });
    } else {
        router.post(route('cart.store'), { product_id: product.id }, { preserveScroll: true, onSuccess: () => Toast.fire({ icon: 'success', title: 'Masuk keranjang!' }) });
    }
};

// ==========================================
// === LOGIC DUMMY SPEED TEST (185 Mbps) ===
// ==========================================
const speed = ref(0);
const isTesting = ref(false);
const testStatus = ref('IDLE'); // IDLE, TESTING, COMPLETED
const progressRotation = ref(0); // Rotasi gauge (0 - 180 derajat)

const startSpeedTest = () => {
    if (isTesting.value) return;
    
    isTesting.value = true;
    testStatus.value = 'TESTING';
    speed.value = 0;
    progressRotation.value = 0;

    // Phase 1: Inisialisasi (0-1 detik) - Angka kecil & acak
    let phase1 = setInterval(() => {
        speed.value = Math.floor(Math.random() * 40);
        progressRotation.value = (speed.value / 200) * 180;
    }, 80);

    // Phase 2: Boosting (Setelah 1 detik) - Angka naik cepat
    setTimeout(() => {
        clearInterval(phase1);
        
        let currentSpeed = 40;
        let phase2 = setInterval(() => {
            // Naikkan kecepatan
            currentSpeed += Math.random() * 12;
            
            // Visual limit agar tidak melebihi 200 saat proses
            if (currentSpeed > 190) currentSpeed = 175;
            
            speed.value = Math.floor(currentSpeed);
            progressRotation.value = Math.min((currentSpeed / 200) * 180, 180);
        }, 50);

        // Phase 3: Finish (Tepat di 185 Mbps setelah total 4 detik)
        setTimeout(() => {
            clearInterval(phase2);
            speed.value = 185; // TARGET DUMMY
            progressRotation.value = (185 / 200) * 180; // Posisi gauge
            isTesting.value = false;
            testStatus.value = 'COMPLETED';
        }, 3000);

    }, 1000);
};
</script>

<template>
    <Head title="Internet Masa Depan" />

    <div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden bg-slate-950">
        <img src="/images/it.jpg" class="absolute top-0 left-0 w-full h-full object-cover z-0 opacity-60" alt="Global Tech Network">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-900/50 z-0"></div>
        
        <div class="relative z-10 text-center px-4 max-w-6xl mx-auto mt-0 md:mt-10">
            <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-slate-800/50 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-widest mb-8 backdrop-blur-md animate-fade-in-up">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>Jaringan Fiber Optik Tercepat
            </div>
            
            <h1 class="text-6xl md:text-8xl font-black text-white tracking-tight mb-8 leading-tight drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                Koneksi Tanpa <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-600 drop-shadow-[0_0_20px_rgba(8,145,178,0.5)]">Kompromi.</span>
            </h1>
            
            <p class="text-xl md:text-2xl text-slate-300 mb-12 max-w-3xl mx-auto font-medium leading-relaxed">
                Streaming 4K, Gaming Low Latency, dan Work From Home tanpa hambatan.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-5 justify-center">
                <Link :href="route('products.index')" class="px-10 py-4 bg-cyan-600 text-white rounded-full font-bold text-lg hover:bg-cyan-500 hover:scale-105 transition duration-300 shadow-[0_0_20px_rgba(8,145,178,0.4)]">
                    Lihat Paket Internet &rarr;
                </Link>
                <a href="#speedtest" class="px-10 py-4 bg-slate-800/50 backdrop-blur text-white border border-slate-600 rounded-full font-bold text-lg hover:bg-slate-700 hover:border-slate-500 transition">
                    Test Kecepatan
                </a>
            </div>
        </div>
    </div>

    <div id="speedtest" class="bg-slate-950 py-24 relative overflow-hidden border-t border-slate-900">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-black text-white mb-4">
                Seberapa Cepat <span class="text-cyan-400">BlueLine?</span>
            </h2>
            <p class="text-slate-400 mb-12">Buktikan sendiri performa jaringan fiber optik kami.</p>

            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden">
                
                <div class="relative w-64 h-32 mx-auto overflow-hidden mb-6">
                    <div class="absolute top-0 left-0 w-64 h-64 rounded-full border-[24px] border-slate-800 box-border"></div>
                    
                    <div class="absolute top-0 left-0 w-64 h-64 rounded-full border-[24px] border-cyan-500 box-border transition-transform duration-100 ease-linear origin-center"
                            style="border-bottom-color: transparent; border-right-color: transparent;"
                            :style="{ transform: `rotate(${progressRotation - 135}deg)` }">
                    </div>
                    
                    <div class="absolute top-0 left-0 w-64 h-64 rounded-full border-[24px] border-slate-800/0 box-border" style="clip-path: polygon(0 50%, 100% 50%, 100% 100%, 0% 100%); background: transparent;"></div>
                </div>

                <div class="-mt-20 mb-10 relative z-20">
                    <div class="text-7xl sm:text-8xl font-black text-white tracking-tighter tabular-nums drop-shadow-[0_0_15px_rgba(6,182,212,0.5)]">
                        {{ speed }}
                    </div>
                    <div class="text-cyan-400 font-bold text-xl uppercase tracking-[0.2em] mt-2">Mbps</div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-slate-800 pt-8 mb-8">
                    <div>
                        <div class="text-[10px] sm:text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Ping</div>
                        <div class="text-white font-mono text-xl sm:text-2xl font-bold">
                            {{ isTesting ? Math.floor(Math.random() * 20) + 5 : (testStatus === 'COMPLETED' ? '4' : '-') }} <span class="text-sm text-slate-600">ms</span>
                        </div>
                    </div>
                    <div class="border-l border-r border-slate-800">
                        <div class="text-[10px] sm:text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Upload</div>
                        <div class="text-white font-mono text-xl sm:text-2xl font-bold">
                            {{ isTesting ? Math.floor(speed / 1.5) : (testStatus === 'COMPLETED' ? '95' : '-') }} <span class="text-sm text-slate-600">Mbps</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] sm:text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Jitter</div>
                        <div class="text-white font-mono text-xl sm:text-2xl font-bold">
                            {{ isTesting ? Math.floor(Math.random() * 5) : (testStatus === 'COMPLETED' ? '1' : '-') }} <span class="text-sm text-slate-600">ms</span>
                        </div>
                    </div>
                </div>

                <button 
                    @click="startSpeedTest" 
                    :disabled="isTesting"
                    class="w-full sm:w-auto px-12 py-4 rounded-xl font-black text-lg transition-all duration-300 relative group overflow-hidden"
                    :class="isTesting ? 'bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-cyan-600 hover:bg-cyan-500 text-white shadow-[0_0_25px_rgba(8,145,178,0.4)] hover:shadow-[0_0_40px_rgba(8,145,178,0.6)] hover:-translate-y-1'"
                >
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg v-if="!isTesting" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <svg v-else class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
                        {{ isTesting ? 'MENGUJI JARINGAN...' : 'MULAI SPEED TEST' }}
                    </span>
                </button>

            </div>
        </div>
    </div>

    <div id="coverage" class="py-32 bg-slate-900 relative border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-white mb-4">Jangkauan Luas</h2>
                <p class="text-slate-400 text-lg">Hadir melayani pusat bisnis dan pariwisata Indonesia.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div v-for="(city, index) in [
                    {name: 'Bali', img: 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800', desc: 'Denpasar, Kuta, Ubud'},
                    {name: 'Malang', img: 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=800', desc: 'Malang Kota & Batu'},
                    {name: 'Lombok', img: 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800', desc: 'Mataram & Mandalika'}
                ]" :key="index" class="group relative h-[450px] rounded-[2rem] overflow-hidden cursor-pointer shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-800">
                    <img :src="city.img" class="w-full h-full object-cover transition duration-700 group-hover:scale-110 opacity-80 group-hover:opacity-100">
                    <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 w-full">
                        <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-2xl border border-slate-700 shadow-lg">
                            <h3 class="text-2xl font-black text-white mb-1">{{ city.name }}</h3>
                            <p class="text-cyan-400 font-bold text-sm">{{ city.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-32 bg-slate-950 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4">
                <div>
                    <h2 class="text-4xl font-black text-white mb-2">Produk Unggulan</h2>
                    <p class="text-slate-400 text-lg">Paket internet dan perangkat terbaik bulan ini.</p>
                </div>
                <Link :href="route('products.index')" class="text-cyan-400 font-bold text-lg hover:text-cyan-300 hover:underline decoration-2 underline-offset-4 flex items-center gap-2">Lihat Katalog Lengkap <span>&rarr;</span></Link>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="product in featuredProducts" :key="product.id" class="group bg-slate-800 rounded-[2rem] border border-slate-700 overflow-hidden hover:shadow-[0_0_20px_rgba(8,145,178,0.15)] hover:border-cyan-500/50 transition duration-500 flex flex-col">
                    <div class="aspect-[4/3] relative overflow-hidden bg-slate-950 p-6 flex items-center justify-center">
                        <img :src="product.image" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition duration-500 shadow-sm opacity-90 group-hover:opacity-100">
                        <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur shadow-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-slate-700" :class="product.type === 'service' ? 'text-cyan-400' : 'text-emerald-400'">{{ product.type === 'service' ? 'Internet' : 'Hardware' }}</div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-lg font-bold mb-2 text-white line-clamp-1 group-hover:text-cyan-400 transition">{{ product.name }}</h3>
                        <p class="text-slate-400 text-sm mb-4 line-clamp-2 leading-relaxed">{{ product.description }}</p>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-700">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500 font-bold uppercase">Harga</span>
                                <span class="text-white font-black text-lg">{{ formatRupiah(product.price) }}</span>
                            </div>
                            <button @click="handleOrder(product)" class="w-12 h-12 rounded-full bg-cyan-600 text-white flex items-center justify-center hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/50 transform active:scale-95 group/btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover/btn:rotate-90 transition duration-300"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="py-32 bg-slate-900 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Apa Kata Mereka?</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Pengalaman nyata dari pelanggan kami.</p>
            </div>

            <div v-if="feedbacks.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div v-for="feedback in feedbacks" :key="feedback.id" class="bg-slate-900 p-8 rounded-[2rem] shadow-lg border border-slate-800 flex flex-col h-full hover:border-cyan-500/30 transition duration-300">
                    <div class="flex gap-1 text-yellow-400 text-xl mb-4">
                        <span v-for="n in 5" :key="n">{{ n <= feedback.rating ? '★' : '☆' }}</span>
                    </div>
                    <p class="text-slate-300 text-lg italic mb-6 flex-grow leading-relaxed">"{{ feedback.message }}"</p>
                    <div class="flex items-center gap-4 mt-auto border-t border-slate-800 pt-4">
                        <div class="w-10 h-10 rounded-full bg-cyan-900/50 text-cyan-400 font-bold flex items-center justify-center border border-cyan-800">
                            {{ feedback.user ? feedback.user.name.charAt(0).toUpperCase() : 'U' }}
                        </div>
                        <div>
                            <h4 class="font-bold text-white">{{ feedback.user ? feedback.user.name : 'Pengguna' }}</h4>
                            <p class="text-xs text-slate-500">Pelanggan Terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-10 bg-slate-900 rounded-3xl border border-dashed border-slate-700">
                <p class="text-slate-500">Belum ada ulasan.</p>
            </div>
        </div>
    </div>
</template>