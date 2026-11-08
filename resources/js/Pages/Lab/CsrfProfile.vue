<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ user: Object });

const form = useForm({ email: '' });
const page = usePage();
const status = computed(() => page.props.flash?.status);

function submit() {
    form.post('/lab/csrf/update-email');
}
</script>

<template>
    <div style="max-width: 500px; margin: 40px auto; font-family: sans-serif;">
        <h2>Profil Saya (Lab - CSRF)</h2>
        <p>Email saat ini: <strong>{{ user.email }}</strong></p>

        <p v-if="status" style="color: green;">{{ status }}</p>

        <form @submit.prevent="submit">
            <input v-model="form.email" type="email" placeholder="Email baru" style="width: 100%; padding: 8px; margin-bottom: 8px;" />
            <button type="submit">Ubah Email</button>
        </form>
    </div>
</template>