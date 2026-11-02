<script setup>
import { useForm, usePage, Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
    host: '',
});

const page = usePage();
const pingResult = computed(() => page.props.ping_result);

function submit() {
    form.post('/lab/ping');
}
</script>

<template>
    <Head title="Ping Tool" />

    <div style="max-width: 600px; margin: 40px auto; font-family: sans-serif;">
        <h2>Network Ping Tool (Lab - Command Injection)</h2>

        <form @submit.prevent="submit" style="display: flex; gap: 8px; margin-bottom: 16px;">
            <input
                v-model="form.host"
                type="text"
                placeholder="contoh: google.com"
                style="flex: 1; padding: 8px;"
            />
            <button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Mengecek...' : 'Ping' }}
            </button>
        </form>

        <div v-if="pingResult" style="margin-top: 16px;">
            <p><strong>Command dieksekusi:</strong></p>
            <pre style="background: #f4f4f4; padding: 10px;">{{ pingResult.command }}</pre>

            <p><strong>Output:</strong></p>
            <pre style="background: #000; color: #0f0; padding: 10px; white-space: pre-wrap;">{{ pingResult.output }}</pre>
        </div>
    </div>
</template>