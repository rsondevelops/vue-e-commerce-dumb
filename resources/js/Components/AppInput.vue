<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="form-label-custom">{{ label }}</label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            class="form-control-custom"
            :class="{ 'is-invalid': error }"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <div v-if="error" class="form-error">{{ error }}</div>
    </div>
</template>