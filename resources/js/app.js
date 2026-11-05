
import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const graficas = [];

    // Lee los colores de las variables CSS actuales.
    function obtenerColor(variable, respaldo) {
        const valor = getComputedStyle(document.documentElement)
            .getPropertyValue(variable)
            .trim();

        return valor || respaldo;
    }

    function actualizarColores() {
        const tema = document.documentElement.getAttribute('data-theme');
        const oscuro = tema === 'oscuro';

        const colorTexto = obtenerColor('--on-surface', oscuro ? '#e3e6ea' : '#0b1c30');
        const colorBorde = obtenerColor('--outline-variant', oscuro ? '#434944' : '#bccac0');
        const colorFondo = obtenerColor('--surface', oscuro ? '#0f1216' : '#f8f9ff');

        // Colores propios del informe 50/30/20.
        const colorNecesidades = obtenerColor('--need', '#2563eb');
        const colorDeseos = obtenerColor('--want', '#7c3aed');
        const colorAhorros = obtenerColor('--save', '#059669');

        // El color principal cambia entre cuenta estándar y familiar.
        const colorPrincipal = obtenerColor('--primary', '#006948');

        graficas.forEach(({ chart, tipo }) => {
            if (tipo === 'distribucion') {
                chart.data.datasets[0].backgroundColor = [
                    colorNecesidades,
                    colorDeseos,
                    colorAhorros
                ];

                chart.data.datasets[0].borderColor = colorFondo;
            }

            if (tipo === 'categorias') {
    const canvasCategorias = chart.canvas;
    const datosCategorias = JSON.parse(
        canvasCategorias.dataset.chartData || '[]'
    );

    chart.data.datasets[0].backgroundColor = datosCategorias.map(item => {
        switch (item.bloque) {
            case 'necesidades':
                return colorNecesidades;
            case 'discrecionales':
            case 'deseos':
                return colorDeseos;
            case 'ahorro':
                return colorAhorros;
            default:
                return colorPrincipal;
        }
    });

    chart.data.datasets[0].borderColor =
        chart.data.datasets[0].backgroundColor;
}

            // Actualiza textos, ejes, leyendas y líneas.
            chart.options.plugins.legend.labels.color = colorTexto;
            chart.options.plugins.tooltip.backgroundColor = colorFondo;
            chart.options.plugins.tooltip.titleColor = colorTexto;
            chart.options.plugins.tooltip.bodyColor = colorTexto;
            chart.options.plugins.tooltip.borderColor = colorBorde;
            chart.options.plugins.tooltip.borderWidth = 1;

            if (chart.options.scales) {
                Object.values(chart.options.scales).forEach(escala => {
                    escala.ticks.color = colorTexto;
                    escala.grid.color = colorBorde;
                });
            }

            chart.update('none');
        });
    }

    // Gráfica de distribución del informe 50/30/20.
    const canvasDistribucion = document.getElementById('grafica-distribucion');

    if (canvasDistribucion) {
        const datos = JSON.parse(
            canvasDistribucion.dataset.chartData || '[]'
        );

        if (datos.length > 0) {
            const chart = new Chart(canvasDistribucion, {
                type: 'doughnut',
                data: {
                    labels: datos.map(item => item.nombre),
                    datasets: [{
                        data: datos.map(item => Number(item.monto)),
                        backgroundColor: [],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: obtenerColor('--on-surface', '#0b1c30'),
                                padding: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const valor = Number(context.raw || 0);

                                    return `${context.label}: ${valor.toLocaleString('es-CO', {
                                        style: 'currency',
                                        currency: 'COP',
                                        maximumFractionDigits: 0
                                    })}`;
                                }
                            }
                        }
                    }
                }
            });

            graficas.push({ chart, tipo: 'distribucion' });
        }
    }

    // Gráfica de gastos por categoría.
    const canvasCategorias = document.getElementById('grafica-categorias-chart');

    if (canvasCategorias) {
        const datos = JSON.parse(
            canvasCategorias.dataset.chartData || '[]'
        );

        if (datos.length > 0) {
            const chart = new Chart(canvasCategorias, {
                type: 'bar',
                data: {
                    labels: datos.map(item => item.nombre),
                    datasets: [{
                        label: 'Dinero gastado',
                        data: datos.map(item => Number(item.monto)),
                        backgroundColor: '',
                        borderRadius: 6,
                        maxBarThickness: 38
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback(value) {
                                    return '$' + Number(value).toLocaleString('es-CO');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                            labels: {
                                color: obtenerColor('--on-surface', '#0b1c30')
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    return Number(context.raw || 0).toLocaleString('es-CO', {
                                        style: 'currency',
                                        currency: 'COP',
                                        maximumFractionDigits: 0
                                    });
                                }
                            }
                        }
                    }
                }
            });

            graficas.push({ chart, tipo: 'categorias' });
        }
    }

    // Aplica los colores iniciales.
    actualizarColores();

    // Detecta cambios de tema y actualiza las gráficas automáticamente.
    const observadorTema = new MutationObserver(() => {
        actualizarColores();
    });

    observadorTema.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme', 'data-account-type']
    });

    // Si cambian las variables de color por otra clase del documento,
    // se puede llamar actualizarColores() para refrescar las gráficas.
});
