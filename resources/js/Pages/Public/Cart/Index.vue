<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCart } from '@/composables/useCart';

const { items, removeItem, updateQty, totalPrice, formattedTotalPrice } = useCart();

function formatPrice(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
}

function subtotal(item) {
    return formatPrice(item.price * item.qty);
}

const isEmpty = computed(() => items.value.length === 0);
</script>

<template>
    <Head title="Keranjang Belanja" />

    <PublicLayout>
        <div class="cart-page">

            <h1>Keranjang Belanja</h1>

            <!-- KERANJANG KOSONG -->
            <div v-if="isEmpty" class="cart-empty">
                <i class="bi bi-cart-x"></i>
                <h2>Keranjang Anda masih kosong</h2>
                <p>Yuk, mulai jelajahi produk-produk Toko Nusantara.</p>
                <Link href="/produk" class="btn-primary-custom"> Mulai Belanja
                </Link>
            </div>

            <!-- ISI KERANJANG -->
            <div v-else class="cart-layout">

                <div class="cart-items">
                    <div v-for="item in items" :key="item.id" class="cart-item">
                        <Link :href="`/produk/${item.slug}`" class="cart-item-image">
                            <img
                                v-if="item.image"
                                :src="`/storage/${item.image}`"
                                :alt="item.name"
                            />
                            <div v-else class="cart-item-image-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        </Link>

                        <div class="cart-item-info">
                            <Link :href="`/produk/${item.slug}`" class="cart-item-name">{{ item.name }}</Link>
                            <div class="cart-item-price">{{ formatPrice(item.price) }}</div>

                            <div class="cart-item-controls">
                                <div class="quantity-control">
                                    <button type="button" @click="updateQty(item.id, item.qty - 1)" :disabled="item.qty <= 1">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span>{{ item.qty }}</span>
                                    <button type="button" @click="updateQty(item.id, item.qty + 1)" :disabled="item.qty >= item.stock">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>

                                <button type="button" class="cart-item-remove" @click="removeItem(item.id)">
                                    <i class="bi bi-trash3"></i> Hapus
                                </button>
                            </div>

                            <div v-if="item.qty >= item.stock" class="cart-item-stock-note">
                                <i class="bi bi-info-circle"></i> Jumlah maksimal sesuai stok tersedia.
                            </div>
                        </div>

                        <div class="cart-item-subtotal">{{ subtotal(item) }}</div>
                    </div>
                </div>

                <!-- RINGKASAN -->
                <div class="cart-summary">
                    <h3>Ringkasan Belanja</h3>
                    <div class="cart-summary-row">
                        <span>Total ({{ items.length }} produk)</span>
                        <strong>{{ formattedTotalPrice }}</strong>
                    </div>
                    <Link href="/checkout" class="btn-primary-custom cart-checkout-btn">
                        Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
                    </Link>
                    <p class="cart-summary-note">
                        Harga & stok akan diverifikasi ulang saat checkout.
                    </p>
                </div>

            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.cart-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 32px 28px 64px;
}

.cart-page h1 {
    font-size: 24px;
    margin-bottom: 24px;
}

/* ================================================
   EMPTY STATE
================================================ */
.cart-empty {
    text-align: center;
    padding: 64px 20px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

.cart-empty i {
    font-size: 48px;
    color: var(--muted);
    margin-bottom: 16px;
    display: block;
}

.cart-empty h2 {
    font-size: 18px;
    margin-bottom: 6px;
}

.cart-empty p {
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 20px;
}

/* ================================================
   LAYOUT
================================================ */
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    align-items: start;
}

/* ================================================
   CART ITEM
================================================ */
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cart-item {
    display: flex;
    gap: 14px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px;
}

.cart-item-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
    background: var(--bg-page);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-image-placeholder i {
    font-size: 24px;
    color: var(--muted);
}

.cart-item-info {
    flex: 1;
    min-width: 0;
}

.cart-item-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    display: block;
    margin-bottom: 4px;
}

.cart-item-price {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 10px;
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 16px;
}

.quantity-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.quantity-control button {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--bg-page);
    color: var(--dark);
    cursor: pointer;
    transition: .15s;
}

.quantity-control button:hover:not(:disabled) {
    background: var(--primary-light);
    color: var(--primary);
}

.quantity-control button:disabled {
    opacity: .4;
    cursor: not-allowed;
}

.quantity-control span {
    width: 36px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
}

.cart-item-remove {
    display: flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    color: var(--danger);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.cart-item-remove:hover {
    text-decoration: underline;
}

.cart-item-stock-note {
    font-size: 11px;
    color: var(--warning);
    margin-top: 6px;
}

.cart-item-subtotal {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary);
    flex-shrink: 0;
    align-self: center;
}

/* ================================================
   SUMMARY
================================================ */
.cart-summary {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    position: sticky;
    top: 80px;
}

.cart-summary h3 {
    font-size: 15px;
    margin-bottom: 16px;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: var(--body-text);
    margin-bottom: 20px;
}

.cart-summary-row strong {
    font-size: 17px;
    color: var(--primary);
}

.cart-checkout-btn {
    width: 100%;
    justify-content: center;
    margin-bottom: 10px;
}

.cart-summary-note {
    font-size: 11px;
    color: var(--muted);
    text-align: center;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 767px) {
    .cart-layout {
        grid-template-columns: 1fr;
    }

    .cart-item {
        flex-wrap: wrap;
    }

    .cart-item-subtotal {
        width: 100%;
        text-align: right;
        margin-top: 4px;
    }
}
</style>