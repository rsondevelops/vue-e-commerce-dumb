<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppInput from '@/Components/AppInput.vue';
import AppButton from '@/Components/AppButton.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Daftar Akun" />

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-mark"><i class="bi bi-shop"></i></div>
                <h1>Toko Nusantara</h1>
            </div>

            <h2 class="auth-title">Buat Akun Baru</h2>
            <p class="auth-subtitle">Sudah punya akun? <Link href="/login">Masuk di sini</Link></p>

            <form @submit.prevent="submit" class="auth-form">
                <AppInput v-model="form.name" label="Nama Lengkap" placeholder="Nama Anda" :error="form.errors.name" />
                <AppInput v-model="form.email" type="email" label="Email" placeholder="nama@email.com" :error="form.errors.email" />
                <AppInput v-model="form.phone" label="Nomor HP (opsional)" placeholder="0812xxxxxxxx" :error="form.errors.phone" />
                <AppInput v-model="form.password" type="password" label="Password" placeholder="Minimal 8 karakter" :error="form.errors.password" />
                <AppInput v-model="form.password_confirmation" type="password" label="Konfirmasi Password" placeholder="Ulangi password" />

                <AppButton
                    type="submit"
                    variant="primary"
                    icon="bi-person-plus"
                    :disabled="form.processing"
                    style="width: 100%; justify-content: center;"
                >
                    {{ form.processing ? 'Memproses...' : 'Daftar Sekarang' }}
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
    max-width: 420px;
}

.auth-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
.auth-brand-mark { width: 38px; height: 38px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.auth-brand-mark i { color: #fff; font-size: 19px; }
.auth-brand h1 { font-size: 16px; }
.auth-title { font-size: 20px; margin-bottom: 4px; }
.auth-subtitle { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
.auth-subtitle a { color: var(--primary); font-weight: 600; }
.auth-form { display: flex; flex-direction: column; gap: 16px; }
.auth-back { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted); }
.auth-back:hover { color: var(--primary); }
</style>