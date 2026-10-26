<script setup>
import { ref } from 'vue';
import AppButton from '@/Components/AppButton.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppCard from '@/Components/AppCard.vue';
import AppStatCard from '@/Components/AppStatCard.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppModal from '@/Components/AppModal.vue';
import AppInput from '@/Components/AppInput.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';

const showModal = ref(false);
const inputValue = ref('');
</script>

<template>
    <div style="max-width: 900px; margin: 0 auto; padding: 40px 20px; display: flex; flex-direction: column; gap: 32px;">

        <h1>🎨 Design System Playground</h1>

        <AppAlert type="success" message="Ini contoh alert sukses, akan hilang otomatis dalam 4 detik." />

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Buttons</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <AppButton variant="primary" icon="bi-plus-lg">Tambah</AppButton>
                <AppButton variant="secondary" icon="bi-arrow-left">Kembali</AppButton>
                <AppButton variant="edit" icon="bi-pencil">Edit</AppButton>
                <AppButton variant="danger" icon="bi-trash">Hapus</AppButton>
            </div>
        </section>

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Badges</h3>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <AppBadge status="pending" />
                <AppBadge status="processing" />
                <AppBadge status="shipped" />
                <AppBadge status="completed" />
                <AppBadge status="cancelled" />
            </div>
        </section>

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Stat Cards</h3>
            <div class="stat-grid">
                <AppStatCard label="Total Pesanan" value="128" icon="bi-bag-check-fill" color="primary" />
                <AppStatCard label="Pendapatan" value="Rp 12.4jt" icon="bi-cash-stack" color="emerald" />
                <AppStatCard label="Stok Menipis" value="5" icon="bi-exclamation-triangle-fill" color="amber" />
                <AppStatCard label="Dibatalkan" value="3" icon="bi-x-circle-fill" color="rose" />
            </div>
        </section>

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Card & Table</h3>
            <AppCard title="Produk Terbaru" subtitle="Contoh isi tabel">
                <table class="mini-table">
                    <thead>
                        <tr><th>Nama</th><th>Harga</th><th>Stok</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Kopi Gayo 200gr</td><td>Rp 45.000</td><td>24</td></tr>
                        <tr><td>Batik Tulis Lasem</td><td>Rp 350.000</td><td>3</td></tr>
                    </tbody>
                </table>
            </AppCard>
        </section>

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Input & Modal</h3>
            <div style="max-width: 320px; margin-bottom: 16px;">
                <AppInput v-model="inputValue" label="Nama Produk" placeholder="Ketik sesuatu..." />
            </div>
            <AppButton @click="showModal = true">Buka Modal</AppButton>
            <AppModal :show="showModal" title="Contoh Modal" @close="showModal = false">
                <p>Ini isi modal. Nilai input di atas: <strong>{{ inputValue || '(kosong)' }}</strong></p>
                <template #footer>
                    <AppButton variant="secondary" @click="showModal = false">Tutup</AppButton>
                </template>
            </AppModal>
        </section>

        <section>
            <h3 style="margin-bottom: 12px; font-size: 14px;">Empty State</h3>
            <AppCard>
                <AppEmptyState
                    icon="bi-box-seam"
                    title="Belum ada produk"
                    description="Tambahkan produk pertama Anda untuk mulai berjualan."
                >
                    <template #action>
                        <AppButton variant="primary" icon="bi-plus-lg">Tambah Produk</AppButton>
                    </template>
                </AppEmptyState>
            </AppCard>
        </section>

    </div>
</template>