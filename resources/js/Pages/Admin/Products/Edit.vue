<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ product: Object });

// Isi form dengan data produk yang ada
const form = useForm({
    _method: 'POST', // Trik Inertia agar bisa upload file di mode update
    name: props.product.name,
    price: props.product.price,
    type: props.product.type,
    description: props.product.description,
    image: null,
    stock: props.product.stock,
    speed: props.product.speed,
    is_featured: Boolean(props.product.is_featured)
});

// Tampilkan gambar lama secara default
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
        forceFormData: true
    });
};
</script>

<template>
    <Head title="Edit Produk" />
    <AdminLayout>
        <template #header>Edit Produk</template>

        <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow border border-slate-200 mt-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-800">Edit Data Produk</h2>
                <Link :href="route('admin.products.index')" class="text-sm text-blue-600 hover:underline">Kembali</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input v-model="form.name" type="text" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Produk</label>
                    <select v-model="form.type" class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                        <option value="hardware">📦 Hardware</option>
                        <option value="service">📡 Service</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Harga (Rp)</label>
                        <input v-model="form.price" type="number" class="w-full border border-slate-300 rounded-lg p-2.5" required>
                    </div>

                    <div v-if="form.type === 'hardware'">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Stok</label>
                        <input v-model="form.stock" type="number" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div v-else>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kecepatan</label>
                        <input v-model="form.speed" type="text" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                    <textarea v-model="form.description" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>

                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <input type="checkbox" v-model="form.is_featured" id="feat" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <label for="feat" class="text-sm font-bold text-slate-700 cursor-pointer select-none">Tampilkan di Halaman Depan (Featured)</label>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Update Foto (Opsional)</label>
                    <input type="file" @change="handleImageUpload" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>

                    <div v-if="imagePreview" class="mt-4">
                        <p class="text-xs text-slate-500 mb-1">Preview:</p>
                        <img :src="imagePreview" class="h-32 w-32 object-cover rounded-lg border border-slate-200">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <Link :href="route('admin.products.index')" class="px-4 py-2 text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md transition disabled:opacity-70">
                        {{ form.processing ? 'Menyimpan...' : 'Update Produk' }}
                    </button>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>