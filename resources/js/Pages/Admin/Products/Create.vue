<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    price: '',
    type: 'hardware',
    description: '',
    image: null,
    stock: '',
    speed: '',
});

const imagePreview = ref(null);
const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true
    });
};
</script>

<template>
    <Head title="Tambah Produk" />
    
    <AdminLayout>
        <div class="mb-6">
            <h2 class="text-2xl font-black text-white">Tambah Produk Baru</h2>
            <p class="text-slate-400 text-sm">Isi formulir di bawah untuk menambahkan item ke katalog.</p>
        </div>

        <div class="max-w-3xl mx-auto bg-slate-900 border border-slate-800 p-8 rounded-2xl shadow-xl">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-bold text-slate-200 mb-2">Nama Produk / Paket</label>
                    <input v-model="form.name" type="text" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition" 
                           placeholder="Contoh: Router Mikrotik atau Paket 50Mbps" required>
                    <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-200 mb-2">Jenis Produk</label>
                    <select v-model="form.type" 
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-cyan-500 transition">
                        <option value="hardware">📦 Hardware (Barang Fisik)</option>
                        <option value="service">📡 Service (Layanan Internet)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-200 mb-2">Harga (Rp)</label>
                        <input v-model="form.price" type="number" 
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition" 
                               placeholder="500000" required>
                    </div>

                    <div v-if="form.type === 'hardware'">
                        <label class="block text-sm font-bold text-slate-200 mb-2">Stok Awal</label>
                        <input v-model="form.stock" type="number" 
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition" 
                               placeholder="10">
                    </div>
                    <div v-else>
                        <label class="block text-sm font-bold text-slate-200 mb-2">Kecepatan (Speed)</label>
                        <input v-model="form.speed" type="text" 
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition" 
                               placeholder="Up to 100 Mbps">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-200 mb-2">Deskripsi Lengkap</label>
                    <textarea v-model="form.description" rows="4" 
                              class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition" 
                              placeholder="Jelaskan spesifikasi produk..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-200 mb-2">Foto Produk</label>
                    <div class="flex items-center gap-4">
                        <input type="file" @change="handleImageUpload" accept="image/*" 
                               class="block w-full text-sm text-slate-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-bold
                                      file:bg-slate-800 file:text-cyan-400
                                      hover:file:bg-slate-700 cursor-pointer" required>
                    </div>
                    <div v-if="form.errors.image" class="text-red-400 text-xs mt-1">{{ form.errors.image }}</div>
                    
                    <div v-if="imagePreview" class="mt-4 p-2 bg-slate-950 rounded-xl border border-slate-800 inline-block">
                        <p class="text-[10px] text-slate-500 mb-1 uppercase tracking-widest text-center">Preview</p>
                        <img :src="imagePreview" class="h-32 w-32 object-cover rounded-lg">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-800">
                    <Link :href="route('admin.products.index')" 
                          class="px-6 py-3 text-slate-400 bg-slate-800 rounded-xl hover:bg-slate-700 hover:text-white font-bold transition">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing" 
                            class="px-8 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-900/50 transition flex items-center gap-2">
                        <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Produk</span>
                    </button>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>