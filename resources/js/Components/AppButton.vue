<script setup>
defineProps({
    variant: {
        type: String,
        default: 'primary', // primary | secondary | danger | edit
    },
    icon: {
        type: String,
        default: null, // contoh: 'bi-plus-lg'
    },
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
});

const variantClass = {
    primary: 'btn-primary-custom',
    secondary: 'btn-secondary-custom',
    danger: 'btn-danger-custom',
    edit: 'btn-edit-custom',
};
</script>

<template>
    <a v-if="href" :href="href" :class="variantClass[variant]">
        <i v-if="icon" :class="['bi', icon]"></i>
        <slot />
    </a>
    <button v-else :type="type" :class="variantClass[variant]">
        <i v-if="icon" :class="['bi', icon]"></i>
        <slot />
    </button>
</template>