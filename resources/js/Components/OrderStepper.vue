<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const steps = [
    { key: 'pending', label: 'Pesanan Dibuat', icon: 'bi-receipt' },
    { key: 'processing', label: 'Diproses', icon: 'bi-gear-fill' },
    { key: 'shipped', label: 'Dikirim', icon: 'bi-truck' },
    { key: 'completed', label: 'Selesai', icon: 'bi-check-circle-fill' },
];

const currentIndex = computed(() =>
    steps.findIndex((step) => step.key === props.status)
);
</script>

<template>
    <!-- Alur normal: tampilkan stepper linear -->
    <div v-if="status !== 'cancelled'" class="order-stepper">
        <div
            v-for="(step, index) in steps"
            :key="step.key"
            class="stepper-item"
            :class="{
                done: index < currentIndex,
                current: index === currentIndex,
            }"
        >
            <div class="stepper-icon">
                <i class="bi" :class="index <= currentIndex ? step.icon : 'bi-circle'"></i>
            </div>
            <div class="stepper-label">{{ step.label }}</div>
            <div v-if="index < steps.length - 1" class="stepper-line"></div>
        </div>
    </div>

    <!-- Alur dibatalkan: kartu terpisah, BUKAN bagian stepper -->
    <div v-else class="order-cancelled-card">
        <i class="bi bi-x-circle-fill"></i>
        <div>
            <div class="order-cancelled-title">Pesanan Dibatalkan</div>
            <div class="order-cancelled-desc">Pesanan ini tidak dilanjutkan prosesnya.</div>
        </div>
    </div>
</template>

<style scoped>
.order-stepper {
    display: flex;
    align-items: flex-start;
    padding: 8px 0;
}

.stepper-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    text-align: center;
}

.stepper-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-page);
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 15px;
    margin-bottom: 8px;
    z-index: 1;
    transition: .2s;
}

.stepper-item.done .stepper-icon {
    background: var(--success);
    border-color: var(--success);
    color: #fff;
}

.stepper-item.current .stepper-icon {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}

.stepper-label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 500;
    max-width: 80px;
}

.stepper-item.done .stepper-label,
.stepper-item.current .stepper-label {
    color: var(--dark);
    font-weight: 600;
}

.stepper-line {
    position: absolute;
    top: 18px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: var(--border);
    z-index: 0;
}

.stepper-item.done .stepper-line {
    background: var(--success);
}

/* ================================================
   CANCELLED CARD
================================================ */
.order-cancelled-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--danger-bg);
    border-radius: 10px;
    padding: 16px;
}

.order-cancelled-card i {
    font-size: 28px;
    color: var(--danger);
}

.order-cancelled-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--danger);
}

.order-cancelled-desc {
    font-size: 12px;
    color: var(--danger);
    opacity: .8;
}

@media (max-width: 575px) {
    .stepper-label {
        font-size: 9px;
    }
}
</style>