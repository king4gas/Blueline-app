<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Lupa Password" />

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-white">
        
        <div class="flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12 order-2 lg:order-1">
            <div class="w-full max-w-md mx-auto">
                <Link :href="route('home')" class="flex items-center gap-2 mb-10 group">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:rotate-12 transition">B</div>
                    <span class="text-2xl font-bold tracking-tight text-slate-900">Blueline.</span>
                </Link>

                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Lupa Password?</h1>
                <p class="text-slate-500 mb-8">
                    Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.
                </p>

                <div v-if="status" class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            required 
                            autofocus
                            placeholder="nama@email.com"
                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition"
                        >
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full py-3.5 rounded-xl bg-slate-900 text-white font-bold text-lg hover:bg-blue-600 transition shadow-lg shadow-slate-900/20 active:scale-95 disabled:opacity-70"
                    >
                        {{ form.processing ? 'Mengirim...' : 'Kirim Tautan Reset' }}
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-slate-500">
                    <Link :href="route('login')" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Kembali ke Halaman Login
                    </Link>
                </div>
            </div>
        </div>

        <div class="hidden lg:block relative order-1 lg:order-2 bg-slate-900 overflow-hidden">
            <img 
                src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop" 
                class="absolute inset-0 w-full h-full object-cover opacity-60"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 p-16 text-white z-10">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg shadow-blue-600/50">🛡️</div>
                <h2 class="text-4xl font-extrabold mb-4 leading-tight">Keamanan Akun<br>Prioritas Kami.</h2>
                <p class="text-blue-100 text-lg max-w-md">
                    Pastikan data Anda selalu terlindungi dengan memperbarui password secara berkala.
                </p>
            </div>
        </div>
    </div>
</template>