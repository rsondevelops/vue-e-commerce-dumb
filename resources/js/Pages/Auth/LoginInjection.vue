<script setup>
import { useForm, usePage, Link, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppInput from '@/Components/AppInput.vue';
import AppButton from '@/Components/AppButton.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const page = usePage();
const loginResult = computed(() => page.props.login_result);

function submit() {
    form.post('/lab/login', {
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
                <h1>Toko SQL INJECTION</h1>
            </div>

            <h2 class="auth-title">Masuk ke Akun Anda</h2>
            <p class="auth-subtitle">Belum punya akun? <Link href="/register">Daftar di sini</Link></p>

            <div
                v-if="loginResult"
                class="auth-alert"
                :class="loginResult.success ? 'auth-alert-success' : 'auth-alert-error'"
            >
                <p>{{ loginResult.message }}</p>
                <pre v-if="loginResult.query" class="auth-alert-query">{{ loginResult.query }}</pre>
            </div>

            <form @submit.prevent="submit" class="auth-form">
                <AppInput
                    v-model="form.email"
                    type="text"
                    label="Email"
                    placeholder="nama@email.com"
                    :error="form.errors.email"
                />
                <AppInput
                    v-model="form.password"
                    type="text"
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
.auth-alert {
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 20px;
    font-size: 13px;
}

.auth-alert-success {
    background: #e6f9ed;
    border: 1px solid #34d399;
    color: #065f46;
}

.auth-alert-error {
    background: #fdecec;
    border: 1px solid #f87171;
    color: #991b1b;
}

.auth-alert-query {
    margin-top: 8px;
    padding: 8px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 6px;
    font-size: 11px;
    white-space: pre-wrap;
    word-break: break-word;
}

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