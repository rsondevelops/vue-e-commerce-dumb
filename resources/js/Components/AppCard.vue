<script setup>
defineProps({
    title: {
        type: String,
        default: null,
    },
    subtitle: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="card-box">
        <div v-if="title" class="card-header-custom">
            <div>
                <div class="card-title-custom">{{ title }}</div>
                <div v-if="subtitle" class="card-subtitle-custom">{{ subtitle }}</div>
            </div>
            <div v-if="$slots.action">
                <slot name="action" />
            </div>
        </div>
        <div class="card-body-pad">
            <slot />
        </div>
    </div>
</template>