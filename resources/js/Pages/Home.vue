<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
    banners: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    featuredProducts: { type: Array, default: () => [] },
});

const categoryIcons = {
    'Kopi Nusantara': 'bi-cup-hot-fill',
    'Elektronik': 'bi-lightning-charge-fill',
    'Gadget & Smartphone': 'bi-phone-fill',
    'Aksesoris Gadget': 'bi-headphones',
    'Peralatan Kantor': 'bi-briefcase-fill',
    'Peralatan Rumah': 'bi-house-fill',
    'Mainan & Hobi': 'bi-controller',
    'Perlengkapan Sekolah': 'bi-book-fill',
    'Alat Pertukangan': 'bi-tools',
    'Tanaman & Gardening': 'bi-flower1',
};

function iconFor(categoryName) {
    return categoryIcons[categoryName] ?? 'bi-shop';
}
</script>

<template>
    <Head title="Beranda" />

    <PublicLayout>

        <!-- ================================================
             HERO BANNER CAROUSEL
        ================================================ -->
        <section v-if="banners.length > 0" class="hero-section">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button
                        v-for="(banner, index) in banners"
                        :key="banner.id"
                        type="button"
                        data-bs-target="#heroCarousel"
                        :data-bs-slide-to="index"
                        :class="{ active: index === 0 }"
                    ></button>
                </div>
                <div class="carousel-inner">
                    <div
                        v-for="(banner, index) in banners"
                        :key="banner.id"
                        class="carousel-item"
                        :class="{ active: index === 0 }"
                    >
                        <component :is="banner.link_url ? Link : 'div'" :href="banner.link_url">
                            <img :src="`/storage/${banner.image}`" class="hero-image" :alt="banner.title" />
                            <div class="hero-caption">
                                <h2>{{ banner.title }}</h2>
                                <p v-if="banner.subtitle">{{ banner.subtitle }}</p>
                            </div>
                        </component>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </section>

        <!-- Fallback jika belum ada banner sama sekali -->
        <section v-else class="hero-section-empty">
            <div class="wrap-inner">
                <i class="bi bi-shop"></i>
                <h1>Toko Nusantara</h1>
                <p>Kekayaan produk asli daerah, langsung dari pengrajin lokal.</p>
            </div>
        </section>

        <!-- ================================================
             GRID KATEGORI
        ================================================ -->
        <section class="section-block">
            <div class="section-inner">
                <div class="section-heading">
                    <h2>Belanja per Kategori</h2>
                    <p>Temukan produk sesuai kategori favorit Anda</p>
                </div>

                <div v-if="categories.length > 0" class="category-grid">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="`/produk?kategori=${category.slug}`"
                        class="category-tile"
                    >
                        <div class="category-tile-icon">
                            <i class="bi" :class="iconFor(category.name)"></i>
                        </div>
                        <div class="category-tile-name">{{ category.name }}</div>
                        <div class="category-tile-count">{{ category.products_count }} produk</div>
                    </Link>
                </div>

                <p v-else style="color: var(--muted); text-align: center;">
                    Belum ada kategori tersedia saat ini.
                </p>
            </div>
        </section>

        <!-- ================================================
             PRODUK UNGGULAN
        ================================================ -->
        <section class="section-block" style="background: var(--white);">
            <div class="section-inner">
                <div class="section-heading" style="display:flex; align-items:flex-end; justify-content:space-between;">
                    <div>
                        <h2>Produk Unggulan</h2>
                        <p>Pilihan terbaru dari Toko Nusantara</p>
                    </div>
                    <Link href="/produk" class="btn-secondary-custom" style="text-decoration:none;">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </Link>
                </div>

                <div v-if="featuredProducts.length > 0" class="product-grid">
                    <ProductCard
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <p v-else style="color: var(--muted); text-align: center;">
                    Belum ada produk tersedia saat ini.
                </p>
            </div>
        </section>

    </PublicLayout>
</template>

<style scoped>
/* ================================================
   HERO
================================================ */
.hero-section {
    position: relative;
}

.hero-image {
    width: 100%;
    height: 420px;
    object-fit: cover;
}

.hero-caption {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(36,21,18,.75), rgba(36,21,18,.1) 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 40px;
    color: #fff;
}

.hero-caption h2 {
    font-size: 28px;
    color: #fff;
    margin-bottom: 6px;
}

.hero-caption p {
    font-size: 14px;
    color: rgba(255,255,255,.85);
    max-width: 480px;
}

.hero-section-empty {
    background: var(--dark);
    padding: 90px 20px;
    text-align: center;
    color: #fff;
}

.hero-section-empty i {
    font-size: 40px;
    color: var(--primary-light);
    margin-bottom: 12px;
    display: block;
}

.hero-section-empty h1 {
    color: #fff;
    font-size: 28px;
    margin-bottom: 8px;
}

.hero-section-empty p {
    color: rgba(255,255,255,.7);
    font-size: 14px;
}

/* ================================================
   SECTION UMUM
================================================ */
.section-block {
    padding: 56px 0;
}

.section-inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 28px;
}

.section-heading {
    margin-bottom: 28px;
}

.section-heading h2 {
    font-size: 24px;
    margin-bottom: 4px;
}

.section-heading p {
    font-size: 14px;
    color: var(--muted);
}

/* ================================================
   GRID KATEGORI
================================================ */
.category-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
}

.category-tile {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px 16px;
    text-align: center;
    transition: .2s;
}

.category-tile:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
}

.category-tile-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: var(--primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.category-tile-icon i {
    font-size: 24px;
    color: var(--primary);
}

.category-tile-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 2px;
}

.category-tile-count {
    font-size: 11px;
    color: var(--muted);
}

/* ================================================
   GRID PRODUK
================================================ */
.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 991px) {
    .category-grid { grid-template-columns: repeat(3, 1fr); }
    .product-grid { grid-template-columns: repeat(3, 1fr); }
    .hero-image { height: 320px; }
    .hero-caption h2 { font-size: 22px; }
}

@media (max-width: 575px) {
    .category-grid { grid-template-columns: repeat(2, 1fr); }
    .product-grid { grid-template-columns: repeat(2, 1fr); }
    .hero-image { height: 240px; }
    .section-block { padding: 40px 0; }
}
</style>