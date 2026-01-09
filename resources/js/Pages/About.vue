<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref } from 'vue';

const user = usePage().props.auth.user;

// Form Feedback
const form = useForm({
    name: user ? user.name : '', // Auto-fill jika login
    email: user ? user.email : '',
    message: '',
    rating: 0,
});

// State untuk hover bintang
const hoverRating = ref(0);

const submitFeedback = () => {
    if (form.rating === 0) {
        alert("Mohon berikan rating bintang.");
        return;
    }
    form.post(route('feedback.store'), {
        preserveScroll: true,
        onSuccess: () => {
            alert("Terima kasih! Masukan Anda telah kami terima.");
            form.reset('message', 'rating');
        }
    });
};
</script>

<template>
    <Head title="Tentang Kami & Kontak" />

    <UserLayout>
        
        <div class="relative bg-slate-900 text-white py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">
                    Mengenal <span class="text-blue-500">Blueline.</span>
                </h1>
                <p class="text-xl text-slate-300 max-w-2xl mx-auto leading-relaxed">
                    Kami bukan sekadar penyedia internet. Kami adalah mitra teknologi yang menghubungkan Bali, Malang, dan Lombok dengan dunia.
                </p>
            </div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full blur-[150px] opacity-20 translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 rounded-full blur-[150px] opacity-20 -translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl h-80 md:h-96">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-blue-900/30"></div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">🚀</div>
                                <h3 class="text-2xl font-bold text-slate-900">Visi Kami</h3>
                            </div>
                            <p class="text-slate-600 leading-relaxed">
                                Menjadi penyedia infrastruktur digital terdepan di Indonesia yang tidak hanya cepat, tetapi juga andal dan terjangkau bagi semua kalangan, dari UMKM hingga korporasi besar.
                            </p>
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-xl">🎯</div>
                                <h3 class="text-2xl font-bold text-slate-900">Misi Kami</h3>
                            </div>
                            <ul class="space-y-2 text-slate-600 list-disc list-inside">
                                <li>Membangun jaringan fiber optik yang merata di daerah berkembang.</li>
                                <li>Menyediakan layanan pelanggan (Support) yang manusiawi dan responsif 24/7.</li>
                                <li>Menghadirkan perangkat keras jaringan original dengan harga kompetitif.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="py-20 bg-[#F8FAFC] border-y border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-900">Hubungi Kami</h2>
                    <p class="text-slate-500 mt-2">Kunjungi kantor cabang kami atau hubungi via telepon.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🏝️</span> Bali (Pusat)
                        </h3>
                        <p class="text-slate-500 text-sm mb-4">
                            Jl. Sunset Road No. 88, Kuta, Badung, Bali.
                        </p>
                        <div class="space-y-2 text-sm font-medium text-slate-700">
                            <p>📞 (0361) 123-4567</p>
                            <p>📱 0812-3456-7890 (WA)</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🍏</span> Malang
                        </h3>
                        <p class="text-slate-500 text-sm mb-4">
                            Jl. Soekarno Hatta No. 12, Lowokwaru, Malang.
                        </p>
                        <div class="space-y-2 text-sm font-medium text-slate-700">
                            <p>📞 (0341) 987-6543</p>
                            <p>📱 0812-9876-5432 (WA)</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🌶️</span> Lombok
                        </h3>
                        <p class="text-slate-500 text-sm mb-4">
                            Jl. Pejanggik No. 45, Mataram, NTB.
                        </p>
                        <div class="space-y-2 text-sm font-medium text-slate-700">
                            <p>📞 (0370) 555-1234</p>
                            <p>📱 0819-8765-4321 (WA)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-20 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="bg-blue-50 rounded-[40px] p-8 md:p-12 text-center border border-blue-100">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Suara Pelanggan</h2>
                    <p class="text-slate-500 mb-8">Bantu kami menjadi lebih baik. Berikan kritik, saran, dan penilaian Anda terhadap layanan Blueline.</p>

                    <form @submit.prevent="submitFeedback" class="text-left space-y-6 bg-white p-8 rounded-3xl shadow-sm">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                <input v-model="form.name" type="text" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama Anda" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Email (Opsional)</label>
                                <input v-model="form.email" type="email" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="email@contoh.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Kritik & Saran</label>
                            <textarea v-model="form.message" rows="4" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Ceritakan pengalaman Anda..." required></textarea>
                        </div>

                        <div class="flex flex-col items-center justify-center py-4 border-t border-dashed border-gray-200 mt-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Beri Bintang Pelayanan</label>
                            
                            <div class="flex gap-2">
                                <button 
                                    v-for="star in 5" 
                                    :key="star"
                                    type="button"
                                    @click="form.rating = star"
                                    @mouseenter="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="text-4xl transition-transform hover:scale-110 focus:outline-none"
                                    :class="(hoverRating || form.rating) >= star ? 'text-yellow-400' : 'text-gray-300'"
                                >
                                    ★
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2" v-if="form.rating > 0">
                                Anda memberi nilai: <span class="font-bold text-slate-900">{{ form.rating }}/5</span>
                            </p>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full py-4 rounded-xl bg-slate-900 text-white font-bold text-lg hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 disabled:opacity-70"
                        >
                            {{ form.processing ? 'Mengirim...' : 'Kirim Masukan' }}
                        </button>

                    </form>
                </div>

            </div>
        </div>

    </UserLayout>
</template>