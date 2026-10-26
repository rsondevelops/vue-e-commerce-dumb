<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Link :href="`/produk/${product.slug}`" class="product-card">
        <div class="product-card-image">
            <img
                v-if="product.image"
                :src="`/storage/${product.image}`"
                :alt="product.name"
            />
            <div v-else class="product-card-image-placeholder">
                <i class="bi bi-image"></i>
            </div>
            <span v-if="product.stock <= 5" class="product-card-badge">
                Stok Terbatas
            </span>
        </div>
        <div class="product-card-body">
            <div class="product-card-category">{{ product.category?.name ?? '-' }}</div>
            <h3 class="product-card-name">{{ product.name }}</h3>
            <div class="product-card-price">{{ product.formatted_price }}</div>
        </div>
    </Link>
</template>

<style scoped>
.product-card {
    display: block;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: .2s;
}

.product-card:hover {
    border-color: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(36, 21, 18, .08);
}

.product-card-image {
    position: relative;
    aspect-ratio: 1;
    background: var(--bg-page);
    overflow: hidden;
}

.product-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-card-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-card-image-placeholder i {
    font-size: 32px;
    color: var(--muted);
}

.product-card-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: var(--warning-bg);
    color: var(--warning);
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
}

.product-card-body {
    padding: 14px;
}

.product-card-category {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 4px;
}

.product-card-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 36px;
}

.product-card-price {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
}
</style>    