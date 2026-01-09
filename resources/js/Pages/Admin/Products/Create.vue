<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    price: '',
    type: 'hardware', // Default hardware
    description: '',
    image: null,
    stock: '',
    speed: '',
});

// Logic Preview Gambar
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
        forceFormData: true // Wajib untuk upload file
    });
};
</script>

<template>
    <Head title="Tambah Produk" />
    <AdminLayout>
        <template #header>Tambah Produk Baru</template>

        <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk / Paket</label>
                    <input v-model="form.name" type="text" class="w-full border rounded p-2" placeholder="Contoh: Router Mikrotik atau Paket 50Mbps" required>
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Produk</label>
                    <select v-model="form.type" class="w-full border rounded p-2 bg-white">
                        <option value="hardware">📦 Hardware (Barang Fisik)</option>
                        <option value="service">📡 Service (Layanan Internet)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                        <input v-model="form.price" type="number" class="w-full border rounded p-2" placeholder="500000" required>
                    </div>

                    <div v-if="form.type === 'hardware'">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Stok Awal</label>
                        <input v-model="form.stock" type="number" class="w-full border rounded p-2" placeholder="10">
                    </div>
                    <div v-else>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kecepatan (Speed)</label>
                        <input v-model="form.speed" type="text" class="w-full border rounded p-2" placeholder="Up to 100 Mbps">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Lengkap</label>
                    <textarea v-model="form.description" rows="3" class="w-full border rounded p-2" placeholder="Jelaskan spesifikasi produk..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Produk</label>
                    <input type="file" @change="handleImageUpload" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>
                    
                    <div v-if="imagePreview" class="mt-4">
                        <p class="text-xs text-gray-500 mb-1">Preview:</p>
                        <img :src="imagePreview" class="h-32 w-32 object-cover rounded border">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <Link :href="route('admin.products.index')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Produk' }}
                    </button>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>