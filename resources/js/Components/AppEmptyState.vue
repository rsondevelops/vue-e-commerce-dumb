<script setup>
defineProps({
    icon: {
        type: String,
        default: 'bi-inbox',
    },
    title: {
        type: String,
        default: 'Belum ada data',
    },
    description: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="app-empty-state">
        <i class="bi" :class="icon"></i>
        <h4>{{ title }}</h4>
        <p v-if="description">{{ description }}</p>
        <div v-if="$slots.action" class="app-empty-state-action">
            <slot name="action" />
        </div>
    </div>
</template>

<style scoped>
.app-empty-state {
    text-align: center;
    padding: 48px 20px;
}

.app-empty-state i {
    font-size: 40px;
    color: var(--muted);
    margin-bottom: 12px;
    display: block;
}

.app-empty-state h4 {
    font-size: 15px;
    color: var(--dark);
    margin-bottom: 4px;
}

.app-empty-state p {
    font-size: 13px;
    color: var(--muted);
    max-width: 320px;
    margin: 0 auto;
}

.app-empty-state-action {
    margin-top: 16px;
}
</style>