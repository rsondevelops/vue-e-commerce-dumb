<script setup>
import { ref, watch } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import AppPagination from '@/Components/AppPagination.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';

const props = defineProps({
    products: { type: Object, required: true },
    categories: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
    selectedCategory: { type: Object, default: null },
});

const search = ref(props.filters.search ?? '');
const sort = ref(props.filters.sort ?? 'terbaru');
const isMobileFilterOpen = ref(false);

let searchTimeout = null;

function applyFilters(extra = {}) {
    router.get('/produk', {
        search: search.value || undefined,
        kategori: props.filters.kategori || undefined,
        sort: sort.value || undefined,
        ...extra,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});

watch(sort, () => applyFilters());

function selectCategory(slug) {
    router.get('/produk', {
        search: search.value || undefined,
        kategori: slug || undefined,
        sort: sort.value || undefined,
    }, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="selectedCategory ? selectedCategory.name : 'Semua Produk'" />

    <PublicLayout>
        <div class="catalog-page">

            <!-- HEADER HALAMAN -->
            <div class="catalog-header">
                <h1>{{ selectedCategory ? selectedCategory.name : 'Semua Produk' }}</h1>
                <p>{{ products.total }} produk ditemukan</p>
            </div>

            <div class="catalog-layout">

                <!-- SIDEBAR FILTER (desktop) / TOGGLE (mobile) -->
                <button class="mobile-filter-toggle" @click="isMobileFilterOpen = !isMobileFilterOpen">
                    <i class="bi bi-funnel"></i> Filter Kategori
                    <i class="bi" :class="isMobileFilterOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>

                <aside class="catalog-sidebar" :class="{ 'is-open': isMobileFilterOpen }">
                    <div class="sidebar-title">Kategori</div>
                    <ul class="category-filter-list">
                        <li>
                            <button
                                class="category-filter-item"
                                :class="{ active: !filters.kategori }"
                                @click="selectCategory(null)"
                            >
                                Semua Produk
                            </button>
                        </li>
                        <li v-for="cat in categories" :key="cat.id">
                            <button
                                class="category-filter-item"
                                :class="{ active: filters.kategori === cat.slug }"
                                @click="selectCategory(cat.slug)"
                            >
                                {{ cat.name }}
                                <span class="category-filter-count">{{ cat.products_count }}</span>
                            </button>
                        </li>
                    </ul>
                </aside>

                <!-- KONTEN UTAMA -->
                <div class="catalog-main">

                    <!-- SEARCH & SORT -->
                    <div class="catalog-toolbar">
                        <div class="catalog-search">
                            <i class="bi bi-search"></i>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari produk..."
                            />
                        </div>
                        <select v-model="sort" class="form-control-custom" style="max-width: 180px;">
                            <option value="terbaru">Terbaru</option>
                            <option value="termurah">Harga Termurah</option>
                            <option value="termahal">Harga Termahal</option>
                        </select>
                    </div>

                    <!-- GRID PRODUK -->
                    <AppEmptyState
                        v-if="products.data.length === 0"
                        icon="bi-search"
                        title="Produk tidak ditemukan"
                        description="Coba ubah kata kunci pencarian atau pilih kategori lain."
                    />

                    <div v-else class="catalog-product-grid">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <AppPagination :links="products.links" />

                </div>

            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.catalog-page {
    max-width: 1180px;
    margin: 0 auto;
    padding: 32px 28px 64px;
}

.catalog-header {
    margin-bottom: 24px;
}

.catalog-header h1 {
    font-size: 26px;
    margin-bottom: 4px;
}

.catalog-header p {
    font-size: 13px;
    color: var(--muted);
}

.catalog-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 28px;
    align-items: start;
}

/* ================================================
   SIDEBAR
================================================ */
.mobile-filter-toggle {
    display: none;
}

.catalog-sidebar {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    position: sticky;
    top: 80px;
}

.sidebar-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    margin-bottom: 12px;
}

.category-filter-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin: 0;
    padding: 0;
}

.category-filter-item {
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 8px;
    border: none;
    background: transparent;
    font-size: 13px;
    color: var(--body-text);
    cursor: pointer;
    transition: .15s;
}

.category-filter-item:hover {
    background: var(--bg-page);
}

.category-filter-item.active {
    background: var(--primary-light);
    color: var(--primary);
    font-weight: 600;
}

.category-filter-count {
    font-size: 11px;
    color: var(--muted);
}

.category-filter-item.active .category-filter-count {
    color: var(--primary);
}

/* ================================================
   TOOLBAR
================================================ */
.catalog-toolbar {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.catalog-search {
    flex: 1;
    position: relative;
}

.catalog-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 14px;
}

.catalog-search input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    background: var(--white);
    color: var(--dark);
}

.catalog-search input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-light);
}

/* ================================================
   GRID PRODUK
================================================ */
.catalog-product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* ================================================
   RESPONSIVE
================================================ */
@media (max-width: 991px) {
    .catalog-layout {
        grid-template-columns: 1fr;
    }

    .mobile-filter-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 12px 16px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .catalog-sidebar {
        display: none;
        position: static;
    }

    .catalog-sidebar.is-open {
        display: block;
    }

    .catalog-product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 575px) {
    .catalog-page {
        padding: 20px 16px 40px;
    }

    .catalog-toolbar {
        flex-direction: column;
    }

    .catalog-product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>