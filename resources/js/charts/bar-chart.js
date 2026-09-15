import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
} from 'chart.js';

Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip
);

const initializeBarCharts = () => {
    const charts = document.querySelectorAll('[data-bar-chart]');

    charts.forEach((canvas) => {
        if (Chart.getChart(canvas)) {
            return;
        }

        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');

        // Mobile menggunakan horizontal bar chart
        const isMobile = window.innerWidth < 640;

        new Chart(canvas, {
            type: 'bar',

            data: {
                labels: labels,

                datasets: [
                    {
                        data: values,

                        backgroundColor: '#086375',
                        hoverBackgroundColor: '#0A7185',

                        borderRadius: 6,
                        borderSkipped: false,

                        barPercentage: 0.55,
                        categoryPercentage: 0.75,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                // Mobile: horizontal
                // Desktop: vertical
                indexAxis: isMobile ? 'y' : 'x',

                plugins: {
                    legend: {
                        display: false,
                    },

                    tooltip: {
                        displayColors: false,

                        callbacks: {
                            title: (items) => {
                                return items[0].label;
                            },

                            label: (context) => {
                                return `${context.raw} siswa`;
                            }
                        }
                    }
                },

                scales: {
                    // ==========================================
                    // SUMBU X
                    // ==========================================
                    x: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0,
                            color: '#64748B',
                            padding: 8,

                            font: {
                                size: 12,
                            },
                        },

                        title: {
                            display: isMobile,
                            text: 'Jumlah Siswa',

                            color: '#475569',

                            font: {
                                size: 12,
                                weight: '500',
                            }
                        },

                        grid: {
                            color: '#E5E7EB',
                        },

                        border: {
                            display: false,
                        }
                    },

                    // ==========================================
                    // SUMBU Y
                    // ==========================================
                    y: {
                        beginAtZero: true,

                        ticks: {
                            color: '#475569',
                            padding: 8,

                            font: {
                                size: 12,
                            },

                            // Desktop tidak membutuhkan label kategori
                            // di sumbu Y karena kategori berada di X.
                            callback: function (value) {
                                if (isMobile) {
                                    return this.getLabelForValue(value);
                                }

                                return value;
                            }
                        },

                        title: {
                            display: false,
                        },

                        grid: {
                            display: !isMobile,
                            color: '#E5E7EB',
                        },

                        border: {
                            display: false,
                        }
                    }
                }
            }
        });
    });
};

const destroyBarCharts = () => {
    document.querySelectorAll('[data-bar-chart]').forEach((canvas) => {
        Chart.getChart(canvas)?.destroy();
    });
};

document.addEventListener('DOMContentLoaded', initializeBarCharts);
document.addEventListener('livewire:navigated', initializeBarCharts);
document.addEventListener('livewire:navigating', destroyBarCharts);