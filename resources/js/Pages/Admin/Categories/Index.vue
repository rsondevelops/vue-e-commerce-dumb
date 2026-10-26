<script setup>
import { ref, watch } from 'vue';
import { useForm, usePage, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppModal from '@/Components/AppModal.vue';
import AppInput from '@/Components/AppInput.vue';
import AppToggle from '@/Components/AppToggle.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const page = usePage();

// ================================================
// STATE MODAL FORM (dipakai untuk Tambah & Edit)
// ================================================
const showFormModal = ref(false);
const editingCategory = ref(null); // null = mode Tambah, terisi = mode Edit
const imagePreview = ref(null);

const form = useForm({
    name: '',
    image: null,
    is_active: true,
});

function openCreateModal() {
    editingCategory.value = null;
    imagePreview.value = null;
    form.reset();
    form.clearErrors();
    showFormModal.value = true;
}

function openEditModal(category) {
    editingCategory.value = category;
    imagePreview.value = category.image ? `/storage/${category.image}` : null;
    form.clearErrors();
    form.name = category.name;
    form.image = null; // gambar baru kosong dulu, kecuali admin pilih file baru
    form.is_active = category.is_active;
    showFormModal.value = true;
}

function handleImageChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    form.image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
}

function submitForm() {
    if (editingCategory.value) {
        // Laravel tidak bisa membaca file lewat method PUT murni,
        // jadi kita "palsukan" PUT lewat POST + field _method.
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/categories/${editingCategory.value.id}`, {
                forceFormData: true,
                onSuccess: () => (showFormModal.value = false),
            });
    } else {
        form.post('/admin/categories', {
            forceFormData: true,
            onSuccess: () => (showFormModal.value = false),
        });
    }
}

// ================================================
// STATE MODAL HAPUS
// ================================================
const showDeleteModal = ref(false);
const categoryToDelete = ref(null);
const deleteForm = useForm({});

function openDeleteModal(category) {
    categoryToDelete.value = category;
    showDeleteModal.value = true;
}

function confirmDelete() {
    deleteForm.delete(`/admin/categories/${categoryToDelete.value.id}`, {
        onSuccess: () => (showDeleteModal.value = false),
    });
}
</script>

<template>
    <Head title="Manajemen Kategori" />

    <AdminLayout page-title="Kategori" breadcrumb="Kategori">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>Manajemen Kategori</h1>
                <p>Kelola kategori produk yang tampil di katalog publik.</p>
            </div>
            <AppButton variant="primary" icon="bi-plus-lg" class="mb-4" @click="openCreateModal">
                Tambah Kategori
            </AppButton>
        </div>

        <AppCard>
            <AppEmptyState
                v-if="categories.length === 0"
                icon="bi-tags"
                title="Belum ada kategori"
                description="Tambahkan kategori pertama untuk mulai mengelompokkan produk Anda."
            >
                <template #action>
                    <AppButton variant="primary" icon="bi-plus-lg" @click="openCreateModal">
                        Tambah Kategori
                    </AppButton>
                </template>
            </AppEmptyState>

            <table v-else class="mini-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Gambar</th>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Jumlah Produk</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td>
                            <img
                                v-if="category.image"
                                :src="`/storage/${category.image}`"
                                style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;"
                            />
                            <div
                                v-else
                                style="width: 40px; height: 40px; border-radius: 8px; background: var(--bg-page); display: flex; align-items: center; justify-content: center;"
                            >
                                <i class="bi bi-image" style="color: var(--muted);"></i>
                            </div>
                        </td>
                        <td style="font-weight: 600; color: var(--dark);">{{ category.name }}</td>
                        <td style="color: var(--muted);">{{ category.slug }}</td>
                        <td>{{ category.products_count }} produk</td>
                        <td>
                            <AppBadge :status="category.is_active ? 'active' : 'inactive'" />
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button class="btn-edit-custom" @click="openEditModal(category)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn-danger-custom" @click="openDeleteModal(category)">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </AppCard>

        <!-- MODAL TAMBAH / EDIT -->
        <AppModal
            :show="showFormModal"
            :title="editingCategory ? 'Edit Kategori' : 'Tambah Kategori'"
            @close="showFormModal = false"
        >
            <form
                @submit.prevent="submitForm"
                style="display: flex; flex-direction: column; gap: 16px;"
            >
                <AppInput
                    v-model="form.name"
                    label="Nama Kategori"
                    placeholder="Contoh: Kopi Nusantara"
                    :error="form.errors.name"
                />

                <div>
                    <label class="form-label-custom">Gambar Kategori</label>

                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 64px; height: 64px; border-radius: 10px; border: 1px solid var(--border); overflow: hidden; flex-shrink: 0; background: var(--bg-page); display: flex; align-items: center; justify-content: center;"
                        >
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                style="width: 100%; height: 100%; object-fit: cover;"
                            />

                            <i
                                v-else
                                class="bi bi-image"
                                style="color: var(--muted); font-size: 22px;"
                            ></i>
                        </div>

                        <input
                            type="file"
                            accept="image/png, image/jpeg, image/jpg, image/webp"
                            class="form-control-custom"
                            @change="handleImageChange"
                        />
                    </div>

                    <div class="form-hint">
                        Format JPG/PNG/WEBP, maksimal 2MB.
                    </div>

                    <div
                        v-if="form.errors.image"
                        class="form-error"
                    >
                        {{ form.errors.image }}
                    </div>
                </div>

                <div
                    style="display: flex; align-items: center; justify-content: space-between;"
                >
                    <label class="form-label-custom" style="margin: 0;">
                        Status Aktif
                    </label>

                    <AppToggle v-model="form.is_active" />
                </div>
            </form>

            <template #footer>
                <AppButton
                    variant="secondary"
                    @click="showFormModal = false"
                >
                    Batal
                </AppButton>

                <AppButton
                    variant="primary"
                    :disabled="form.processing"
                    @click="submitForm"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- MODAL KONFIRMASI HAPUS -->
        <AppModal :show="showDeleteModal" title="Hapus Kategori" @close="showDeleteModal = false">
            <p>
                Apakah Anda yakin ingin menghapus kategori
                <strong>{{ categoryToDelete?.name }}</strong>?
                Tindakan ini tidak bisa dibatalkan.
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