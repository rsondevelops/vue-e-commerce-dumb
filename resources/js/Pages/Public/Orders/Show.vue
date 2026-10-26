<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import OrderStepper from '@/Components/OrderStepper.vue';
import AppBadge from '@/Components/AppBadge.vue';

const props = defineProps({
    order: { type: Object, required: true },
});

const paymentLabel = {
    transfer: 'Transfer Manual',
    cod: 'Bayar di Tempat (COD)',
};

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="`Pesanan ${order.order_code}`" />

    <PublicLayout>
        <div class="order-detail-page">

            <Link href="/pesanan-saya" class="back-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Pesanan Saya
            </Link>

            <div class="order-detail-header">
                <div>
                    <h1>{{ order.order_code }}</h1>
                    <p>Dibuat pada {{ formatDate(order.created_at) }}</p>
                </div>
                <AppBadge :status="order.status" />
            </div>

            <!-- STEPPER STATUS -->
            <div class="card-box" style="padding: 24px; margin-bottom: 20px;">
                <OrderStepper :status="order.status" />

                <div v-if="order.status === 'cancelled' && order.cancelled_reason" class="cancelled-reason">
                    <strong>Alasan pembatalan:</strong> {{ order.cancelled_reason }}
                </div>
            </div>

            <div class="order-detail-layout">

                <!-- ITEM PESANAN -->
                <div class="card-box" style="padding: 20px;">
                    <h3 class="detail-section-title">Item Pesanan</h3>
                    <div class="order-item-list">
                        <div v-for="item in order.items" :key="item.id" class="order-item-row">
                            <div>
                                <Link
                                    v-if="item.product"
                                    :href="`/produk/${item.product.slug}`"
                                    class="order-item-name"
                                >
                                    {{ item.product_name }}
                                </Link>
                                <span v-else class="order-item-name" style="color: var(--muted);">
                                    {{ item.product_name }}
                                    <span class="order-item-unavailable">(produk tidak tersedia lagi)</span>
                                </span>
                                <div class="order-item-qty">
                                    {{ item.qty }} × Rp {{ item.product_price.toLocaleString('id-ID') }}
                                </div>
                            </div>
                            <div class="order-item-subtotal">
                                Rp {{ item.subtotal.toLocaleString('id-ID') }}
                            </div>
                        </div>
                    </div>

                    <div class="order-total-row">
                        <span>Total Pembayaran</span>
                        <strong>{{ order.formatted_total }}</strong>
                    </div>
                </div>

                <!-- INFO PENGIRIMAN & PEMBAYARAN -->
                <div class="order-detail-side">
                    <div class="card-box" style="padding: 20px; margin-bottom: 16px;">
                        <h3 class="detail-section-title">Informasi Pengiriman</h3>
                        <div class="info-row">
                            <i class="bi bi-person"></i>
                            <span>{{ order.customer_name }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-telephone"></i>
                            <span>{{ order.customer_phone }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ order.customer_address }}</span>
                        </div>
                        <div v-if="order.notes" class="info-row">
                            <i class="bi bi-chat-left-text"></i>
                            <span>{{ order.notes }}</span>
                        </div>
                    </div>

                    <div class="card-box" style="padding: 20px;">
                        <h3 class="detail-section-title">Pembayaran</h3>
                        <div class="info-row">
                            <i class="bi bi-credit-card"></i>
                            <span>{{ paymentLabel[order.payment_method] }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.order-detail-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 24px 28px 64px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 20px;
}

.back-link:hover {
    color: var(--primary);
}

.order-detail-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
}

.order-detail-header h1 {
    font-size: 20px;
    margin-bottom: 4px;
}

.order-detail-header p {
    font-size: 12px;
    color: var(--muted);
}

.cancelled-reason {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    font-size: 13px;
    color: var(--body-text);
}

.order-detail-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}

.detail-section-title {
    font-size: 14px;
    margin-bottom: 14px;
}

/* ================================================
   ITEM LIST
================================================ */
.order-item-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 16px;
}

.order-item-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
}

.order-item-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.order-item-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    display: block;
    margin-bottom: 4px;
}

.order-item-unavailable {
    font-size: 11px;
    font-weight: 400;
    color: var(--muted);
}

.order-item-qty {
    font-size: 12px;
    color: var(--muted);
}

.order-item-subtotal {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    flex-shrink: 0;
}

.order-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 14px;
    border-top: 1.5px solid var(--border);
}

.order-total-row strong {
    font-size: 17px;
    color: var(--primary);
}

/* ================================================
   INFO ROWS
================================================ */
.info-row {
    display: flex;
    gap: 10px;
    font-size: 12.5px;
    color: var(--body-text);
    margin-bottom: 10px;
    line-height: 1.5;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-row i {
    color: var(--primary);
    margin-top: 2px;
    flex-shrink: 0;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 767px) {
    .order-detail-layout {
        grid-template-columns: 1fr;
    }
}
</style>