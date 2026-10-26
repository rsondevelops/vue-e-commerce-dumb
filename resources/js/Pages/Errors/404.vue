<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    message: {
        type: String,
        default: 'Modul dalam proses pembuatan.',
    },
});
</script>

<template>
    <Head title="Halaman tidak Ada" />

    <div class="error-page">
        <div class="error-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1>404</h1>
        <h2>Halaman tidak Ada</h2>
        <p>{{ message }}</p>
        <Link href="/" class="btn-primary-custom">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </Link>
    </div>
</template>

<style scoped>
.error-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: var(--bg-page);
    padding: 20px;
}

.error-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--danger-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.error-icon i {
    font-size: 36px;
    color: var(--danger);
}

.error-page h1 {
    font-size: 48px;
    color: var(--primary);
    line-height: 1;
    margin-bottom: 8px;
}

.error-page h2 {
    font-size: 20px;
    margin-bottom: 8px;
}

.error-page p {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 24px;
    max-width: 360px;
}
</style>