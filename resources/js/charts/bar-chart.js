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
                    y: {
                        beginAtZero: true,

                        ticks: {
                        precision: 0,
                        color: '#64748B',
                        padding: 8,
                    },

                    title: {
                        display: true,
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

                    x: {
                        ticks: {
                            color: '#475569',

                            autoSkip: false,

                            maxRotation: 0,
                            minRotation: 0,

                            padding: 8,

                            font: {
                                size: 12,
                            },

                            callback: function (value) {
                                const label = this.getLabelForValue(value);

                                const words = label.split(' ');
                                const lines = [];

                                let currentLine = '';

                                words.forEach((word) => {
                                    const testLine = currentLine
                                        ? `${currentLine} ${word}`
                                        : word;

                                    if (testLine.length > 15) {
                                        if (currentLine) {
                                            lines.push(currentLine);
                                        }

                                        currentLine = word;
                                    } else {
                                        currentLine = testLine;
                                    }
                                });

                                if (currentLine) {
                                    lines.push(currentLine);
                                }

                                return lines;
                            }
                        },

                        grid: {
                            display: false,
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