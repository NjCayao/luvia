<div class="container-fluid">
    <!-- Selector de período -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt mr-1"></i>
                Período de Análisis
            </h3>
        </div>
        <div class="card-body">
            <form method="get" action="<?= url('/perfil/estadisticas') ?>" class="form-inline">
                <div class="form-group mr-3">
                    <label for="period" class="mr-2">Mostrar estadísticas de:</label>
                    <select name="period" id="period" class="form-control" onchange="this.form.submit()">
                        <option value="week" <?= $stats['period'] === 'week' ? 'selected' : '' ?>>Última semana</option>
                        <option value="month" <?= $stats['period'] === 'month' ? 'selected' : '' ?>>Último mes</option>
                        <option value="year" <?= $stats['period'] === 'year' ? 'selected' : '' ?>>Último año</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de rendimiento -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-eye"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Vistas</span>
                    <span class="info-box-number"><?= $stats['total_views'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fab fa-whatsapp"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Contactos</span>
                    <span class="info-box-number"><?= $stats['total_clicks'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tasa de Conversión</span>
                    <span class="info-box-number"><?= $stats['total_conversion_rate'] ?>%</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-trophy"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tu Posición</span>
                    <span class="info-box-number">#<?= $stats['ranking_position'] ?> (Top <?= $stats['percentile'] ?>%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico principal de rendimiento -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line mr-1"></i>
                Rendimiento del Perfil
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <canvas id="performanceChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-4 text-center">
                    <span class="text-info">
                        <i class="fas fa-eye mr-1"></i> Vistas
                    </span>
                </div>
                <div class="col-md-4 text-center">
                    <span class="text-success">
                        <i class="fab fa-whatsapp mr-1"></i> Contactos
                    </span>
                </div>
                <div class="col-md-4 text-center">
                    <span class="text-danger">
                        <i class="fas fa-percentage mr-1"></i> Tasa de Conversión
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de rendimiento -->
    <div class="row">
        <div class="col-md-6">
            <!-- Promedios diarios -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator mr-1"></i>
                        Promedios Diarios
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Vistas por día:</h6>
                            <h2 class="mb-0 text-info"><?= $stats['avg_daily_views'] ?></h2>
                        </div>
                        <div class="text-right">
                            <h6 class="mb-0">Promedio de tu categoría:</h6>
                            <h2 class="mb-0 <?= $stats['avg_daily_views'] > $stats['avg_category_views'] ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($stats['avg_category_views'], 2) ?>
                                <?php if ($stats['avg_daily_views'] > $stats['avg_category_views']): ?>
                                    <small class="text-success"><i class="fas fa-arrow-up"></i> +<?= round(($stats['avg_daily_views'] / $stats['avg_category_views'] - 1) * 100, 2) ?>%</small>
                                <?php else: ?>
                                    <small class="text-danger"><i class="fas fa-arrow-down"></i> <?= round(($stats['avg_daily_views'] / $stats['avg_category_views'] - 1) * 100, 2) ?>%</small>
                                <?php endif; ?>
                            </h2>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Contactos por día:</h6>
                            <h2 class="mb-0 text-success"><?= $stats['avg_daily_clicks'] ?></h2>
                        </div>
                        <div class="text-right">
                            <h6 class="mb-0">Promedio de tu categoría:</h6>
                            <h2 class="mb-0 <?= $stats['avg_daily_clicks'] > $stats['avg_category_clicks'] ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($stats['avg_category_clicks'], 2) ?>
                                <?php if ($stats['avg_daily_clicks'] > $stats['avg_category_clicks']): ?>
                                    <small class="text-success"><i class="fas fa-arrow-up"></i> +<?= round(($stats['avg_daily_clicks'] / $stats['avg_category_clicks'] - 1) * 100, 2) ?>%</small>
                                <?php else: ?>
                                    <small class="text-danger"><i class="fas fa-arrow-down"></i> <?= round(($stats['avg_daily_clicks'] / $stats['avg_category_clicks'] - 1) * 100, 2) ?>%</small>
                                <?php endif; ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Análisis de posición -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy mr-1"></i>
                        Tu Posición en el Ranking
                    </h3>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="rankingChart" height="200"></canvas>
                    </div>
                    <div class="d-flex flex-row justify-content-center text-center">
                        <span class="mr-2">
                            <i class="fas fa-trophy text-warning"></i> Estás en el <strong>Top <?= $stats['percentile'] ?>%</strong> de perfiles
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Tarjeta de rendimiento -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Métricas de Rendimiento
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <tr>
                                <th>Métrica</th>
                                <th>Tu Perfil</th>
                                <th>Comparativa</th>
                            </tr>
                            <tr>
                                <td>Vistas totales</td>
                                <td><?= $stats['total_views'] ?></td>
                                <td>
                                    <?php 
                                    $viewsComparison = $stats['avg_category_views'] * $stats['period_days'];
                                    $viewsPercentage = $viewsComparison > 0 ? round(($stats['total_views'] / $viewsComparison) * 100, 2) : 0;
                                    ?>
                                    <div class="progress-group">
                                        <?php if ($viewsPercentage >= 100): ?>
                                            <span class="text-success">+<?= $viewsPercentage - 100 ?>% por encima del promedio</span>
                                        <?php else: ?>
                                            <span class="text-danger"><?= $viewsPercentage - 100 ?>% por debajo del promedio</span>
                                        <?php endif; ?>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar <?= $viewsPercentage >= 100 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= min(100, $viewsPercentage) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Contactos totales</td>
                                <td><?= $stats['total_clicks'] ?></td>
                                <td>
                                    <?php 
                                    $clicksComparison = $stats['avg_category_clicks'] * $stats['period_days'];
                                    $clicksPercentage = $clicksComparison > 0 ? round(($stats['total_clicks'] / $clicksComparison) * 100, 2) : 0;
                                    ?>
                                    <div class="progress-group">
                                        <?php if ($clicksPercentage >= 100): ?>
                                            <span class="text-success">+<?= $clicksPercentage - 100 ?>% por encima del promedio</span>
                                        <?php else: ?>
                                            <span class="text-danger"><?= $clicksPercentage - 100 ?>% por debajo del promedio</span>
                                        <?php endif; ?>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar <?= $clicksPercentage >= 100 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= min(100, $clicksPercentage) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Tasa de conversión</td>
                                <td><?= $stats['total_conversion_rate'] ?>%</td>
                                <td>
                                    <?php 
                                    $conversionComparison = $stats['avg_category_clicks'] > 0 && $stats['avg_category_views'] > 0 ? 
                                                           ($stats['avg_category_clicks'] / $stats['avg_category_views']) * 100 : 0;
                                    $conversionPercentage = $conversionComparison > 0 ? 
                                                           round(($stats['total_conversion_rate'] / $conversionComparison) * 100, 2) : 0;
                                    ?>
                                    <div class="progress-group">
                                        <?php if ($conversionPercentage >= 100): ?>
                                            <span class="text-success">+<?= $conversionPercentage - 100 ?>% por encima del promedio</span>
                                        <?php else: ?>
                                            <span class="text-danger"><?= $conversionPercentage - 100 ?>% por debajo del promedio</span>
                                        <?php endif; ?>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar <?= $conversionPercentage >= 100 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= min(100, $conversionPercentage) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Ranking en tu categoría</td>
                                <td>#<?= $stats['ranking_position'] ?> de <?= $stats['total_in_category'] ?></td>
                                <td>
                                    <div class="progress-group">
                                        <span class="text-<?= $stats['percentile'] >= 50 ? 'success' : 'danger' ?>">
                                            Mejor que el <?= $stats['percentile'] ?>% de perfiles
                                        </span>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar <?= $stats['percentile'] >= 50 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $stats['percentile'] ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recomendaciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Recomendaciones para Mejorar
                    </h3>
                </div>
                <div class="card-body">
                    <div class="callout <?= $stats['total_views'] < $stats['avg_category_views'] * $stats['period_days'] ? 'callout-warning' : 'callout-success' ?>">
                        <h5><i class="fas fa-eye mr-1"></i> Visibilidad</h5>
                        <?php if ($stats['total_views'] < $stats['avg_category_views'] * $stats['period_days']): ?>
                            <p>Tu perfil recibe menos visitas que el promedio. Considera:</p>
                            <ul>
                                <li>Mejorar tus fotos principales para captar más atención</li>
                                <li>Actualizar tu descripción con información relevante</li>
                                <li>Actualizar tu perfil regularmente para aparecer entre los recientes</li>
                            </ul>
                        <?php else: ?>
                            <p>¡Excelente! Tu perfil recibe más visitas que el promedio. Para mantener este nivel:</p>
                            <ul>
                                <li>Continúa actualizando tus fotos regularmente</li>
                                <li>Mantén tu descripción actualizada y atractiva</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="callout <?= $stats['total_conversion_rate'] < $conversionComparison ? 'callout-warning' : 'callout-success' ?>">
                        <h5><i class="fas fa-percentage mr-1"></i> Conversión</h5>
                        <?php if ($stats['total_conversion_rate'] < $conversionComparison): ?>
                            <p>Tu tasa de conversión está por debajo del promedio. Recomendaciones:</p>
                            <ul>
                                <li>Asegúrate de que tu información de contacto sea clara y visible</li>
                                <li>Mejora la calidad y variedad de tus fotos</li>
                                <li>Proporciona información detallada sobre tus servicios y tarifas</li>
                            </ul>
                        <?php else: ?>
                            <p>¡Muy bien! Tu tasa de conversión es superior al promedio. Para mantener este rendimiento:</p>
                            <ul>
                                <li>Mantén la calidad de tus fotos y descripciones</li>
                                <li>Responde rápidamente a los mensajes</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i>
                        ¿Cómo se calculan estas estadísticas?
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <h5>Vistas</h5>
                                    <p>Número de veces que los usuarios han visitado tu perfil.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <h5>Contactos</h5>
                                    <p>Número de clics en tu botón de WhatsApp para contactarte.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <h5>Tasa de Conversión</h5>
                                    <p>Porcentaje de visitas que resultan en un contacto (Contactos ÷ Vistas × 100%).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <h5>Comparativa</h5>
                                    <p>Tus estadísticas se comparan con el promedio de perfiles en tu misma categoría (<?= $stats['profile']['gender'] === 'female' ? 'Mujeres' : ($stats['profile']['gender'] === 'male' ? 'Hombres' : 'Trans') ?>).</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <h5>Ranking</h5>
                                    <p>Tu posición se calcula en base al número total de vistas entre todos los perfiles de tu categoría.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para las gráficas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de rendimiento
    const performanceLabels = <?= json_encode(array_keys($stats['views_by_day'])) ?>;
    const viewsData = <?= json_encode(array_values($stats['views_by_day'])) ?>;
    const clicksData = <?= json_encode(array_values($stats['clicks_by_day'])) ?>;
    const conversionData = <?= json_encode(array_values($stats['conversion_rate'])) ?>;
    
    // Configurar gráfico de rendimiento
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: performanceLabels,
            datasets: [
                {
                    label: 'Vistas',
                    data: viewsData,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Contactos',
                    data: clicksData,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Conversión (%)',
                    data: conversionData,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y1',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de acciones'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Tasa de conversión (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 2) {
                                return label + context.parsed.y.toFixed(2) + '%';
                            }
                            return label + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
    
    // Configurar gráfico de ranking
    const rankingCtx = document.getElementById('rankingChart').getContext('2d');
    new Chart(rankingCtx, {
        type: 'doughnut',
        data: {
            labels: ['Tu posición', 'Resto de perfiles'],
            datasets: [{
                data: [<?= $stats['percentile'] ?>, <?= 100 - $stats['percentile'] ?>],
                backgroundColor: [
                    'rgba(60, 141, 188, 0.8)',
                    'rgba(210, 214, 222, 0.8)'
                ],
                borderColor: [
                    'rgba(60, 141, 188, 1)',
                    'rgba(210, 214, 222, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataIndex === 0) {
                                return 'Tu perfil está en el TOP ' + <?= $stats['percentile'] ?> + '%';
                            } else {
                                return 'Resto de perfiles: ' + (100 - <?= $stats['percentile'] ?>) + '%';
                            }
                        }
                    }
                }
            }
        }
    });
});
</script>