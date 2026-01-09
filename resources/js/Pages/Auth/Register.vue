<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-white">
        
        <div class="hidden lg:block relative bg-slate-900 overflow-hidden">
            <img 
                src="https://images.unsplash.com/photo-1558494949-efc5270f9c63?q=80&w=1974&auto=format&fit=crop" 
                class="absolute inset-0 w-full h-full object-cover opacity-50"
            >
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/90"></div>
            
            <div class="absolute bottom-0 left-0 p-16 text-white z-10">
                <blockquote class="text-2xl font-medium italic mb-6">
                    "Sejak menggunakan Blueline, produktivitas tim kami meningkat karena internet yang sangat stabil."
                </blockquote>
                <div class="flex items-center gap-4">
                    <img src="https://i.pravatar.cc/100?img=11" class="w-12 h-12 rounded-full border-2 border-blue-500">
                    <div>
                        <div class="font-bold">Andi Pratama</div>
                        <div class="text-sm text-blue-200">CEO Startup Digital</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12">
            <div class="w-full max-w-md mx-auto">
                <Link :href="route('home')" class="flex items-center gap-2 mb-10 group">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:rotate-12 transition">B</div>
                    <span class="text-2xl font-bold tracking-tight text-slate-900">Blueline.</span>
                </Link>

                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Buat Akun Baru 🚀</h1>
                <p class="text-slate-500 mb-8">Mulai perjalanan digital Anda bersama kami hari ini.</p>

                <form @submit.prevent="submit" class="space-y-5">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            autofocus
                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition"
                            placeholder="John Doe"
                        >
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            required 
                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition"
                            placeholder="nama@email.com"
                        >
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            required 
                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition"
                            placeholder="Minimal 8 karakter"
                        >
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Konfirmasi Password</label>
                        <input 
                            type="password" 
                            v-model="form.password_confirmation" 
                            required 
                            class="w-full px-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition"
                            placeholder="Ulangi password"
                        >
                        <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full py-3.5 rounded-xl bg-blue-600 text-white font-bold text-lg hover:bg-blue-700 transition shadow-lg shadow-blue-600/30 active:scale-95 disabled:opacity-70"
                    >
                        {{ form.processing ? 'Mendaftar...' : 'Daftar Sekarang' }}
                    </button>

                </form>

                <div class="mt-8 text-center text-sm text-slate-500">
                    Sudah punya akun? 
                    <Link :href="route('login')" class="text-blue-600 font-bold hover:underline">Login di sini</Link>
                </div>
            </div>
        </div>

    </div>
</template>