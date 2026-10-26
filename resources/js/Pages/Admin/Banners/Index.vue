<script setup>
import { ref } from 'vue';
import { router, useForm, usePage, Head } from '@inertiajs/vue3';
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
    banners: { type: Array, required: true },
});

const page = usePage();

// ================================================
// MODAL FORM (Tambah & Edit)
// ================================================
const showFormModal = ref(false);
const editingBanner = ref(null);
const imagePreview = ref(null);

const form = useForm({
    title: '',
    subtitle: '',
    link_url: '',
    image: null,
    is_active: true,
});

function openCreateModal() {
    editingBanner.value = null;
    imagePreview.value = null;
    form.reset();
    form.clearErrors();
    showFormModal.value = true;
}

function openEditModal(banner) {
    editingBanner.value = banner;
    imagePreview.value = `/storage/${banner.image}`;
    form.clearErrors();
    form.title = banner.title;
    form.subtitle = banner.subtitle ?? '';
    form.link_url = banner.link_url ?? '';
    form.image = null;
    form.is_active = banner.is_active;
    showFormModal.value = true;
}

function handleImageChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => (imagePreview.value = e.target.result);
    reader.readAsDataURL(file);
}

function submitForm() {
    if (editingBanner.value) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/banners/${editingBanner.value.id}`, {
                forceFormData: true,
                onSuccess: () => (showFormModal.value = false),
            });
    } else {
        form.post('/admin/banners', {
            forceFormData: true,
            onSuccess: () => (showFormModal.value = false),
        });
    }
}

// ================================================
// HAPUS
// ================================================
const showDeleteModal = ref(false);
const bannerToDelete = ref(null);
const deleteForm = useForm({});

function openDeleteModal(banner) {
    bannerToDelete.value = banner;
    showDeleteModal.value = true;
}

function confirmDelete() {
    deleteForm.delete(`/admin/banners/${bannerToDelete.value.id}`, {
        onSuccess: () => (showDeleteModal.value = false),
    });
}

// ================================================
// REORDER
// ================================================
function moveUp(banner) {
    router.post(`/admin/banners/${banner.id}/move-up`, {}, { preserveScroll: true });
}

function moveDown(banner) {
    router.post(`/admin/banners/${banner.id}/move-down`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Manajemen Banner" />

    <AdminLayout page-title="Banner" breadcrumb="Banner">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>Manajemen Banner</h1>
                <p>Kelola banner yang tampil di carousel homepage.</p>
            </div>
            <AppButton variant="primary" icon="bi-plus-lg" class="mb-4" @click="openCreateModal">
                Tambah Banner
            </AppButton>
        </div>

        <AppCard>
            <AppEmptyState
                v-if="banners.length === 0"
                icon="bi-image"
                title="Belum ada banner"
                description="Tambahkan banner pertama untuk mempercantik homepage."
            >
                <template #action>
                    <AppButton variant="primary" icon="bi-plus-lg" @click="openCreateModal">
                        Tambah Banner
                    </AppButton>
                </template>
            </AppEmptyState>

            <table v-else class="mini-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th style="width: 90px;">Gambar</th>
                        <th>Judul</th>
                        <th>Tautan</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(banner, index) in banners" :key="banner.id">
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <button
                                    class="btn-secondary-custom"
                                    style="padding: 2px 6px; font-size: 11px;"
                                    :disabled="index === 0"
                                    @click="moveUp(banner)"
                                > Up
                                    <i class="bi bi-chevron-up"></i>
                                </button>
                                <button
                                    class="btn-secondary-custom"
                                    style="padding: 2px 6px; font-size: 11px;"
                                    :disabled="index === banners.length - 1"
                                    @click="moveDown(banner)"
                                > Down
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <img
                                :src="`/storage/${banner.image}`"
                                style="width: 64px; height: 40px; border-radius: 6px; object-fit: cover;"
                            />
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--dark);">{{ banner.title }}</div>
                            <div v-if="banner.subtitle" style="font-size: 11px; color: var(--muted);">{{ banner.subtitle }}</div>
                        </td>
                        <td style="color: var(--muted); font-size: 12px;">{{ banner.link_url || '-' }}</td>
                        <td>
                            <AppBadge :status="banner.is_active ? 'active' : 'inactive'" />
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <button class="btn-edit-custom" @click="openEditModal(banner)">
                                    <i class="bi bi-pencil"></i>Edit
                                </button>
                                <button class="btn-danger-custom" @click="openDeleteModal(banner)">
                                    <i class="bi bi-trash"></i>Delete
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
            :title="editingBanner ? 'Edit Banner' : 'Tambah Banner'"
            @close="showFormModal = false"
        >
            <form @submit.prevent="submitForm" style="display: flex; flex-direction: column; gap: 16px;">

                <AppInput v-model="form.title" label="Judul Banner" placeholder="Contoh: Diskon Spesial Kopi" :error="form.errors.title" />
                <AppInput v-model="form.subtitle" label="Subjudul (opsional)" placeholder="Kalimat pendukung singkat" :error="form.errors.subtitle" />
                <AppInput v-model="form.link_url" label="Tautan Tujuan (opsional)" placeholder="/produk?kategori=kopi-nusantara" :error="form.errors.link_url" />

                <div>
                    <label class="form-label-custom">Gambar Banner</label>
                    <div
                        style="width: 100%; aspect-ratio: 16/6; border-radius: 10px; border: 1px dashed var(--border); overflow: hidden; background: var(--bg-page); display: flex; align-items: center; justify-content: center; margin-bottom: 8px;"
                    >
                        <img v-if="imagePreview" :src="imagePreview" style="width: 100%; height: 100%; object-fit: cover;" />
                        <i v-else class="bi bi-image" style="color: var(--muted); font-size: 28px;"></i>
                    </div>
                    <input
                        type="file"
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="form-control-custom"
                        @change="handleImageChange"
                    />
                    <div class="form-hint">Rasio disarankan 16:6 (lebar), maksimal 2MB.</div>
                    <div v-if="form.errors.image" class="form-error">{{ form.errors.image }}</div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <label class="form-label-custom" style="margin: 0;">Status Aktif</label>
                    <AppToggle v-model="form.is_active" />
                </div>
            </form>

            <template #footer>
                <AppButton variant="secondary" @click="showFormModal = false">Batal</AppButton>
                <AppButton variant="primary" :disabled="form.processing" @click="submitForm">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- MODAL KONFIRMASI HAPUS -->
        <AppModal :show="showDeleteModal" title="Hapus Banner" @close="showDeleteModal = false">
            <p>Apakah Anda yakin ingin menghapus banner <strong>{{ bannerToDelete?.title }}</strong>?</p>
            <template #footer>
                <AppButton variant="secondary" @click="showDeleteModal = false">Batal</AppButton>
                <AppButton variant="danger" :disabled="deleteForm.processing" @click="confirmDelete">
                    {{ deleteForm.processing ? 'Menghapus...' : 'Ya, Hapus' }}
                </AppButton>
            </template>
        </AppModal>

    </AdminLayout>
</template>