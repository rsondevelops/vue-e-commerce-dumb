<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppStatCard from '@/Components/AppStatCard.vue';
import AppCard from '@/Components/AppCard.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';
import SalesChart from '@/Components/SalesChart.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    salesChart: { type: Object, required: true },
    recentOrders: { type: Array, default: () => [] },
    lowStockProducts: { type: Array, default: () => [] },
});

function formatRupiah(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
}
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout page-title="Dashboard" breadcrumb="Dashboard">

        <div class="page-header-left" style="margin-bottom: 20px;">
            <h1>Selamat Datang, Admin 👋</h1>
            <p>Berikut ringkasan performa Toko Nusantara.</p>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <AppStatCard
                label="Total Pesanan"
                :value="stats.totalOrders"
                icon="bi-bag-check-fill"
                color="primary"
            />
            <AppStatCard
                label="Pendapatan Bulan Ini"
                :value="formatRupiah(stats.revenueThisMonth)"
                icon="bi-cash-stack"
                color="emerald"
            />
            <AppStatCard
                label="Stok Menipis"
                :value="stats.lowStockCount"
                icon="bi-exclamation-triangle-fill"
                color="amber"
            />
            <AppStatCard
                label="Dibatalkan Bulan Ini"
                :value="stats.cancelledOrders"
                icon="bi-x-circle-fill"
                color="rose"
            />
        </div>

        <!-- GRAFIK PENJUALAN -->
        <AppCard title="Penjualan 7 Hari Terakhir" subtitle="Total pendapatan dari pesanan selesai" style="margin-bottom: 20px;">
            <SalesChart :labels="salesChart.labels" :data="salesChart.data" />
        </AppCard>

        <div class="dashboard-bottom-grid">

            <!-- PESANAN TERBARU -->
            <AppCard title="Pesanan Terbaru">
                <template #action>
                    <Link href="/admin/orders" class="view-all">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </Link>
                </template>

                <AppEmptyState
                    v-if="recentOrders.length === 0"
                    icon="bi-receipt"
                    title="Belum ada pesanan"
                />

                <table v-else class="mini-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in recentOrders" :key="order.id">
                            <td>
                                <Link :href="`/admin/orders/${order.id}`" style="font-weight:600; color:var(--dark);">
                                    {{ order.order_code }}
                                </Link>
                            </td>
                            <td>{{ order.customer_name }}</td>
                            <td>{{ order.formatted_total }}</td>
                            <td><AppBadge :status="order.status" /></td>
                        </tr>
                    </tbody>
                </table>
            </AppCard>

            <!-- STOK MENIPIS -->
            <AppCard title="Produk Stok Menipis">
                <template #action>
                    <Link href="/admin/products" class="view-all">
                        Kelola <i class="bi bi-arrow-right"></i>
                    </Link>
                </template>

                <AppEmptyState
                    v-if="lowStockProducts.length === 0"
                    icon="bi-check-circle"
                    title="Semua stok aman"
                    description="Tidak ada produk dengan stok menipis saat ini."
                />

                <table v-else class="mini-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in lowStockProducts" :key="product.id">
                            <td>
                                <Link :href="`/admin/products/${product.id}/edit`" style="font-weight:600; color:var(--dark);">
                                    {{ product.name }}
                                </Link>
                            </td>
                            <td>
                                <span style="color: var(--warning); font-weight: 700;">{{ product.stock }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </AppCard>

        </div>

    </AdminLayout>
</template>

<style scoped>
.dashboard-bottom-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 20px;
    align-items: start;
}

@media (max-width: 991px) {
    .dashboard-bottom-grid {
        grid-template-columns: 1fr;
    }
}
</style>