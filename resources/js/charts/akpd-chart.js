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

document.addEventListener('DOMContentLoaded', () => {

    const charts = document.querySelectorAll('[data-akpd-chart]');

    charts.forEach((canvas) => {

        const data = JSON.parse(canvas.dataset.data);

        const labels = Object.keys(data);

        const ya = labels.map(label => data[label].ya);
        const tidak = labels.map(label => data[label].tidak);

        const yaPercentage = labels.map(label => {
            const total = data[label].ya + data[label].tidak;

            return total > 0
                ? (data[label].ya / total) * 100
                : 0;
        });

        const tidakPercentage = labels.map(label => {
            const total = data[label].ya + data[label].tidak;

            return total > 0
                ? (data[label].tidak / total) * 100
                : 0;
        });


        new Chart(canvas, {
            type: 'bar',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: 'Ya',
                        data: ya,
                        backgroundColor: '#FF6B6B',
                        borderRadius: 6,
                        barThickness: 18,
                    },
                    {
                        label: 'Tidak',
                        data: tidak,
                        backgroundColor: '#086375',
                        borderRadius: 6,
                        barThickness: 18,
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                indexAxis: 'y',

                layout: {
                    padding: {
                        right: 90,
                    }
                },

                scales: {
                    x: {
                        beginAtZero: true,

                        suggestedMax: 35,

                        ticks: {
                            precision: 0,
                        },

                        grid: {
                            display: true,
                            color: '#E5E7EB',
                        }
                    },

                    y: {
                        grid: {
                            display: false,
                        },

                        ticks: {
                            font: {
                                size: 13,
                            }
                        }
                    }
                },

                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {

                                const index = context.dataIndex;
                                const datasetIndex = context.datasetIndex;

                                const value = context.raw;

                                const percentage = datasetIndex === 0
                                    ? yaPercentage[index]
                                    : tidakPercentage[index];

                                return `${value} siswa (${percentage.toFixed(1)}%)`;
                            }
                        }
                    }
                }
            },

            plugins: [
                {
                    id: 'akpdLabels',

                    afterDatasetsDraw(chart) {

                        const { ctx } = chart;

                        ctx.save();

                        chart.data.datasets.forEach((dataset, datasetIndex) => {

                            const meta = chart.getDatasetMeta(datasetIndex);

                            meta.data.forEach((bar, index) => {

                                const value = dataset.data[index];

                                const percentage = datasetIndex === 0
                                    ? yaPercentage[index]
                                    : tidakPercentage[index];

                                ctx.fillStyle = '#6B7280';

                                ctx.font = '12px Arial';

                                ctx.textAlign = 'left';

                                ctx.textBaseline = 'middle';

                                ctx.fillText(
                                    `${percentage.toFixed(1)}% (${value})`,
                                    bar.x + 8,
                                    bar.y
                                );
                            });

                        });

                        ctx.restore();
                    }
                }
            ]
        });

    });

});