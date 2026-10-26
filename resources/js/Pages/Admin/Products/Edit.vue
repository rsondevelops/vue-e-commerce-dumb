<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppInput from '@/Components/AppInput.vue';
import AppToggle from '@/Components/AppToggle.vue';

const props = defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, required: true },
});

const imagePreview = ref(props.product.image ? `/storage/${props.product.image}` : null);

const form = useForm({
    category_id: props.product.category_id,
    name: props.product.name,
    description: props.product.description ?? '',
    price: props.product.price,
    stock: props.product.stock,
    image: null,
    is_active: props.product.is_active,
});

function handleImageChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    form.image = file;

    const reader = new FileReader();
    reader.onload = (e) => (imagePreview.value = e.target.result);
    reader.readAsDataURL(file);
}

function submit() {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/products/${props.product.id}`, {
            forceFormData: true,
        });
}
</script>

<template>
    <Head title="Edit Produk" />

    <AdminLayout page-title="Edit Produk" breadcrumb="Produk / Edit">

        <div class="page-header">
            <div class="page-header-left">
                <h1>Edit Produk</h1>
                <p>Perbarui informasi produk "{{ product.name }}".</p>
            </div>
            <Link href="/admin/products" class="btn-secondary-custom mb-4">
                <i class="bi bi-arrow-left"></i> Kembali
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="row g-4">

                <div class="col-lg-8">
                    <AppCard title="Informasi Produk">
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <AppInput
                                v-model="form.name"
                                label="Nama Produk"
                                :error="form.errors.name"
                            />

                            <div>
                                <label class="form-label-custom">Kategori</label>
                                <select v-model="form.category_id" class="form-control-custom" :class="{ 'is-invalid': form.errors.category_id }">
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <div v-if="form.errors.category_id" class="form-error">{{ form.errors.category_id }}</div>
                            </div>

                            <div>
                                <label class="form-label-custom">Deskripsi</label>
                                <textarea
                                    v-model="form.description"
                                    rows="5"
                                    class="form-control-custom"
                                ></textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <AppInput v-model="form.price" type="number" label="Harga (Rp)" :error="form.errors.price" />
                                </div>
                                <div class="col-6">
                                    <AppInput v-model="form.stock" type="number" label="Stok" :error="form.errors.stock" />
                                </div>
                            </div>
                        </div>
                    </AppCard>
                </div>

                <div class="col-lg-4">
                    <AppCard title="Gambar & Status">
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div>
                                <label class="form-label-custom">Gambar Produk</label>
                                <div
                                    style="width: 100%; aspect-ratio: 1; border-radius: 10px; border: 1px dashed var(--border); overflow: hidden; background: var(--bg-page); display: flex; align-items: center; justify-content: center; margin-bottom: 10px;"
                                >
                                    <img v-if="imagePreview" :src="imagePreview" style="width: 100%; height: 100%; object-fit: cover;" />
                                    <i v-else class="bi bi-image" style="color: var(--muted); font-size: 32px;"></i>
                                </div>
                                <input
                                    type="file"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    class="form-control-custom"
                                    @change="handleImageChange"
                                />
                                <div class="form-hint">Biarkan kosong jika tidak ingin mengganti gambar.</div>
                                <div v-if="form.errors.image" class="form-error">{{ form.errors.image }}</div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label class="form-label-custom" style="margin: 0;">Status Aktif</label>
                                <AppToggle v-model="form.is_active" />
                            </div>
                        </div>
                    </AppCard>

                    <div style="margin-top: 16px;">
                        <AppButton type="submit" variant="primary" :disabled="form.processing" style="width: 100%; justify-content: center;">
                            {{ form.processing ? 'Menyimpan...' : 'Perbarui Produk' }}
                        </AppButton>
                    </div>
                </div>

            </div>
        </form>

    </AdminLayout>
</template>