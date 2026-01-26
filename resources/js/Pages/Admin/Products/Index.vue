<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({ products: Array });

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', minimumFractionDigits: 0}).format(n);

const deleteProduct = (id) => {
    Swal.fire({
        title: 'Hapus produk ini?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.products.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Produk berhasil dihapus.', 'success');
                },
                onError: (errors) => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: errors.error || 'Produk tidak bisa dihapus.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    })
}
</script>

<template>
    <Head title="Kelola Produk" />
    <AdminLayout>
        <template #header>
            <div class="flex flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Produk & Layanan</h2>
                    <p class="text-slate-500 text-xs md:text-sm">Kelola katalog internet dan hardware.</p>
                </div>
                <Link :href="route('admin.products.create')" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm hover:shadow active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                    Tambah Baru
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-4">
            <div v-if="products.length === 0" class="p-10 text-center">
                <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">📦</div>
                <h3 class="text-slate-900 font-bold text-sm">Produk Kosong</h3>
                <p class="text-slate-400 text-xs mb-4">Belum ada data produk.</p>
                <Link :href="route('admin.products.create')" class="text-blue-600 text-sm font-bold hover:underline">+ Buat Baru</Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-400 font-bold tracking-wider">
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Detail</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr v-for="product in products" :key="product.id" class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-md bg-gray-100 overflow-hidden border border-slate-200 flex-shrink-0">
                                        <img :src="product.image" class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700">{{ product.name }}</div>
                                        <div v-if="product.is_featured" class="text-[10px] text-yellow-600 flex items-center gap-1 font-bold">
                                            ★ Featured
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span v-if="product.type === 'service'" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wide">Layanan</span>
                                <span v-else class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">Hardware</span>
                            </td>
                            <td class="px-6 py-3 font-mono font-medium text-slate-600">{{ formatRupiah(product.price) }}</td>
                            <td class="px-6 py-3 text-xs">
                                <span v-if="product.type === 'hardware'" :class="product.stock > 0 ? 'text-slate-500' : 'text-red-500 font-bold'">Stok: {{ product.stock }}</span>
                                <span v-else class="text-blue-600 font-medium">{{ product.speed }}</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('admin.products.edit', product.id)" class="p-1.5 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </Link>

                                    <button @click="deleteProduct(product.id)" class="p-1.5 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>