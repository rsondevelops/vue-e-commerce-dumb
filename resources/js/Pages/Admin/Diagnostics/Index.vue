<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const result = computed(() => page.props.flash?.ping_result);

const form = useForm({
    host: '127.0.0.1',
});

function submit() {
    form.post(route('admin.diagnostics.ping'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="container-fluid py-4">
        <div class="mb-4">
            <h3 class="fw-bold mb-1">Server Diagnostics</h3>
            <p class="text-muted mb-0">
                Check server connectivity using Ping.
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Ping Host</h5>

                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">
                            Hostname / IP Address
                        </label>

                        <input
                            v-model="form.host"
                            type="text"
                            class="form-control"
                            placeholder="example.com or 127.0.0.1"
                            :class="{ 'is-invalid': form.errors.host }"
                        />

                        <div
                            v-if="form.errors.host"
                            class="invalid-feedback"
                        >
                            {{ form.errors.host }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="form.processing"
                    >
                        <span
                            v-if="form.processing"
                            class="spinner-border spinner-border-sm me-2"
                        ></span>

                        {{ form.processing ? 'Checking...' : 'Check Connection' }}
                    </button>
                </form>
            </div>
        </div>

        <div v-if="result" class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-semibold mb-0">Ping Result</h5>

                    <span
                        class="badge"
                        :class="result.success ? 'bg-success' : 'bg-danger'"
                    >
                        {{ result.success ? 'Connected' : 'Failed' }}
                    </span>
                </div>

                <p class="text-muted mb-2">
                    Host: {{ result.host }}
                </p>

                <pre class="bg-dark text-light rounded p-3 mb-0"
                    style="white-space: pre-wrap; overflow-wrap: anywhere;"
                >{{ result.output || result.error || 'No output returned.' }}</pre>
            </div>
        </div>
    </div>
</template>