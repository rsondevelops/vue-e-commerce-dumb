<script setup>
import { ref } from 'vue';
import { useForm, usePage, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppCard from '@/Components/AppCard.vue';
import AppButton from '@/Components/AppButton.vue';
import AppBadge from '@/Components/AppBadge.vue';
import AppAlert from '@/Components/AppAlert.vue';
import AppModal from '@/Components/AppModal.vue';
import OrderStepper from '@/Components/OrderStepper.vue';

const props = defineProps({
    order: { type: Object, required: true },
    allowedNextStatuses: { type: Array, default: () => [] },
});

const page = usePage();

const statusLabel = {
    processing: 'Diproses',
    shipped: 'Dikirim',
    completed: 'Selesai',
};

// ================================================
// UPDATE STATUS
// ================================================
const updateForm = useForm({ status: '' });

function updateStatus(newStatus) {
    updateForm.status = newStatus;
    updateForm.patch(`/admin/orders/${props.order.id}/status`, {
        preserveScroll: true,
    });
}

// ================================================
// BATALKAN PESANAN
// ================================================
const showCancelModal = ref(false);
const cancelForm = useForm({ cancelled_reason: '' });

function submitCancel() {
    cancelForm.post(`/admin/orders/${props.order.id}/cancel`, {
        preserveScroll: true,
        onSuccess: () => {
            showCancelModal.value = false;
            cancelForm.reset();
        },
    });
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
}

const canCancel = props.allowedNextStatuses.includes('cancelled');
const paymentLabel = { transfer: 'Transfer Manual', cod: 'Bayar di Tempat (COD)' };
</script>

<template>
    <Head :title="`Pesanan ${order.order_code}`" />

    <AdminLayout page-title="Detail Pesanan" breadcrumb="Pesanan / Detail">

        <AppAlert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" />
        <AppAlert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" />

        <div class="page-header">
            <div class="page-header-left">
                <h1>{{ order.order_code }}</h1>
                <p>Dibuat oleh {{ order.user?.name ?? order.customer_name }} pada {{ formatDate(order.created_at) }}</p>
            </div>
            <Link href="/admin/orders" class="btn-secondary-custom mb-4">
                <i class="bi bi-arrow-left"></i> Kembali
            </Link>
        </div>

        <!-- STEPPER + AKSI STATUS -->
        <AppCard style="margin-bottom: 20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px;">
                <AppBadge :status="order.status" />
            </div>

            <OrderStepper :status="order.status" />

            <div v-if="order.status === 'cancelled' && order.cancelled_reason" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border); font-size:13px;">
                <strong>Alasan pembatalan:</strong> {{ order.cancelled_reason }}
            </div>

            <!-- TOMBOL AKSI TRANSISI STATUS -->
            <div v-if="allowedNextStatuses.length > 0" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:20px; padding-top:20px; border-top:1px solid var(--border);">
                <AppButton
                    v-for="next in allowedNextStatuses.filter(s => s !== 'cancelled')"
                    :key="next"
                    variant="primary"
                    icon="bi-circle"
                    :disabled="updateForm.processing"
                    @click="updateStatus(next)"
                >
                    Pesanan akan "{{ statusLabel[next] }}"
                </AppButton>

                <AppButton
                    v-if="canCancel"
                    variant="danger"
                    icon="bi-x-circle"
                    @click="showCancelModal = true"
                >
                    Batalkan Pesanan
                </AppButton>
            </div>
            <p v-else style="margin-top:20px; padding-top:20px; border-top:1px solid var(--border); font-size:12px; color:var(--muted);">
                Pesanan ini sudah mencapai status akhir, tidak ada aksi lebih lanjut.
            </p>
        </AppCard>

        <div class="order-admin-layout">

            <!-- ITEM PESANAN -->
            <AppCard title="Item Pesanan">
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div v-for="item in order.items" :key="item.id" style="display:flex; justify-content:space-between; padding-bottom:14px; border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px; font-weight:600; color:var(--dark);">{{ item.product_name }}</div>
                            <div style="font-size:12px; color:var(--muted);">{{ item.qty }} × Rp {{ item.product_price.toLocaleString('id-ID') }}</div>
                        </div>
                        <div style="font-size:13px; font-weight:700; color:var(--primary);">
                            Rp {{ item.subtotal.toLocaleString('id-ID') }}
                        </div>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; padding-top:16px; margin-top:2px;">
                    <span style="font-size:13px;">Total Pembayaran</span>
                    <strong style="font-size:17px; color:var(--primary);">{{ order.formatted_total }}</strong>
                </div>
            </AppCard>

            <!-- INFO CUSTOMER -->
            <div>
                <AppCard title="Informasi Pengiriman" style="margin-bottom:16px;">
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:12.5px; color:var(--body-text);">
                        <div><i class="bi bi-person" style="color:var(--primary); margin-right:8px;"></i>{{ order.customer_name }}</div>
                        <div><i class="bi bi-telephone" style="color:var(--primary); margin-right:8px;"></i>{{ order.customer_phone }}</div>
                        <div><i class="bi bi-geo-alt" style="color:var(--primary); margin-right:8px;"></i>{{ order.customer_address }}</div>
                        <div v-if="order.notes"><i class="bi bi-chat-left-text" style="color:var(--primary); margin-right:8px;"></i>{{ order.notes }}</div>
                    </div>
                </AppCard>

                <AppCard title="Pembayaran">
                    <div style="font-size:12.5px; color:var(--body-text);">
                        <i class="bi bi-credit-card" style="color:var(--primary); margin-right:8px;"></i>
                        {{ paymentLabel[order.payment_method] }}
                    </div>
                </AppCard>
            </div>

        </div>

        <!-- MODAL BATALKAN PESANAN -->
        <AppModal :show="showCancelModal" title="Batalkan Pesanan" @close="showCancelModal = false">
            <p style="font-size:13px; margin-bottom:16px;">
                Stok produk pada pesanan ini akan <strong>dikembalikan otomatis</strong>. Tindakan ini tidak bisa dibatalkan.
            </p>
            <label class="form-label-custom">Alasan Pembatalan</label>
            <textarea
                v-model="cancelForm.cancelled_reason"
                rows="3"
                class="form-control-custom"
                :class="{ 'is-invalid': cancelForm.errors.cancelled_reason }"
                placeholder="Contoh: Stok tidak tersedia, permintaan customer, dsb."
            ></textarea>
            <div v-if="cancelForm.errors.cancelled_reason" class="form-error">{{ cancelForm.errors.cancelled_reason }}</div>

            <template #footer>
                <AppButton variant="secondary" @click="showCancelModal = false">Batal</AppButton>
                <AppButton variant="danger" :disabled="cancelForm.processing" @click="submitCancel">
                    {{ cancelForm.processing ? 'Memproses...' : 'Ya, Batalkan Pesanan' }}
                </AppButton>
            </template>
        </AppModal>

    </AdminLayout>
</template>

<style scoped>
.order-admin-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}

@media (max-width: 767px) {
    .order-admin-layout {
        grid-template-columns: 1fr;
    }
}
</style>