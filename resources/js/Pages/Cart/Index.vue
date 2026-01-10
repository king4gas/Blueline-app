<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

// === FITUR AGAR NAVBAR TIDAK REFRESH ===
defineOptions({ layout: UserLayout });

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
            icon: 'info', title: 'Oops...', text: 'Silakan pilih minimal satu produk untuk di-checkout!',
            background: '#1e293b', color: '#fff', confirmButtonColor: '#0891b2',
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
                title: 'Pesanan Berhasil!', text: 'Silakan tunggu verifikasi admin.', icon: 'success',
                background: '#1e293b', color: '#fff', confirmButtonColor: '#0891b2',
            });
        },
        onError: () => {
             Swal.fire({ title: 'Gagal!', text: 'Pastikan semua data terisi dengan benar.', icon: 'error', background: '#1e293b', color: '#fff', });
        }
    });
};

const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
</script>

<template>
    <Head title="Keranjang Belanja" />

    <div class="min-h-screen bg-slate-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <h1 class="text-3xl font-black text-white mb-8 flex items-center gap-3">
                <span class="text-cyan-400">🛒</span> Keranjang Belanja
                <span class="text-sm font-bold text-cyan-400 bg-slate-900 border border-slate-700 px-3 py-1 rounded-full">{{ carts.length }} Item</span>
            </h1>

            <div v-if="carts.length === 0" class="flex flex-col items-center justify-center bg-slate-900 rounded-[2rem] p-16 border border-slate-800 border-dashed text-center">
                <div class="w-24 h-24 bg-slate-800 text-cyan-500 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">🛒</div>
                <h3 class="text-xl font-bold text-white mb-2">Keranjang Kosong</h3>
                <p class="text-slate-400 mb-8 max-w-sm">Sepertinya Anda belum menambahkan layanan atau perangkat apapun.</p>
                <Link :href="route('products.index')" class="px-8 py-3 bg-cyan-600 text-white rounded-full font-bold hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/50">Mulai Belanja</Link>
            </div>

            <div v-else class="flex flex-col lg:flex-row gap-8">
                
                <div class="w-full lg:w-3/4 space-y-4">
                    <div v-for="cart in carts" :key="cart.id" class="group bg-slate-900 p-4 rounded-3xl border border-slate-800 flex items-center gap-4 transition hover:border-cyan-500/30 hover:shadow-[0_0_20px_rgba(8,145,178,0.1)]">
                        <div class="pl-2">
                            <input type="checkbox" :value="cart.id" :checked="selectedItems.includes(cart.id)" @change="toggleSelection(cart.id)" class="w-6 h-6 text-cyan-600 bg-slate-950 border-slate-700 rounded-lg focus:ring-cyan-500 cursor-pointer transition checked:bg-cyan-600">
                        </div>
                        <div class="w-24 h-24 bg-slate-950 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-700">
                            <img :src="cart.product.image" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-white truncate text-lg">{{ cart.product.name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-800 text-cyan-400 uppercase border border-slate-700">{{ cart.product.type }}</span>
                                <span class="text-sm text-slate-500">Qty: {{ cart.quantity }}</span>
                            </div>
                            <div class="text-lg font-black text-cyan-400 mt-2">{{ formatRupiah(cart.product.price) }}</div>
                        </div>
                        <Link :href="route('cart.destroy', cart.id)" method="delete" as="button" class="p-3 rounded-full text-slate-600 hover:bg-slate-950 hover:text-red-500 transition" title="Hapus Item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </Link>
                    </div>
                </div>

                <div class="w-full lg:w-1/4">
                    <div class="bg-slate-900 p-6 rounded-3xl border border-slate-800 sticky top-24 shadow-xl">
                        <h3 class="font-bold text-white mb-6 text-lg">Ringkasan</h3>
                        <div class="flex justify-between items-center mb-4 text-sm text-slate-400">
                            <span>Item Dipilih</span>
                            <span class="font-medium text-white">{{ selectedItems.length }} produk</span>
                        </div>
                        <div class="border-t border-slate-800 pt-4 flex justify-between items-end mb-8">
                            <span class="text-slate-500 font-medium">Total Tagihan</span>
                            <span class="text-2xl font-black text-cyan-400">{{ formatRupiah(grandTotal) }}</span>
                        </div>
                        <button @click="openModal" class="w-full py-4 rounded-full font-bold text-white transition-all shadow-lg active:scale-95 flex justify-center items-center gap-2" :class="selectedItems.length > 0 ? 'bg-cyan-600 hover:bg-cyan-500 shadow-cyan-900/50' : 'bg-slate-800 text-slate-500 cursor-not-allowed'" :disabled="selectedItems.length === 0">Checkout Sekarang &rarr;</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showPaymentModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="showPaymentModal = false"></div>
            <div class="bg-slate-900 rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 border border-slate-800 animate-in fade-in zoom-in duration-300">
                <div class="bg-slate-800/50 px-8 py-6 border-b border-slate-700 flex justify-between items-center backdrop-blur-sm">
                    <h3 class="font-bold text-xl text-white">Konfirmasi Pesanan</h3>
                    <button @click="showPaymentModal = false" class="text-slate-500 hover:text-white transition">✕</button>
                </div>
                <div class="p-8 max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <div class="bg-slate-950 rounded-2xl p-5 mb-8 border border-slate-800 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>
                        <p class="text-xs uppercase tracking-wider font-bold text-cyan-500 mb-2">Total Pembayaran</p>
                        <div class="text-3xl font-black text-white mb-4 relative z-10">{{ formatRupiah(grandTotal) }}</div>
                        <div class="text-sm text-slate-400 bg-slate-900 py-3 rounded-xl border border-slate-800 relative z-10">Transfer ke BCA: <span class="font-bold font-mono text-cyan-400 select-all">123-456-7890</span></div>
                    </div>
                    <form @submit.prevent="submitOrder" class="space-y-5">
                        <div><label class="block text-sm font-bold text-slate-400 mb-2">Alamat Lengkap</label><textarea v-model="form.address" required rows="2" class="w-full rounded-xl bg-slate-950 border-slate-700 text-white focus:border-cyan-500 focus:ring-cyan-500 placeholder-slate-600 transition" placeholder="Jalan, No. Rumah, Kota..."></textarea></div>
                        <div><label class="block text-sm font-bold text-slate-400 mb-2">No. WhatsApp</label><input type="text" v-model="form.phone" required class="w-full rounded-xl bg-slate-950 border-slate-700 text-white focus:border-cyan-500 focus:ring-cyan-500 placeholder-slate-600 transition" placeholder="0812..."></div>
                        <div>
                            <label class="block text-sm font-bold text-slate-400 mb-2">Bukti Transfer</label>
                            <input type="file" @input="form.payment_proof = $event.target.files[0]" required accept="image/*" class="block w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 transition cursor-pointer border border-slate-700 rounded-xl bg-slate-950">
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showPaymentModal = false" class="flex-1 py-3 rounded-xl font-bold text-slate-400 hover:bg-slate-800 transition">Batal</button>
                            <button type="submit" :disabled="form.processing" class="flex-1 py-3 rounded-xl font-bold bg-cyan-600 text-white hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/50 disabled:opacity-70">{{ form.processing ? 'Memproses...' : 'Kirim Bukti' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>