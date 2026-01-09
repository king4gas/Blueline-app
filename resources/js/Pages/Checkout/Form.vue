<script setup>
import { Head, useForm } from '@inertiajs/vue3';

// Ambil data produk dari controller
const props = defineProps({
    product: Object,
    user: Object
});

// Setup Form Inertia
const form = useForm({
    product_id: props.product.id,
    address: '', // Alamat dikosongkan agar user isi sendiri
    phone: '',
    note: ''
});

const submit = () => {
    form.post(route('checkout.store'), {
        onFinish: () => form.reset('address', 'phone', 'note'),
    });
};
</script>

<template>
    <Head title="Checkout Pesanan" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Konfirmasi Pesanan</h2>

            <div class="bg-white shadow-lg rounded-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">

                <div class="bg-blue-600 p-8 text-white flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-blue-200 uppercase tracking-wider mb-2">Item yang dibeli</h3>
                        <h2 class="text-3xl font-bold mb-4">{{ product.name }}</h2>
                        <p class="text-blue-100 mb-6">{{ product.description }}</p>

                        <div class="inline-block bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                            <span class="text-sm text-blue-100">Total Harga:</span>
                            <div class="text-2xl font-bold">Rp {{ product.price.toLocaleString('id-ID') }}</div>
                        </div>
                    </div>
                    <div class="mt-8 text-sm text-blue-200">
                        *Harga sudah termasuk PPN. Biaya pemasangan (jika ada) ditagihkan terpisah.
                    </div>
                </div>

                <div class="p-8">
                    <form @submit.prevent="submit">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor WhatsApp / Telepon</label>
                            <input 
                                v-model="form.phone"
                                type="text" 
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Contoh: 08123456789"
                                required
                            >
                            <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap / Lokasi Pasang</label>
                            <textarea 
                                v-model="form.address"
                                rows="3"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Jalan, Nomor Rumah, RT/RW, Kecamatan..."
                                required
                            ></textarea>
                            <div v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                            <textarea 
                                v-model="form.note"
                                rows="2"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Misal: Pasang di hari libur"
                            ></textarea>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-gray-900 text-white font-bold py-3 px-4 rounded-lg hover:bg-gray-800 transition duration-300 flex justify-center items-center"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Bayar Sekarang</span>
                        </button>

                        <div class="mt-4 text-center">
                            <a :href="route('home')" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</template>