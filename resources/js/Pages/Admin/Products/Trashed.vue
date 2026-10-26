<script setup>
import { ref } from 'vue';
import { router, useForm, usePage, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppModal from '@/Components/AppModal.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';

defineProps({
    products: { type: Object, required: true },
});

const page = usePage();

// ================================================
// RESTORE
// ================================================
const restoreForm = useForm({});

function restoreProduct(product) {
    restoreForm.post(`/admin/products/${product.id}/restore`, {
        preserveScroll: true,
    });
}

// ================================================
// FORCE DELETE (konfirmasi ganda)
// ================================================
const showForceDeleteModal = ref(false);
const productToForceDelete = ref(null);
const confirmText = ref('');
const forceDeleteForm = useForm({});

function openForceDeleteModal(product) {
    productToForceDelete.value = product;
    confirmText.value = '';
    showForceDeleteModal.value = true;
}

function confirmForceDelete() {
    forceDeleteForm.delete(`/admin/products/${productToForceDelete.value.id}/force-delete`, {
        onSuccess: () => (showForceDeleteModal.value = false),
        preserveScroll: true,
    });
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Sampah Produk" />

    <AdminLayout page-title="Sampah Produk" breadcrumb="Produk / Sampah">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>Sampah Produk</h1>
                <p>Produk yang dihapus akan tersimpan di sini sebelum dihapus permanen.</p>
            </div>
            <Link href="/admin/products" class="btn-secondary-custom mb-4">
                <i class="bi bi-arrow-left"></i> Kembali ke Produk
            </Link>
        </div>

        <AppCard>
            <AppEmptyState
                v-if="products.data.length === 0"
                icon="bi-trash3"
                title="Sampah kosong"
                description="Tidak ada produk yang dihapus saat ini."
            />

            <table v-else class="mini-table">
                <thead>
                    <tr>
                        <th style="width: 56px;">Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Dihapus Pada</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id">
                        <td>
                            <img
                                v-if="product.image"
                                :src="`/storage/${product.image}`"
                                style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover; opacity: .6;"
                            />
                            <div v-else style="width: 38px; height: 38px; border-radius: 8px; background: var(--bg-page); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="color: var(--muted); font-size: 14px;"></i>
                            </div>
                        </td>
                        <td style="font-weight: 600; color: var(--dark);">{{ product.name }}</td>
                        <td style="color: var(--muted);">{{ product.category?.name ?? '-' }}</td>
                        <td style="color: var(--muted); font-size: 12px;">{{ formatDate(product.deleted_at) }}</td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button
                                    class="btn-edit-custom"
                                    :disabled="restoreForm.processing"
                                    @click="restoreProduct(product)"
                                >
                                    <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                </button>
                                <button class="btn-danger-custom" @click="openForceDeleteModal(product)">
                                    <i class="bi bi-trash3-fill"></i> Hapus Permanen
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </AppCard>

        <!-- MODAL KONFIRMASI GANDA — FORCE DELETE -->
        <AppModal :show="showForceDeleteModal" title="Hapus Permanen" @close="showForceDeleteModal = false">
            <div style="display: flex; gap: 12px; padding: 12px; background: var(--danger-bg); border-radius: 8px; margin-bottom: 16px;">
                <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger); font-size: 20px; flex-shrink: 0;"></i>
                <p style="font-size: 13px; color: var(--danger); margin: 0;">
                    Tindakan ini <strong>TIDAK BISA DIBATALKAN</strong>. Produk
                    <strong>{{ productToForceDelete?.name }}</strong> dan gambarnya akan hilang selamanya.
                </p>
            </div>

            <label class="form-label-custom">
                Ketik <strong>HAPUS</strong> untuk konfirmasi:
            </label>
            <input
                v-model="confirmText"
                type="text"
                class="form-control-custom"
                placeholder="Ketik HAPUS di sini"
            />

            <template #footer>
                <AppButton variant="secondary" @click="showForceDeleteModal = false">Batal</AppButton>
                <AppButton
                    variant="danger"
                    :disabled="confirmText !== 'HAPUS' || forceDeleteForm.processing"
                    @click="confirmForceDelete"
                >
                    {{ forceDeleteForm.processing ? 'Menghapus...' : 'Hapus Permanen' }}
                </AppButton>
            </template>
        </AppModal>

    </AdminLayout>
</template>