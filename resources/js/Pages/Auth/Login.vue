<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppInput from '@/Components/AppInput.vue';
import AppButton from '@/Components/AppButton.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Masuk" />

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-mark"><i class="bi bi-shop"></i></div>
                <h1>Toko Nusantara</h1>
            </div>

            <h2 class="auth-title">Masuk ke Akun Anda</h2>
            <p class="auth-subtitle">Belum punya akun? <Link href="/register">Daftar di sini</Link></p>

            <form @submit.prevent="submit" class="auth-form">
                <AppInput
                    v-model="form.email"
                    type="email"
                    label="Email"
                    placeholder="nama@email.com"
                    :error="form.errors.email"
                />
                <AppInput
                    v-model="form.password"
                    type="password"
                    label="Password"
                    placeholder="••••••••"
                    :error="form.errors.password"
                />

                <label class="auth-remember">
                    <input type="checkbox" v-model="form.remember" />
                    Ingat saya
                </label>

                <AppButton
                    type="submit"
                    variant="primary"
                    icon="bi-box-arrow-in-right"
                    :disabled="form.processing"
                    style="width: 100%; justify-content: center;"
                >
                    {{ form.processing ? 'Memproses...' : 'Masuk' }}
                </AppButton>
            </form>

            <Link href="/" class="auth-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </Link>
        </div>
    </div>
</template>

<style scoped>
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-page);
    padding: 20px;
}

.auth-card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 36px 32px;
    width: 100%;
    max-width: 400px;
}

.auth-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 28px;
}

.auth-brand-mark {
    width: 38px;
    height: 38px;
    background: var(--primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-brand-mark i {
    color: #fff;
    font-size: 19px;
}

.auth-brand h1 {
    font-size: 16px;
}

.auth-title {
    font-size: 20px;
    margin-bottom: 4px;
}

.auth-subtitle {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 24px;
}

.auth-subtitle a {
    color: var(--primary);
    font-weight: 600;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.auth-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--body-text);
    cursor: pointer;
}

.auth-back {
    display: block;
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: var(--muted);
}

.auth-back:hover {
    color: var(--primary);
}
</style>