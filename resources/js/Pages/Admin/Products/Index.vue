<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    products: Array,
    filterType: String 
});

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

const pageTitle = computed(() => {
    if (props.filterType === 'service') return 'Kelola Layanan Internet';
    if (props.filterType === 'hardware') return 'Kelola Produk Fisik';
    return 'Semua Produk';
});

const deleteProduct = (id) => {
    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data hilang permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) router.delete(route('admin.products.destroy', id));
    });
};
</script>

<template>
    <Head :title="pageTitle" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h2 class="font-black text-3xl text-white">{{ pageTitle }}</h2>
                    <p class="text-slate-400 text-sm mt-1">Daftar {{ props.filterType === 'service' ? 'paket internet' : 'perangkat keras' }} aktif.</p>
                </div>
                <Link :href="route('admin.products.create', { type: props.filterType })" 
                      class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-lg shadow-blue-900/50 transition">
                    + Tambah {{ props.filterType === 'service' ? 'Layanan' : 'Produk' }}
                </Link>
            </div>

            <div class="bg-slate-900 overflow-hidden shadow-xl sm:rounded-2xl border border-slate-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-400">
                        <thead class="bg-slate-800 text-slate-200 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Gambar</th>
                                <th class="px-6 py-4">Nama Produk</th>
                                <th class="px-6 py-4">Harga</th>
                                <th v-if="props.filterType === 'service'" class="px-6 py-4">Speed / Durasi</th>
                                <th v-else class="px-6 py-4">Stok</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr v-for="product in products" :key="product.id" class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4"><img :src="product.image" class="w-12 h-12 rounded-lg object-cover border border-slate-700"></td>
                                <td class="px-6 py-4 font-bold text-white">{{ product.name }}<div class="text-[10px] text-slate-500 uppercase mt-1">{{ product.type }}</div></td>
                                <td class="px-6 py-4 text-cyan-400 font-mono font-bold">{{ formatRupiah(product.price) }}</td>
                                <td v-if="props.filterType === 'service'" class="px-6 py-4"><div class="text-white font-bold">{{ product.speed || '-' }}</div><div class="text-xs">{{ product.duration }} Hari</div></td>
                                <td v-else class="px-6 py-4"><span :class="product.stock > 0 ? 'text-emerald-400' : 'text-red-400 font-bold'">{{ product.stock }} Unit</span></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <Link :href="route('admin.products.edit', product.id)" class="p-2 bg-slate-800 hover:bg-slate-700 text-yellow-500 rounded-lg">✏️</Link>
                                        <button @click="deleteProduct(product.id)" class="p-2 bg-slate-800 hover:bg-slate-700 text-red-500 rounded-lg">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.length === 0"><td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada data.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>