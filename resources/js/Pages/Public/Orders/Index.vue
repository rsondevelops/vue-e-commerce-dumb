<script setup>
import { ref, watch } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppEmptyState from '@/Components/AppEmptyState.vue';
import AppPagination from '@/Components/AppPagination.vue';

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const status = ref(props.filters.status ?? '');

const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: 'pending', label: 'Menunggu' },
    { value: 'processing', label: 'Diproses' },
    { value: 'shipped', label: 'Dikirim' },
    { value: 'completed', label: 'Selesai' },
    { value: 'cancelled', label: 'Dibatalkan' },
];

watch(status, () => {
    router.get('/pesanan-saya', {
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Pesanan Saya" />

    <PublicLayout>
        <div class="orders-page">

            <div class="orders-header">
                <h1>Pesanan Saya</h1>
                <select v-model="status" class="form-control-custom" style="max-width: 200px;">
                    <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                        {{ opt.label }}
                    </option>
                </select>
            </div>

            <AppEmptyState
                v-if="orders.data.length === 0"
                icon="bi-bag-x"
                title="Belum ada pesanan"
                description="Riwayat pesanan Anda akan muncul di sini setelah checkout."
            >
                <template #action>
                    <Link href="/produk" class="btn-primary-custom">Mulai Belanja</Link>
                </template>
            </AppEmptyState>

            <div v-else class="orders-list">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="`/pesanan-saya/${order.order_code}`"
                    class="order-card"
                >
                    <div class="order-card-left">
                        <div class="order-card-code">{{ order.order_code }}</div>
                        <div class="order-card-date">
                            <i class="bi bi-calendar3"></i> {{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                        </div>
                        <div class="order-card-items">{{ order.items_count }} produk</div>
                    </div>
                    <div class="order-card-right">
                        <AppBadge :status="order.status" />
                        <div class="order-card-total">{{ order.formatted_total }}</div>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </Link>
            </div>

            <AppPagination v-if="orders.data.length > 0" :links="orders.links" />

        </div>
    </PublicLayout>
</template>

<style scoped>
.orders-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 32px 28px 64px;
}

.orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.orders-header h1 {
    font-size: 22px;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.order-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    transition: .15s;
}

.order-card:hover {
    border-color: var(--primary-light);
    transform: translateY(-1px);
}

.order-card-code {
    font-size: 14px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 4px;
}

.order-card-date {
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 2px;
}

.order-card-items {
    font-size: 12px;
    color: var(--muted);
}

.order-card-right {
    display: flex;
    align-items: center;
    gap: 14px;
}

.order-card-total {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary);
}

.order-card-right i {
    color: var(--muted);
    font-size: 14px;
}

@media (max-width: 575px) {
    .order-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .order-card-right {
        width: 100%;
        justify-content: space-between;
    }
}
</style>