<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({ carts: Array });

const selectedItems = ref([]); 
const showPaymentModal = ref(false);

const form = useForm({
    selected_cart_ids: [],
    address: '',
    phone: '',
    payment_proof: null,
});

const grandTotal = computed(() => {
    let total = 0;
    if (props.carts) {
        props.carts.forEach(cart => {
            if (selectedItems.value.includes(cart.id)) total += cart.product.price * cart.quantity;
        });
    }
    return total;
});

const toggleSelection = (id) => {
    if (selectedItems.value.includes(id)) selectedItems.value = selectedItems.value.filter(item => item !== id);
    else selectedItems.value.push(id);
};

const openModal = () => {
    if (selectedItems.value.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Oops...',
            text: 'Silakan pilih minimal satu produk untuk di-checkout!',
            confirmButtonColor: '#2563EB',
        });
        return;
    }
    form.selected_cart_ids = selectedItems.value;
    showPaymentModal.value = true;
};

const submitOrder = () => {
    form.post(route('checkout.process'), {
        forceFormData: true, 
        onSuccess: () => {
            showPaymentModal.value = false;
            form.reset();
            selectedItems.value = [];
            Swal.fire({
                title: 'Pesanan Berhasil!',
                text: 'Silakan tunggu verifikasi admin.',
                icon: 'success',
                confirmButtonColor: '#2563EB',
            });
        }
    });
};

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n);
</script>

<template>
    <Head title="Keranjang Belanja" />

    <UserLayout>
        <div class="min-h-screen bg-[#F8FAFC] py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-8 flex items-center gap-3">
                    Keranjang Belanja
                    <span class="text-sm font-normal text-slate-500 bg-white px-3 py-1 rounded-full border border-gray-200">{{ carts.length }} Item</span>
                </h1>

                <div v-if="carts.length === 0" class="flex flex-col items-center justify-center bg-white rounded-3xl p-16 shadow-sm border border-gray-100 text-center">
                    <div class="w-24 h-24 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-4xl mb-6">🛒</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Keranjang Kosong</h3>
                    <p class="text-slate-500 mb-8 max-w-sm">Sepertinya Anda belum menambahkan layanan atau perangkat apapun.</p>
                    <Link :href="route('products.index')" class="px-8 py-3 bg-slate-900 text-white rounded-full font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">Mulai Belanja</Link>
                </div>

                <div v-else class="flex flex-col lg:flex-row gap-8">
                    <div class="w-full lg:w-3/4 space-y-4">
                        <div v-for="cart in carts" :key="cart.id" class="group bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 transition hover:shadow-md">
                            <div class="pl-2">
                                <input type="checkbox" :value="cart.id" :checked="selectedItems.includes(cart.id)" @change="toggleSelection(cart.id)" class="w-6 h-6 text-blue-600 rounded-lg border-gray-300 focus:ring-blue-500 cursor-pointer">
                            </div>
                            <div class="w-24 h-24 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0">
                                <img :src="cart.product.image" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900 truncate">{{ cart.product.name }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">{{ cart.product.type }}</span>
                                    <span class="text-sm text-slate-500">Qty: {{ cart.quantity }}</span>
                                </div>
                                <div class="text-lg font-bold text-blue-600 mt-2">{{ formatRupiah(cart.product.price) }}</div>
                            </div>
                            <Link :href="route('cart.destroy', cart.id)" method="delete" as="button" class="p-3 rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </Link>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-28">
                            <h3 class="font-bold text-slate-900 mb-6 text-lg">Ringkasan</h3>
                            <div class="flex justify-between items-center mb-4 text-sm text-slate-500">
                                <span>Item Dipilih</span>
                                <span class="font-medium text-slate-900">{{ selectedItems.length }} produk</span>
                            </div>
                            <div class="border-t border-gray-100 pt-4 flex justify-between items-end mb-8">
                                <span class="text-slate-500 font-medium">Total Tagihan</span>
                                <span class="text-2xl font-bold text-slate-900">{{ formatRupiah(grandTotal) }}</span>
                            </div>
                            <button @click="openModal" class="w-full py-4 rounded-full font-bold text-white transition-all shadow-lg shadow-slate-900/20 active:scale-95 flex justify-center items-center gap-2" :class="selectedItems.length > 0 ? 'bg-slate-900 hover:bg-slate-800' : 'bg-gray-300 cursor-not-allowed'" :disabled="selectedItems.length === 0">Checkout Sekarang &rarr;</button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showPaymentModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPaymentModal = false"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative z-10 animate-in fade-in zoom-in duration-300">
                    <div class="bg-slate-50 px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-xl text-slate-900">Konfirmasi Pesanan</h3>
                        <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 transition">✕</button>
                    </div>
                    <div class="p-8 max-h-[75vh] overflow-y-auto">
                        <div class="bg-blue-50 rounded-2xl p-5 mb-8 border border-blue-100 text-center">
                            <p class="text-xs uppercase tracking-wider font-bold text-blue-500 mb-2">Total Pembayaran</p>
                            <div class="text-3xl font-black text-slate-900 mb-4">{{ formatRupiah(grandTotal) }}</div>
                            <div class="text-sm text-slate-600 bg-white/50 py-2 rounded-lg">Transfer ke BCA: <span class="font-bold font-mono text-slate-900">123-456-7890</span></div>
                        </div>
                        <form @submit.prevent="submitOrder" class="space-y-5">
                            <div><label class="block text-sm font-bold text-slate-700 mb-1">Alamat Lengkap</label><textarea v-model="form.address" required rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 bg-gray-50"></textarea></div>
                            <div><label class="block text-sm font-bold text-slate-700 mb-1">No. WhatsApp</label><input type="text" v-model="form.phone" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 bg-gray-50"></div>
                            <div><label class="block text-sm font-bold text-slate-700 mb-1">Bukti Transfer</label><input type="file" @input="form.payment_proof = $event.target.files[0]" required accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"></div>
                            <div class="flex gap-4 pt-4">
                                <button type="button" @click="showPaymentModal = false" class="flex-1 py-3 rounded-xl font-bold text-slate-600 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit" :disabled="form.processing" class="flex-1 py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">{{ form.processing ? 'Memproses...' : 'Kirim Bukti' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>