<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="app-pagination">
        <template v-for="(link, index) in links" :key="index">
            <span v-if="!link.url" class="pagination-item disabled" v-html="link.label"></span>
            <Link
                v-else
                :href="link.url"
                class="pagination-item"
                :class="{ active: link.active }"
                v-html="link.label"
                preserve-scroll
            />
        </template>
    </nav>
</template>

<style scoped>
.app-pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 32px;
    flex-wrap: wrap;
}

.pagination-item {
    min-width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--white);
    color: var(--body-text);
    font-size: 13px;
    font-weight: 500;
    transition: .15s;
}

.pagination-item:not(.disabled):hover {
    border-color: var(--primary);
    color: var(--primary);
}

.pagination-item.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    font-weight: 700;
}

.pagination-item.disabled {
    opacity: .4;
    cursor: default;
}
</style>