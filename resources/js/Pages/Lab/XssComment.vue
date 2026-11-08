<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({ comments: Array });

const form = useForm({
    name: '',
    message: '',
});

function submit() {
    form.post('/lab/xss', { onSuccess: () => form.reset() });
}
</script>

<template>
    <div style="max-width: 600px; margin: 40px auto; font-family: sans-serif;">
        <h2>Kolom Komentar (Lab - XSS)</h2>

        <form @submit.prevent="submit" style="margin-bottom: 24px;">
            <input v-model="form.name" placeholder="Nama" style="width: 100%; padding: 8px; margin-bottom: 8px;" />
            <textarea v-model="form.message" placeholder="Pesan" style="width: 100%; padding: 8px; margin-bottom: 8px;"></textarea>
            <button type="submit">Kirim</button>
        </form>

        <div v-for="comment in comments" :key="comment.id" style="border-bottom: 1px solid #eee; padding: 10px 0;">
            <strong>{{ comment.name }}</strong>
            <p v-html="comment.message"></p>
        </div>
    </div>
</template>