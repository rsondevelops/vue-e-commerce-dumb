<script setup>
import { ref, watch } from 'vue';
import { router, usePage, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let searchTimeout = null;

function applyFilters() {
    router.get('/admin/orders', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch(status, applyFilters);

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Manajemen Pesanan" />

    <AdminLayout page-title="Pesanan" breadcrumb="Pesanan">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>Manajemen Pesanan</h1>
                <p>Kelola status pesanan yang masuk dari customer.</p>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap;">
            <input
                v-model="search"
                type="text"
                placeholder="Cari kode pesanan / nama customer..."
                class="form-control-custom"
                style="max-width: 280px;"
            />
            <select v-model="status" class="form-control-custom" style="max-width: 180px;">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="processing">Diproses</option>
                <option value="shipped">Dikirim</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
        </div>

        <AppCard>
            <AppEmptyState
                v-if="orders.data.length === 0"
                icon="bi-receipt"
                title="Tidak ada pesanan ditemukan"
                description="Coba ubah filter pencarian, atau tunggu pesanan baru masuk."
            />

            <table v-else class="mini-table">
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders.data" :key="order.id">
                        <td style="font-weight: 600; color: var(--dark);">{{ order.order_code }}</td>
                        <td>{{ order.customer_name }}</td>
                        <td style="color: var(--muted);">{{ formatDate(order.created_at) }}</td>
                        <td>{{ order.items_count }} produk</td>
                        <td>{{ order.formatted_total }}</td>
                        <td><AppBadge :status="order.status" /></td>
                        <td>
                            <Link :href="`/admin/orders/${order.id}`" class="btn-edit-custom">
                                <i class="bi bi-eye"></i> Detail
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="orders.data.length > 0" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid var(--border);">
                <span style="font-size: 12px; color: var(--muted);">
                    Menampilkan {{ orders.from }}–{{ orders.to }} dari {{ orders.total }} pesanan
                </span>
                <div style="display: flex; gap: 4px;">
                    <Link
                        v-for="link in orders.links"
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

    </AdminLayout>
</template>