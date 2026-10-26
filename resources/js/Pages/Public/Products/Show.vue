<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { useCart } from '@/composables/useCart';

const props = defineProps({
    product: { type: Object, required: true },
    relatedProducts: { type: Array, default: () => [] },
});

const { addItem } = useCart();

const quantity = ref(1);
const justAdded = ref(false);

const isOutOfStock = computed(() => props.product.stock === 0);
const maxQuantity = computed(() => props.product.stock);

function increaseQty() {
    if (quantity.value < maxQuantity.value) {
        quantity.value++;
    }
}

function decreaseQty() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

function addToCart() {
    addItem(props.product, quantity.value);

    justAdded.value = true;
    setTimeout(() => (justAdded.value = false), 2500);

    quantity.value = 1;
}
</script>

<template>
    <Head :title="product.name" />

    <PublicLayout>
        <div class="detail-page">

            <!-- BREADCRUMB -->
            <div class="detail-breadcrumb">
                <Link href="/">Beranda</Link>
                <i class="bi bi-chevron-right"></i>
                <Link href="/produk">Produk</Link>
                <i class="bi bi-chevron-right"></i>
                <Link :href="`/produk?kategori=${product.category?.slug}`">{{ product.category?.name }}</Link>
                <i class="bi bi-chevron-right"></i>
                <span class="is-current">{{ product.name }}</span>
            </div>

            <div class="detail-layout">

                <!-- GAMBAR PRODUK -->
                <div class="detail-image">
                    <img
                        v-if="product.image"
                        :src="`/storage/${product.image}`"
                        :alt="product.name"
                    />
                    <div v-else class="detail-image-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                </div>

                <!-- INFO PRODUK -->
                <div class="detail-info">
                    <div class="detail-category">{{ product.category?.name ?? '-' }}</div>
                    <h1>{{ product.name }}</h1>
                    <div class="detail-price">{{ product.formatted_price }}</div>

                    <div class="detail-stock">
                        <span v-if="isOutOfStock" class="stock-badge out">
                            <i class="bi bi-x-circle-fill"></i> Stok Habis
                        </span>
                        <span v-else-if="product.stock <= 5" class="stock-badge low">
                            <i class="bi bi-exclamation-triangle-fill"></i> Sisa {{ product.stock }} stok
                        </span>
                        <span v-else class="stock-badge available">
                            <i class="bi bi-check-circle-fill"></i> Stok Tersedia ({{ product.stock }})
                        </span>
                    </div>

                    <p class="detail-description">
                        {{ product.description || 'Tidak ada deskripsi untuk produk ini.' }}
                    </p>

                    <!-- KONTROL KUANTITAS -->
                    <div v-if="!isOutOfStock" class="detail-quantity">
                        <label>Jumlah</label>
                        <div class="quantity-control">
                            <button type="button" @click="decreaseQty" :disabled="quantity <= 1">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span>{{ quantity }}</span>
                            <button type="button" @click="increaseQty" :disabled="quantity >= maxQuantity">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- TOMBOL AKSI -->
                    <button
                        class="btn-primary-custom detail-cta"
                        :disabled="isOutOfStock"
                        @click="addToCart"
                    >
                        <i class="bi" :class="isOutOfStock ? 'bi-slash-circle' : 'bi-cart-plus-fill'"></i>
                        {{ isOutOfStock ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                    </button>

                    <Transition name="fade">
                        <div v-if="justAdded" class="detail-added-notice">
                            <i class="bi bi-check-circle-fill"></i>
                            Produk ditambahkan ke keranjang.
                            <Link href="/keranjang">Lihat Keranjang</Link>
                        </div>
                    </Transition>
                </div>

            </div>

            <!-- PRODUK TERKAIT -->
            <div v-if="relatedProducts.length > 0" class="related-section">
                <h2>Produk Terkait</h2>
                <div class="related-grid">
                    <ProductCard
                        v-for="related in relatedProducts"
                        :key="related.id"
                        :product="related"
                    />
                </div>
            </div>

        </div>
    </PublicLayout>
</template>

<style scoped>
.detail-page {
    max-width: 1180px;
    margin: 0 auto;
    padding: 24px 28px 64px;
}

/* ================================================
   BREADCRUMB
================================================ */
.detail-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.detail-breadcrumb a:hover {
    color: var(--primary);
}

.detail-breadcrumb i {
    font-size: 9px;
}

.detail-breadcrumb .is-current {
    color: var(--primary);
    font-weight: 600;
}

/* ================================================
   LAYOUT UTAMA
================================================ */
.detail-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 56px;
}

.detail-image {
    aspect-ratio: 1;
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--bg-page);
    border: 1px solid var(--border);
}

.detail-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.detail-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.detail-image-placeholder i {
    font-size: 56px;
    color: var(--muted);
}

/* ================================================
   INFO PRODUK
================================================ */
.detail-category {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 8px;
}

.detail-info h1 {
    font-size: 26px;
    margin-bottom: 10px;
}

.detail-price {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 14px;
}

.detail-stock {
    margin-bottom: 20px;
}

.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
}

.stock-badge.available { background: var(--success-bg); color: var(--success); }
.stock-badge.low { background: var(--warning-bg); color: var(--warning); }
.stock-badge.out { background: var(--danger-bg); color: var(--danger); }

.detail-description {
    font-size: 14px;
    color: var(--body-text);
    line-height: 1.75;
    margin-bottom: 24px;
    white-space: pre-line;
}

/* ================================================
   KONTROL KUANTITAS
================================================ */
.detail-quantity {
    margin-bottom: 20px;
}

.detail-quantity label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--body-text);
    margin-bottom: 8px;
}

.quantity-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
}

.quantity-control button {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--bg-page);
    color: var(--dark);
    font-size: 16px;
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
    width: 48px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
}

/* ================================================
   TOMBOL AKSI
================================================ */
.detail-cta {
    width: 100%;
    justify-content: center;
    padding: 14px;
    font-size: 14px;
}

.detail-cta:disabled {
    opacity: .5;
    cursor: not-allowed;
}

.detail-added-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding: 10px 14px;
    background: var(--success-bg);
    color: var(--success);
    border-radius: 8px;
    font-size: 13px;
}

.detail-added-notice a {
    margin-left: auto;
    font-weight: 700;
    text-decoration: underline;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity .3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* ================================================
   PRODUK TERKAIT
================================================ */
.related-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 767px) {
    .detail-layout {
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>