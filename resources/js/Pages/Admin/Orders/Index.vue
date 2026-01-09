<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    orders: Object // Data dari controller (Pagination)
});

// State untuk Modal Detail
const showModal = ref(false);
const selectedOrder = ref(null);

// Fungsi Buka Modal
const openDetail = (order) => {
    selectedOrder.value = order;
    showModal.value = true;
};

// Fungsi Update Status
const updateStatus = (newStatus) => {
    if (!confirm(`Ubah status menjadi ${newStatus}?`)) return;

    router.patch(route('admin.orders.update', selectedOrder.value.id), {
        status: newStatus
    }, {
        onSuccess: () => {
            showModal.value = false;
            // Update data lokal biar reaktif tanpa reload
            selectedOrder.value.status = newStatus; 
            // Atau biarkan inertia reload otomatis
        }
    });
};

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(n);
const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'});

// Helper Warna Badge
const statusColor = (s) => {
    if(s === 'pending_verification') return 'bg-yellow-100 text-yellow-800';
    if(s === 'verified') return 'bg-blue-100 text-blue-800';
    if(s === 'shipped') return 'bg-purple-100 text-purple-800';
    if(s === 'completed') return 'bg-green-100 text-green-800';
    return 'bg-red-100 text-red-800';
};
</script>

<template>
    <Head title="Manajemen Pesanan" />

    <AdminLayout>
        <template #header>Daftar Pesanan Masuk</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-100 text-gray-800 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold">{{ order.invoice_number }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold">{{ order.user.name }}</div>
                            <div class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-600">
                            {{ formatRupiah(order.total_price) }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="`px-2 py-1 rounded-full text-xs font-bold uppercase ${statusColor(order.status)}`">
                                {{ order.status.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="openDetail(order)" class="text-blue-600 hover:text-blue-900 font-bold hover:underline">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="p-4 border-t flex gap-2 justify-center" v-if="orders.links.length > 3">
                <template v-for="(link, k) in orders.links" :key="k">
                    <component 
                        :is="link.url ? 'Link' : 'span'" 
                        :href="link.url" 
                        class="px-3 py-1 border rounded"
                        :class="{'bg-blue-600 text-white': link.active, 'text-gray-500': !link.url}"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                
                <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center sticky top-0">
                    <h3 class="font-bold text-lg">Detail Pesanan: {{ selectedOrder.invoice_number }}</h3>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2 border-b pb-1">Data Pelanggan</h4>
                        <p class="text-sm"><span class="font-semibold">Nama:</span> {{ selectedOrder.user.name }}</p>
                        <p class="text-sm"><span class="font-semibold">Email:</span> {{ selectedOrder.user.email }}</p>
                        <p class="text-sm"><span class="font-semibold">No HP:</span> {{ selectedOrder.phone }}</p>
                        <p class="text-sm mt-2"><span class="font-semibold">Alamat:</span><br> {{ selectedOrder.address }}</p>

                        <h4 class="font-bold text-gray-800 mt-6 mb-2 border-b pb-1">Barang Dipesan</h4>
                        <ul class="space-y-2">
                            <li v-for="item in selectedOrder.items" :key="item.id" class="text-sm flex justify-between">
                                <span>{{ item.quantity }}x {{ item.product_name }}</span>
                                <span class="font-bold">{{ formatRupiah(item.price * item.quantity) }}</span>
                            </li>
                        </ul>
                        <div class="mt-2 text-xl font-bold text-blue-600 text-right border-t pt-2">
                            Total: {{ formatRupiah(selectedOrder.total_price) }}
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <h4 class="font-bold text-gray-800 mb-2 border-b pb-1">Bukti Transfer</h4>
                        <div class="bg-gray-100 rounded-lg p-2 mb-4 flex-1 flex items-center justify-center border border-dashed border-gray-300">
                            <img 
                                v-if="selectedOrder.payment_proof" 
                                :src="`/storage/${selectedOrder.payment_proof}`" 
                                class="max-h-64 object-contain rounded hover:scale-150 transition duration-300 cursor-zoom-in"
                                alt="Bukti Transfer"
                            >
                            <span v-else class="text-gray-400">Tidak ada bukti transfer</span>
                        </div>

                        <h4 class="font-bold text-gray-800 mb-2">Update Status</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                v-if="selectedOrder.status === 'pending_verification'"
                                @click="updateStatus('verified')" 
                                class="bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700"
                            >
                                ✅ Verifikasi Lunas
                            </button>
                            
                            <button 
                                v-if="selectedOrder.status === 'verified'"
                                @click="updateStatus('shipped')" 
                                class="bg-purple-600 text-white py-2 rounded font-bold hover:bg-purple-700"
                            >
                                🚚 Kirim / Pasang
                            </button>

                            <button 
                                v-if="selectedOrder.status === 'shipped'"
                                @click="updateStatus('completed')" 
                                class="bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700 col-span-2"
                            >
                                🎉 Selesai
                            </button>

                            <button 
                                v-if="selectedOrder.status !== 'completed' && selectedOrder.status !== 'rejected'"
                                @click="updateStatus('rejected')" 
                                class="bg-red-100 text-red-600 py-2 rounded font-bold hover:bg-red-200 col-span-2"
                            >
                                ❌ Tolak Pesanan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </AdminLayout>
</template>