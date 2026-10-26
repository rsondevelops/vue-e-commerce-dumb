<script setup>
import { onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCart } from '@/composables/useCart';

const props = defineProps({
    order: { type: Object, required: true },
});

const { clearCart } = useCart();

// Bersihkan keranjang HANYA setelah halaman sukses ini benar-benar tampil —
// memastikan order sudah pasti berhasil dibuat sebelum data lokal dihapus.
onMounted(() => {
    clearCart();
});

const paymentLabel = {
    transfer: 'Transfer Manual',
    cod: 'Bayar di Tempat (COD)',
};
</script>

<template>
    <Head title="Pesanan Berhasil" />

    <PublicLayout>
        <div class="success-page">

            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1>Pesanan Berhasil Dibuat!</h1>
            <p class="success-subtitle">
                Terima kasih, pesanan Anda sedang kami proses.
            </p>

            <div class="success-order-card">
                <div class="success-order-row">
                    <span>Kode Pesanan</span>
                    <strong>{{ order.order_code }}</strong>
                </div>
                <div class="success-order-row">
                    <span>Metode Pembayaran</span>
                    <strong>{{ paymentLabel[order.payment_method] }}</strong>
                </div>
                <div class="success-order-row">
                    <span>Total Pembayaran</span>
                    <strong class="success-total">{{ order.formatted_total }}</strong>
                </div>

                <div class="success-items">
                    <div v-for="item in order.items" :key="item.id" class="success-item">
                        <span>{{ item.product_name }} ×{{ item.qty }}</span>
                        <span>Rp {{ item.subtotal.toLocaleString('id-ID') }}</span>
                    </div>
                </div>
            </div>

            <div v-if="order.payment_method === 'transfer'" class="success-instruction">
                <i class="bi bi-info-circle-fill"></i>
                Silakan transfer ke rekening yang akan kami informasikan lewat WhatsApp ke nomor <strong>{{ order.customer_phone }}</strong>. Admin akan mengkonfirmasi pesanan Anda setelah pembayaran diterima.
            </div>
            <div v-else class="success-instruction">
                <i class="bi bi-info-circle-fill"></i>
                Siapkan pembayaran tunai sejumlah <strong>{{ order.formatted_total }}</strong> saat kurir tiba di alamat Anda.
            </div>

            <div class="success-actions">
                <Link href="/pesanan-saya" class="btn-primary-custom">
                    <i class="bi bi-bag-check"></i> Lihat Pesanan Saya
                </Link>
                <Link href="/produk" class="btn-secondary-custom">
                    Lanjut Belanja
                </Link>
            </div>

        </div>
    </PublicLayout>
</template>

<style scoped>
.success-page {
    max-width: 560px;
    margin: 0 auto;
    padding: 56px 28px 64px;
    text-align: center;
}

.success-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--success-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.success-icon i {
    font-size: 34px;
    color: var(--success);
}

.success-page h1 {
    font-size: 22px;
    margin-bottom: 6px;
}

.success-subtitle {
    font-size: 14px;
    color: var(--muted);
    margin-bottom: 28px;
}

.success-order-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    text-align: left;
    margin-bottom: 20px;
}

.success-order-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: var(--body-text);
    margin-bottom: 10px;
}

.success-total {
    color: var(--primary);
    font-size: 16px;
}

.success-items {
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.success-item {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--muted);
}

.success-instruction {
    text-align: left;
    background: var(--primary-light);
    color: var(--primary);
    padding: 14px 16px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.6;
    margin-bottom: 28px;
}

.success-instruction i {
    flex-shrink: 0;
    margin-top: 2px;
}

.success-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
</style>