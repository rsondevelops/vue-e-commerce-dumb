<script setup>
import { ref, watch } from 'vue';
import { router, useForm, usePage, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';
import AppModal from '@/Components/AppModal.vue';

const props = defineProps({
    products: { type: Object, required: true },
    categories: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();

// ================================================
// FILTER & SEARCH
// ================================================
const search = ref(props.filters.search ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const status = ref(props.filters.status ?? '');

let searchTimeout = null;

function applyFilters() {
    router.get('/admin/products', {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

// Debounce search supaya tidak request ke server di SETIAP ketukan huruf
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 800);
});

watch([categoryId, status], applyFilters);

// ================================================
// HAPUS PRODUK
// ================================================
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const deleteForm = useForm({});

function openDeleteModal(product) {
    productToDelete.value = product;
    showDeleteModal.value = true;
}

function confirmDelete() {
    deleteForm.delete(`/admin/products/${productToDelete.value.id}`, {
        onSuccess: () => (showDeleteModal.value = false),
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Manajemen Produk" />

    <AdminLayout page-title="Produk" breadcrumb="Produk">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>Manajemen Produk</h1>
                <p>Kelola produk, stok, dan status ketersediaan.</p>
            </div>
            <Link href="/admin/products/create" class="btn-primary-custom mb-4">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </Link>
        </div>

        <!-- FILTER BAR -->
        <div style="display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap;">
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama produk..."
                class="form-control-custom"
                style="max-width: 260px;"
            />

            <select
                v-model="categoryId"
                class="form-control-custom"
                style="max-width: 200px;"
            >
                <option value="">Semua Kategori</option>
                <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                >
                    {{ cat.name }}
                </option>
            </select>

            <select
                v-model="status"
                class="form-control-custom"
                style="max-width: 160px;"
            >
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>

            <Link
                href="/admin/products-trashed"
                class="btn-secondary-custom"
                style="margin-left: auto;"
            >
                <i class="bi bi-trash3-fill"></i>
                Cek Sampah Produk
            </Link>
        </div>

        <AppCard>
            <AppEmptyState
                v-if="products.data.length === 0"
                icon="bi-box-seam"
                title="Tidak ada produk ditemukan"
                description="Coba ubah filter pencarian, atau tambahkan produk baru."
            >
                <template #action>
                    <Link href="/admin/products-trashed" class="btn-primary-custom">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </Link>
                </template>
            </AppEmptyState>

            <table v-else class="mini-table">
                <thead>
                    <tr>
                        <th style="width: 56px;">Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id">
                        <td>
                            <img
                                v-if="product.image"
                                :src="`/storage/${product.image}`"
                                style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover;"
                            />
                            <div v-else style="width: 38px; height: 38px; border-radius: 8px; background: var(--bg-page); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="color: var(--muted); font-size: 14px;"></i>
                            </div>
                        </td>
                        <td style="font-weight: 600; color: var(--dark);">{{ product.name }}</td>
                        <td style="color: var(--muted);">{{ product.category?.name ?? '-' }}</td>
                        <td>{{ product.formatted_price }}</td>
                        <td>
                            <span v-if="product.stock === 0" style="color: var(--danger); font-weight: 600;">Habis</span>
                            <span v-else-if="product.stock <= 5" style="color: var(--warning); font-weight: 600;">{{ product.stock }} (menipis)</span>
                            <span v-else>{{ product.stock }}</span>
                        </td>
                        <td>
                            <AppBadge :status="product.is_active ? 'active' : 'inactive'" />
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <Link :href="`/admin/products/${product.id}/edit`" class="btn-edit-custom">
                                    <i class="bi bi-pencil"></i>Edit
                                </Link>
                                <button class="btn-danger-custom" @click="openDeleteModal(product)">
                                    <i class="bi bi-trash"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- PAGINATION SEDERHANA -->
            <div v-if="products.data.length > 0" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--muted);">
                    Menampilkan {{ products.from }}–{{ products.to }} dari {{ products.total }} produk
                </span>
                <div style="display: flex; gap: 4px;">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="btn-secondary-custom"
                        :class="{ 'btn-primary-custom': link.active }"
                        style="min-width: 32px; text-align: center; padding: 6px 10px; font-size: 12px;"
                        :style="{ opacity: link.url ? 1 : 0.4, pointerEvents: link.url ? 'auto' : 'none' }"
                        preserve-scroll
                    />
                </div>
            </div>
        </AppCard>

        <!-- MODAL KONFIRMASI HAPUS -->
        <AppModal :show="showDeleteModal" title="Hapus Produk" @close="showDeleteModal = false">
            <p>
                Apakah Anda yakin ingin menghapus produk
                <strong>{{ productToDelete?.name }}</strong>?
                Produk akan dipindahkan ke Sampah dan bisa dipulihkan (lihat Chapter 11).
            </p>
            <template #footer>
                <AppButton variant="secondary" @click="showDeleteModal = false">Batal</AppButton>
                <AppButton variant="danger" :disabled="deleteForm.processing" @click="confirmDelete">
                    {{ deleteForm.processing ? 'Menghapus...' : 'Ya, Hapus' }}
                </AppButton>
            </template>
        </AppModal>

    </AdminLayout>
</template>