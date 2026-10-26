<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    labels: { type: Array, required: true },
    data: { type: Array, required: true },
});

const canvasRef = ref(null);
let chartInstance = null;

function renderChart() {
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: 'Penjualan',
                    data: props.data,
                    borderColor: '#400302',
                    backgroundColor: 'rgba(64, 3, 2, 0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#400302',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID'),
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + (value / 1000) + 'rb',
                    },
                    grid: { color: '#F3E6E5' },
                },
                x: {
                    grid: { display: false },
                },
            },
        },
    });
}

onMounted(renderChart);

// Jika props berubah (misal filter tanggal ditambahkan nanti), render ulang chart.
watch(() => [props.labels, props.data], renderChart);

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>

<template>
    <div style="height: 280px;">
        <canvas ref="canvasRef"></canvas>
    </div>
</template>