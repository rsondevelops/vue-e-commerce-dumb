<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
        // pending | processing | shipped | completed | cancelled | active | inactive | draft
    },
});

const config = {
    pending:    { class: 'warning', icon: 'bi-hourglass-split', label: 'Menunggu' },
    processing: { class: 'info',    icon: 'bi-gear-fill',        label: 'Diproses' },
    shipped:    { class: 'info',    icon: 'bi-truck',            label: 'Dikirim' },
    completed:  { class: 'success', icon: 'bi-check-circle-fill',label: 'Selesai' },
    cancelled:  { class: 'danger',  icon: 'bi-x-circle-fill',    label: 'Dibatalkan' },
    active:     { class: 'success', icon: 'bi-check-circle-fill',label: 'Aktif' },
    inactive:   { class: 'neutral', icon: 'bi-pause-circle-fill',label: 'Nonaktif' },
    draft:      { class: 'neutral', icon: 'bi-file-earmark',     label: 'Draft' },
};

const current = computed(() => config[props.status] ?? { class: 'neutral', icon: 'bi-question-circle', label: props.status });
</script>

<template>
    <span class="badge-pill" :class="current.class">
        <i class="bi" :class="current.icon"></i>
        {{ current.label }}
    </span>
</template>