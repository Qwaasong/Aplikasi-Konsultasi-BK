import {
    Chart,
    DoughnutController,
    ArcElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    DoughnutController,
    ArcElement,
    Tooltip
);

const initializeRadialCharts = () => {
    const charts = document.querySelectorAll('[data-radial-chart]');

    charts.forEach((canvas) => {
        if (Chart.getChart(canvas)) {
            return;
        }

        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');

        const total = values.reduce((sum, value) => sum + Number(value), 0);

        const percentages = values.map((value) => {
            return total > 0
                ? (Number(value) / total) * 100
                : 0;
        });

        const colors = [
            '#086375',
            '#4CAF50',
            '#E0A800',
        ];

        new Chart(canvas, {
            type: 'doughnut',

            data: {
                labels: labels,

                datasets: percentages.map((percentage, index) => ({
                    data: [
                        percentage,
                        100 - percentage
                    ],

                    backgroundColor: [
                        colors[index],
                        '#E9EDF1'
                    ],

                    borderWidth: 0,

                    borderRadius: 8,

                    weight: 1,

                    circumference: 270,

                    rotation: 180,
                }))
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                cutout: '60%',

                plugins: {
                    legend: {
                        display: false,
                    },

                    tooltip: {
                        displayColors: false,

                        callbacks: {
                            title: (items) => {
                                return items[0].datasetIndex < labels.length
                                    ? labels[items[0].datasetIndex]
                                    : '';
                            },

                            label: (context) => {
                                if (context.dataIndex !== 0) {
                                    return '';
                                }

                                return `${percentages[context.datasetIndex].toFixed(1)}%`;
                            }
                        }
                    }
                }
            }
        });
    });
};

const destroyRadialCharts = () => {
    document.querySelectorAll('[data-radial-chart]').forEach((canvas) => {
        Chart.getChart(canvas)?.destroy();
    });
};

document.addEventListener('DOMContentLoaded', initializeRadialCharts);
document.addEventListener('livewire:navigated', initializeRadialCharts);
document.addEventListener('livewire:navigating', destroyRadialCharts);