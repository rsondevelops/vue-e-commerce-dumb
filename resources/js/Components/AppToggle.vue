<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label class="app-toggle">
        <input
            type="checkbox"
            :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)"
        />
        <span class="app-toggle-slider"></span>
    </label>
</template>

<style scoped>
.app-toggle {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
    cursor: pointer;
}

.app-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.app-toggle-slider {
    position: absolute;
    inset: 0;
    background: var(--border);
    border-radius: 22px;
    transition: .2s;
}

.app-toggle-slider::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    left: 3px;
    top: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .2s;
}

.app-toggle input:checked + .app-toggle-slider {
    background: var(--primary);
}

.app-toggle input:checked + .app-toggle-slider::before {
    transform: translateX(18px);
}
</style>