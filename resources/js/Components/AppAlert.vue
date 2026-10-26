<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success', // success | error
    },
    message: {
        type: String,
        required: true,
    },
    autoHide: {
        type: Boolean,
        default: true,
    },
});

const visible = ref(true);

onMounted(() => {
    if (props.autoHide) {
        setTimeout(() => {
            visible.value = false;
        }, 4000);
    }
});
</script>

<template>
    <Transition name="fade">
        <div v-if="visible" class="alert-flash" :class="type">
            <i class="bi" :class="type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'"></i>
            {{ message }}
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>