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
                    <?php
                    if (isset($viewFile) && !empty($viewFile)) {
                        if (file_exists($viewFile)) {
                            try {
                                include $viewFile;
                            } catch (Throwable $e) {
                                echo '<div class="alert alert-danger">';
                                echo '<h5><i class="icon fas fa-ban"></i> Error al cargar la vista</h5>';
                                echo 'Ha ocurrido un error al cargar el contenido: ' . $e->getMessage();
                                echo '</div>';

                                // Si está en modo desarrollo, mostrar más detalles
                                if (defined('APP_DEBUG') && APP_DEBUG) {
                                    echo '<div class="card card-danger">';
                                    echo '<div class="card-header">';
                                    echo '<h3 class="card-title">Información de depuración</h3>';
                                    echo '</div>';
                                    echo '<div class="card-body">';
                                    echo '<p><strong>Archivo:</strong> ' . $e->getFile() . ' (línea ' . $e->getLine() . ')</p>';
                                    echo '<p><strong>Traza:</strong></p>';
                                    echo '<pre>' . $e->getTraceAsString() . '</pre>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                        } else {
                            echo '<div class="alert alert-warning">';
                            echo '<h5><i class="icon fas fa-exclamation-triangle"></i> Vista no encontrada</h5>';
                            echo 'No se encontró el archivo de vista: ' . $viewFile;
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">';
                        echo '<h5><i class="icon fas fa-info"></i> No hay contenido que mostrar</h5>';
                        echo 'No se ha especificado una vista para mostrar.';
                        echo '</div>';
                    }
                    ?>
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