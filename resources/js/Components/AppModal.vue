<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: null,
    },
});

defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-fade">
            <div v-if="show" class="app-modal-backdrop" @click.self="$emit('close')">
                <div class="app-modal-box">
                    <div class="app-modal-header">
                        <h3>{{ title }}</h3>
                        <button class="app-modal-close" @click="$emit('close')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="app-modal-body">
                        <slot />
                    </div>
                    <div v-if="$slots.footer" class="app-modal-footer">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.app-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(18, 20, 36, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}

.app-modal-box {
    background: var(--white);
    border-radius: var(--radius);
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
}

.app-modal-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.app-modal-header h3 {
    font-size: 16px;
}

.app-modal-close {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    border: none;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
    transition: .15s;
}

.app-modal-close:hover {
    background: var(--bg-page);
    color: var(--dark);
}

.app-modal-body {
    padding: 20px;
}

.app-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.modal-fade-enter-active, .modal-fade-leave-active {
    transition: opacity .2s;
}
.modal-fade-enter-from, .modal-fade-leave-to {
    opacity: 0;
}
</style>