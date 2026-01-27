<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    product: Object
});

// Setup Form dengan data lama
const form = useForm({
    _method: 'POST', // Trik Inertia agar bisa upload file di mode update (Laravel limitation)
    name: props.product.name,
    type: props.product.type, 
    price: props.product.price,
    stock: props.product.stock,
    description: props.product.description,
    image: null, // Kosongkan defaultnya, hanya diisi jika user upload baru
    speed: props.product.speed,
    duration: props.product.duration || 30,
    is_featured: Boolean(props.product.is_featured) // Pastikan Boolean
});

// Preview Gambar Lama (Default)
const imagePreview = ref(props.product.image);

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.products.update', props.product.id), {
        forceFormData: true,
        onSuccess: () => {
            // Optional: Show toast here
        }
    });
};
</script>

<template>
    <Head title="Edit Produk" />

    <div class="py-12 max-w-4xl mx-auto px-4">
        
        <div class="flex items-center gap-4 mb-6">
            <Link :href="route('admin.products.index', { type: form.type })" 
                  class="w-10 h-10 flex items-center justify-center bg-slate-800 rounded-full text-slate-400 hover:text-white hover:bg-slate-700 transition border border-slate-700">
                &larr;
            </Link>
            <div>
                <h2 class="text-2xl font-black text-white">Edit {{ form.type === 'service' ? 'Layanan' : 'Produk' }}</h2>
                <p class="text-slate-400 text-sm">Perbarui informasi katalog produk.</p>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <form @submit.prevent="submit" class="space-y-6 relative z-10">
                
                <div>
                    <label class="block text-slate-400 mb-2 font-bold text-sm">Tipe Kategori</label>
                    <div class="flex gap-4">
                        <label class="cursor-pointer flex-1 flex items-center justify-center gap-2 p-4 rounded-xl border transition" 
                            :class="form.type === 'service' ? 'bg-cyan-900/20 border-cyan-500 text-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.1)]' : 'bg-slate-950 border-slate-800 text-slate-500 hover:border-slate-700'">
                            <input type="radio" v-model="form.type" value="service" class="hidden">
                            <span class="font-bold">📡 Layanan Internet</span>
                        </label>
                        <label class="cursor-pointer flex-1 flex items-center justify-center gap-2 p-4 rounded-xl border transition" 
                            :class="form.type === 'hardware' ? 'bg-emerald-900/20 border-emerald-500 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-slate-950 border-slate-800 text-slate-500 hover:border-slate-700'">
                            <input type="radio" v-model="form.type" value="hardware" class="hidden">
                            <span class="font-bold">📦 Hardware</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 mb-2 text-sm font-bold">Nama Produk</label>
                    <input v-model="form.name" type="text" class="w-full bg-slate-950 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition px-4 py-3 placeholder-slate-600" placeholder="Nama Produk">
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-bold">Harga (Rp)</label>
                        <input v-model="form.price" type="number" class="w-full bg-slate-950 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition px-4 py-3">
                        <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                    </div>

                    <div v-if="form.type === 'hardware'">
                        <label class="block text-slate-400 mb-2 text-sm font-bold">Stok Barang</label>
                        <input v-model="form.stock" type="number" class="w-full bg-slate-950 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition px-4 py-3">
                    </div>
                    
                    <div v-else>
                        <label class="block text-slate-400 mb-2 text-sm font-bold">Kecepatan (Speed)</label>
                        <input v-model="form.speed" type="text" class="w-full bg-slate-950 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition px-4 py-3" placeholder="Contoh: 50 Mbps">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 mb-2 text-sm font-bold">Deskripsi</label>
                    <textarea v-model="form.description" rows="4" class="w-full bg-slate-950 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition px-4 py-3 placeholder-slate-600"></textarea>
                </div>

                <div class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-xl hover:border-slate-700 transition cursor-pointer" @click="form.is_featured = !form.is_featured">
                    <div class="relative flex items-center">
                        <input type="checkbox" v-model="form.is_featured" id="feat" class="peer sr-only">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                    </div>
                    <label for="feat" class="text-sm font-bold text-slate-300 cursor-pointer select-none flex-grow">
                        Tampilkan di Halaman Depan (Featured)
                        <span class="block text-xs text-slate-500 font-normal mt-0.5">Produk ini akan muncul di banner utama website.</span>
                    </label>
                </div>

                <div>
                    <label class="block text-slate-400 mb-2 text-sm font-bold">Update Foto (Opsional)</label>
                    
                    <div class="flex items-start gap-6">
                        <div class="w-32 h-32 rounded-xl overflow-hidden border border-slate-700 bg-slate-950 flex-shrink-0 relative group">
                            <img :src="imagePreview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <span class="text-xs text-white font-bold">Ubah</span>
                            </div>
                        </div>

                        <div class="flex-1">
                            <input type="file" @change="handleImageUpload" accept="image/*" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-6
                                file:rounded-full file:border-0
                                file:text-sm file:font-bold
                                file:bg-slate-800 file:text-white
                                hover:file:bg-slate-700
                                cursor-pointer
                            "/>
                            <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Maks 2MB. Biarkan kosong jika tidak ingin mengubah.</p>
                            <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-slate-800 mt-8">
                    <Link :href="route('admin.products.index', { type: form.type })" 
                          class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold transition border border-slate-700">
                        Batal
                    </Link>
                    <button type="submit" 
                            :disabled="form.processing" 
                            class="px-8 py-3 bg-cyan-600 text-white rounded-xl font-bold hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/50 flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>