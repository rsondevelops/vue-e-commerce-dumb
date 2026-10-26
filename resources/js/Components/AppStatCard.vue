<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, required: true },
    color: {
        type: String,
        default: 'primary', // primary | emerald | amber | rose
    },
});
</script>

<template>
    <div class="stat-card">
        <div class="stat-icon" :class="color">
            <i class="bi" :class="icon"></i>
        </div>
        <div class="stat-value">{{ value }}</div>
        <div class="stat-label">{{ label }}</div>
    </div>
</template>