<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle . ' - Admin' : 'Panel de Administración' ?></title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url('plugins/adminlte/css/adminlte.min.css') ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('css/admin.css') ?>">
    
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= url('/admin') ?>" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= url('/') ?>" class="nav-link" target="_blank">Ver Sitio</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle"></i>
                    <span class="d-none d-md-inline-block ml-1"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="<?= url('/logout') ?>" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= url('/admin') ?>" class="brand-link">
            <span class="brand-text font-weight-light"><?= APP_NAME ?> Admin</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="<?= url('/admin') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && substr_count($_SERVER['REQUEST_URI'], '/') === 1 ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/usuarios') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/usuario') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/perfiles') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/perfil') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>Perfiles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/pagos') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/pago') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Pagos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/suscripciones') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/suscripcion') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-sync-alt"></i>
                            <p>Suscripciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/planes') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/plan') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Planes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/estadisticas') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/estadisticas') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Estadísticas</p>
                        </a>
                    </li>
                    <li class="nav-header">ACCESOS RÁPIDOS</li>
                    <li class="nav-item">
                        <a href="<?= url('/') ?>" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-globe"></i>
                            <p>Ver Sitio</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/logout') ?>" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Cerrar Sesión</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= isset($pageHeader) ? $pageHeader : 'Dashboard' ?></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Inicio</a></li>
                            <?php if (isset($pageHeader) && $pageHeader !== 'Dashboard'): ?>
                                <li class="breadcrumb-item active"><?= $pageHeader ?></li>
                            <?php endif; ?>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Flash Messages -->
                <?php displayFlashMessage(); ?>
                
                <!-- Main content goes here -->
                <?php require_once $viewFile ?? ''; ?>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Versión</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= url('/') ?>"><?= APP_NAME ?></a>.</strong> Todos los derechos reservados.
    </footer>
</div>
<!-- ./wrapper -->

<!-- Selector de período -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-1"></i>
            Período de Análisis
        </h3>
    </div>
    <div class="card-body">
        <form method="get" action="<?= url('/admin/estadisticas') ?>" class="form-inline">
            <div class="form-group mr-3">
                <label for="period" class="mr-2">Mostrar estadísticas de:</label>
                <select name="period" id="period" class="form-control" onchange="this.form.submit()">
                    <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Última semana</option>
                    <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Último mes</option>
                    <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Último año</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Resumen general -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $userStats['total'] ?></h3>
                <p>Usuarios Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $profileStats['total'] ?></h3>
                <p>Perfiles Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>S/. <?= number_format($paymentStats['total_amount'], 2) ?></h3>
                <p>Ingresos (<?= $period === 'week' ? 'Semana' : ($period === 'month' ? 'Mes' : 'Año') ?>)</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $subscriptionStats['active_total'] ?></h3>
                <p>Suscripciones Activas</p>
            </div>
            <div class="icon">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos principales -->
<div class="row">
    <div class="col-md-8">
        <!-- Gráfico de ingresos diarios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Ingresos Diarios
                </h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Distribución de usuarios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribución de Usuarios
                </h3>
            </div>
            <div class="card-body">
                <canvas id="userTypeChart" style="min-height: 250px; height: 250px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos secundarios -->
<div class="row">
    <div class="col-md-6">
        <!-- Nuevos usuarios por día -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-1"></i>
                    Nuevos Usuarios Registrados
                </h3>
            </div>
            <div class="card-body">
                <canvas id="newUsersChart" style="min-height: 250px; height: 250px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Distribución de perfiles por género -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-venus-mars mr-1"></i>
                    Perfiles por Género
                </h3>
            </div>
            <div class="card-body">
                <canvas id="genderChart" style="min-height: 250px; height: 250px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Análisis de tráfico -->
<div class="row">
    <div class="col-md-12">
        <!-- Tráfico y conversiones -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-area mr-1"></i>
                    Análisis de Tráfico y Conversiones
                </h3>
            </div>
            <div class="card-body">
                <canvas id="trafficChart" style="min-height: 300px; height: 300px; max-height: 350px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de análisis -->
<div class="row">
    <!-- Perfiles más vistos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye mr-1"></i>
                    Perfiles Más Vistos
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Perfil</th>
                            <th>Género</th>
                            <th>Ciudad</th>
                            <th>Vistas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profileStats['most_viewed'] as $profile): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($profile['main_photo'])): ?>
                                            <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" alt="Foto de perfil" class="img-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?= url('img/profile-placeholder.jpg') ?>" alt="Sin foto" class="img-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($profile['name']) ?>
                                        <?php if ($profile['is_verified']): ?>
                                            <i class="fas fa-check-circle text-primary ml-1" title="Verificado"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $profile['gender'] === 'female' ? 'badge-pink' : ($profile['gender'] === 'male' ? 'badge-blue' : 'badge-purple') ?>">
                                        <?= $profile['gender'] === 'female' ? 'Mujer' : ($profile['gender'] === 'male' ? 'Hombre' : 'Trans') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($profile['city']) ?></td>
                                <td><?= $profile['views'] ?></td>
                                <td>
                                    <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Perfiles más contactados -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fab fa-whatsapp mr-1"></i>
                    Perfiles Más Contactados
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Perfil</th>
                            <th>Género</th>
                            <th>Ciudad</th>
                            <th>Clicks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profileStats['most_contacted'] as $profile): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($profile['main_photo'])): ?>
                                            <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" alt="Foto de perfil" class="img-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?= url('img/profile-placeholder.jpg') ?>" alt="Sin foto" class="img-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($profile['name']) ?>
                                        <?php if ($profile['is_verified']): ?>
                                            <i class="fas fa-check-circle text-primary ml-1" title="Verificado"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $profile['gender'] === 'female' ? 'badge-pink' : ($profile['gender'] === 'male' ? 'badge-blue' : 'badge-purple') ?>">
                                        <?= $profile['gender'] === 'female' ? 'Mujer' : ($profile['gender'] === 'male' ? 'Hombre' : 'Trans') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($profile['city']) ?></td>
                                <td><?= $profile['whatsapp_clicks'] ?></td>
                                <td>
                                    <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Distribución geográfica -->
<div class="row">
    <div class="col-md-6">
        <!-- Mapa de perfiles por ciudad -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Distribución por Ciudad
                </h3>
            </div>
            <div class="card-body">
                <canvas id="cityChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Distribución de planes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tags mr-1"></i>
                    Suscripciones por Plan
                </h3>
            </div>
            <div class="card-body">
                <canvas id="planChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.badge-pink {
    background-color: #f27eb5;
    color: white;
}
.badge-blue {
    background-color: #3490dc;
    color: white;
}
.badge-purple {
    background-color: #8e44ad;
    color: white;
}
</style>

<!-- Scripts para las gráficas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos para gráficos
    const revenueData = <?= json_encode(array_values($paymentStats['revenue_by_day'])) ?>;
    const revenueDates = <?= json_encode(array_keys($paymentStats['revenue_by_day'])) ?>;
    
    const userTypeLabels = ['Administradores', 'Anunciantes', 'Visitantes'];
    const userTypeData = [
        <?= $userStats['by_type']['admin'] ?? 0 ?>,
        <?= $userStats['by_type']['advertiser'] ?? 0 ?>,
        <?= $userStats['by_type']['visitor'] ?? 0 ?>
    ];
    
    const newUsersDates = <?= json_encode(array_keys($userStats['new_by_day'])) ?>;
    const newUsersData = <?= json_encode(array_values($userStats['new_by_day'])) ?>;
    
    const genderLabels = ['Mujeres', 'Hombres', 'Trans'];
    const genderData = [
        <?= $profileStats['by_gender']['female'] ?? 0 ?>,
        <?= $profileStats['by_gender']['male'] ?? 0 ?>,
        <?= $profileStats['by_gender']['trans'] ?? 0 ?>
    ];
    
    const cityLabels = <?= json_encode(array_keys($profileStats['by_city'])) ?>;
    const cityData = <?= json_encode(array_values($profileStats['by_city'])) ?>;
    
    const trafficDates = <?= json_encode(array_keys($trafficStats['views_by_day'])) ?>;
    const viewsData = <?= json_encode(array_values($trafficStats['views_by_day'])) ?>;
    const clicksData = <?= json_encode(array_values($trafficStats['clicks_by_day'])) ?>;
    const conversionData = <?= json_encode(array_values($trafficStats['conversion_rate'])) ?>;
    
    const planLabels = [];
    const planData = [];
    <?php foreach ($subscriptionStats['by_plan'] as $planId => $planInfo): ?>
        planLabels.push('<?= addslashes($planInfo['name']) ?> (<?= $planInfo['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante' ?>)');
        planData.push(<?= $planInfo['count'] ?>);
    <?php endforeach; ?>
    
    // Configurar gráfico de ingresos
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueDates,
            datasets: [{
                label: 'Ingresos diarios (S/.)',
                data: revenueData,
                backgroundColor: 'rgba(60, 141, 188, 0.3)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/. ' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'S/. ' + context.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });
    
    // Configurar gráfico de tipos de usuarios
    const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
    new Chart(userTypeCtx, {
        type: 'pie',
        data: {
            labels: userTypeLabels,
            datasets: [{
                data: userTypeData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)',
                    'rgb(54, 162, 235)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Configurar gráfico de nuevos usuarios
    const newUsersCtx = document.getElementById('newUsersChart').getContext('2d');
    new Chart(newUsersCtx, {
        type: 'bar',
        data: {
            labels: newUsersDates,
            datasets: [{
                label: 'Nuevos usuarios',
                data: newUsersData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                pointRadius: 3,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Configurar gráfico de géneros
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderData,
                backgroundColor: [
                    'rgb(242, 126, 181)', // rosa para mujeres
                    'rgb(52, 144, 220)',  // azul para hombres
                    'rgb(142, 68, 173)'   // morado para trans
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Configurar gráfico de tráfico
    const trafficCtx = document.getElementById('trafficChart').getContext('2d');
    new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: trafficDates,
            datasets: [
                {
                    label: 'Vistas',
                    data: viewsData,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Clicks',
                    data: clicksData,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Tasa de conversión (%)',
                    data: conversionData,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    pointRadius: 3,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
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
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
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
    
    // Configurar gráfico de ciudades
    const cityCtx = document.getElementById('cityChart').getContext('2d');
    new Chart(cityCtx, {
        type: 'bar',
        data: {
            labels: cityLabels,
            datasets: [{
                label: 'Perfiles por ciudad',
                data: cityData,
                backgroundColor: 'rgba(60, 141, 188, 0.7)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Configurar gráfico de planes
    const planCtx = document.getElementById('planChart').getContext('2d');
    new Chart(planCtx, {
        type: 'pie',
        data: {
            labels: planLabels,
            datasets: [{
                data: planData,
                backgroundColor: [
                    'rgba(52, 144, 220, 0.8)',
                    'rgba(56, 193, 114, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(239, 83, 80, 0.8)',
                    'rgba(17, 135, 207, 0.8)',
                    'rgba(155, 89, 182, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>

<!-- jQuery -->
<script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- ChartJS -->
<script src="<?= url('plugins/chart.js/Chart.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= url('plugins/adminlte/js/adminlte.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

<?php if (isset($extraJs)): ?>
    <?= $extraJs ?>
<?php endif; ?>

</body>
</html>