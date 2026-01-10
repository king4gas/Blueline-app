<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue';
import Swal from 'sweetalert2';

// === FITUR AGAR NAVBAR TIDAK REFRESH ===
defineOptions({ layout: UserLayout });

const user = usePage().props.auth.user;

const form = useForm({
    name: user ? user.name : '',
    email: user ? user.email : '',
    message: '',
    rating: 0,
});

const hoverRating = ref(0);

const submitFeedback = () => {
    if (form.rating === 0) {
        Swal.fire({ icon: 'warning', title: 'Lupa Bintang?', text: 'Mohon berikan penilaian bintang.', background: '#1e293b', color: '#fff', confirmButtonColor: '#0891b2' });
        return;
    }

    form.post(route('feedback.store'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Terima Kasih!', text: 'Masukan Anda telah kami terima.', background: '#1e293b', color: '#fff', confirmButtonColor: '#0891b2', timer: 3000, timerProgressBar: true });
            form.reset('message', 'rating');
        },
        onError: () => { Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.', background: '#1e293b', color: '#fff' }); }
    });
};
</script>

<template>
    <Head title="Tentang Kami & Kontak" />

    <div class="relative bg-slate-900 text-white py-24 overflow-hidden border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">
                Mengenal <span class="text-cyan-500">Blueline.</span>
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Mitra teknologi yang menghubungkan Bali, Malang, dan Lombok dengan dunia digital tanpa batas.
            </p>
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-600 rounded-full blur-[150px] opacity-10 translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-600 rounded-full blur-[150px] opacity-10 -translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="py-20 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl h-80 md:h-96 border border-slate-800 group">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-slate-900/40"></div>
                </div>
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-cyan-900/30 flex items-center justify-center text-xl border border-cyan-800 text-cyan-400">🚀</div>
                            <h3 class="text-2xl font-bold text-white">Visi Kami</h3>
                        </div>
                        <p class="text-slate-400 leading-relaxed">Menjadi penyedia infrastruktur digital terdepan di Indonesia yang tidak hanya cepat, tetapi juga andal dan terjangkau.</p>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-900/30 flex items-center justify-center text-xl border border-emerald-800 text-emerald-400">🎯</div>
                            <h3 class="text-2xl font-bold text-white">Misi Kami</h3>
                        </div>
                        <ul class="space-y-2 text-slate-400 list-disc list-inside marker:text-cyan-500">
                            <li>Membangun jaringan fiber optik yang merata.</li>
                            <li>Layanan pelanggan (Support) responsif 24/7.</li>
                            <li>Menghadirkan perangkat keras original berkualitas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-24 bg-slate-900 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4 tracking-tight">Our Expertise</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Standar kualitas yang membuat kami dipercaya ribuan pelanggan.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group relative rounded-[2rem] overflow-hidden shadow-lg h-[450px] bg-slate-800 border border-slate-700 hover:border-cyan-500/50 transition-all duration-500 hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.4] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                    <div class="relative z-10 h-full p-10 flex flex-col items-center text-center group-hover:bg-slate-900/60 transition-colors duration-300">
                        <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mb-8 shadow-lg border border-slate-700 group-hover:border-white/20 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-cyan-400 group-hover:text-white transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.056-1.35-.166-2A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 002 0V7z" clip-rule="evenodd" /></svg>
                        </div>
                        <h3 class="text-3xl font-black mb-8 text-white">Secure Business</h3>
                        <ul class="space-y-4 text-lg font-medium text-slate-400 group-hover:text-slate-200 text-left inline-block">
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-cyan-500 rounded-full shadow-[0_0_10px_#06b6d4]"></span>Cyber Security</li>
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-500 rounded-full"></span>Trusted Partner</li>
                        </ul>
                    </div>
                </div>
                <div class="group relative rounded-[2rem] overflow-hidden shadow-lg h-[450px] bg-slate-800 border border-slate-700 hover:border-cyan-500/50 transition-all duration-500 hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.4] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                    <div class="relative z-10 h-full p-10 flex flex-col items-center text-center group-hover:bg-slate-900/60 transition-colors duration-300">
                        <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mb-8 shadow-lg border border-slate-700 group-hover:border-white/20 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-cyan-400 group-hover:text-white transition" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>
                        </div>
                        <h3 class="text-3xl font-black text-white mb-8">Best Support</h3>
                        <ul class="space-y-4 text-lg font-medium text-slate-400 group-hover:text-slate-200 text-left inline-block">
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-cyan-500 rounded-full shadow-[0_0_10px_#06b6d4]"></span>Support 24/7</li>
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-500 rounded-full"></span>Experts Tech</li>
                        </ul>
                    </div>
                </div>
                <div class="group relative rounded-[2rem] overflow-hidden shadow-lg h-[450px] bg-slate-800 border border-slate-700 hover:border-cyan-500/50 transition-all duration-500 hover:-translate-y-2">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover brightness-[0.4] opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0">
                    <div class="relative z-10 h-full p-10 flex flex-col items-center text-center group-hover:bg-slate-900/60 transition-colors duration-300">
                        <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mb-8 shadow-lg border border-slate-700 group-hover:border-white/20 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-cyan-400 group-hover:text-white transition" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" /><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" /></svg>
                        </div>
                        <h3 class="text-3xl font-black text-white mb-8">Exclusive</h3>
                        <ul class="space-y-4 text-lg font-medium text-slate-400 group-hover:text-slate-200 text-left inline-block">
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-cyan-500 rounded-full shadow-[0_0_10px_#06b6d4]"></span>Manage Services</li>
                            <li class="flex items-center gap-3"><span class="w-3 h-3 bg-slate-500 rounded-full"></span>Best Price</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-20 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12"><h2 class="text-3xl font-bold text-white">Hubungi Kami</h2><p class="text-slate-400 mt-2">Kunjungi kantor cabang kami.</p></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-900 p-8 rounded-3xl shadow-sm border border-slate-800 hover:border-cyan-500/30 transition hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2"><span class="text-2xl">🏝️</span> Bali (Pusat)</h3>
                    <p class="text-slate-400 text-sm mb-4">Jl. Sunset Road No. 88, Kuta, Badung, Bali.</p>
                    <p class="text-sm font-medium text-cyan-400">📞 (0361) 123-4567</p>
                </div>
                <div class="bg-slate-900 p-8 rounded-3xl shadow-sm border border-slate-800 hover:border-cyan-500/30 transition hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2"><span class="text-2xl">🍏</span> Malang</h3>
                    <p class="text-slate-400 text-sm mb-4">Jl. Soekarno Hatta No. 12, Lowokwaru, Malang.</p>
                    <p class="text-sm font-medium text-cyan-400">📞 (0341) 987-6543</p>
                </div>
                <div class="bg-slate-900 p-8 rounded-3xl shadow-sm border border-slate-800 hover:border-cyan-500/30 transition hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2"><span class="text-2xl">🌶️</span> Lombok</h3>
                    <p class="text-slate-400 text-sm mb-4">Jl. Pejanggik No. 45, Mataram, NTB.</p>
                    <p class="text-sm font-medium text-cyan-400">📞 (0370) 555-1234</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-20 bg-slate-900 border-t border-slate-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-800 rounded-[40px] p-8 md:p-12 text-center border border-slate-700 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <h2 class="text-3xl font-bold text-white mb-2 relative z-10">Suara Pelanggan</h2>
                <p class="text-slate-400 mb-8 relative z-10">Bantu kami menjadi lebih baik. Berikan kritik, saran, dan penilaian Anda.</p>
                <form @submit.prevent="submitFeedback" class="text-left space-y-6 bg-slate-900 p-8 rounded-3xl shadow-inner border border-slate-800 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="w-full rounded-xl bg-slate-800 border-slate-700 text-white focus:ring-cyan-500 focus:border-cyan-500 placeholder-slate-500 transition shadow-sm" placeholder="Nama Anda" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Email (Opsional)</label>
                            <input v-model="form.email" type="email" class="w-full rounded-xl bg-slate-800 border-slate-700 text-white focus:ring-cyan-500 focus:border-cyan-500 placeholder-slate-500 transition shadow-sm" placeholder="email@contoh.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Kritik & Saran</label>
                        <textarea v-model="form.message" rows="4" class="w-full rounded-xl bg-slate-800 border-slate-700 text-white focus:ring-cyan-500 focus:border-cyan-500 placeholder-slate-500 transition shadow-sm" placeholder="Ceritakan pengalaman Anda..." required></textarea>
                    </div>
                    <div class="flex flex-col items-center justify-center py-6 border-t border-dashed border-slate-700 mt-4">
                        <label class="block text-sm font-bold text-slate-300 mb-3">Beri Bintang Pelayanan</label>
                        <div class="flex gap-2">
                            <button v-for="star in 5" :key="star" type="button" @click="form.rating = star" @mouseenter="hoverRating = star" @mouseleave="hoverRating = 0" class="text-4xl transition-transform hover:scale-110 focus:outline-none" :class="(hoverRating || form.rating) >= star ? 'text-yellow-400 drop-shadow-[0_0_10px_rgba(250,204,21,0.5)]' : 'text-slate-700'">★</button>
                        </div>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full py-4 rounded-xl bg-cyan-600 text-white font-bold text-lg hover:bg-cyan-500 transition shadow-[0_0_20px_rgba(8,145,178,0.4)] disabled:opacity-70 disabled:cursor-not-allowed">{{ form.processing ? 'Mengirim...' : 'Kirim Masukan' }}</button>
                </form>
            </div>
        </div>
    </div>
</template>