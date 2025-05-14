<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url('plugins/adminlte/css/adminlte.min.css') ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-dark navbar-pink">
        <div class="container">
            <a href="<?= url('/') ?>" class="navbar-brand">
                <span class="brand-text font-weight-light"><b><?= APP_NAME ?></b></span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?= url('/') ?>" class="nav-link">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/categoria/female') ?>" class="nav-link">Mujeres</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/categoria/male') ?>" class="nav-link">Hombres</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/categoria/trans') ?>" class="nav-link">Trans</a>
                    </li>
                </ul>
            </div>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle"></i> Mi Cuenta
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if ($_SESSION['user_type'] === 'admin'): ?>
                                <a href="<?= url('/admin') ?>" class="dropdown-item">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Panel Admin
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
                            
                            <?php if ($_SESSION['user_type'] === 'advertiser'): ?>
                                <a href="<?= url('/usuario/perfil') ?>" class="dropdown-item">
                                    <i class="fas fa-id-card mr-2"></i> Mi Perfil
                                </a>
                                <a href="<?= url('/usuario/medios') ?>" class="dropdown-item">
                                    <i class="fas fa-images mr-2"></i> Mis Fotos/Videos
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= url('/usuario/dashboard') ?>" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Mi Cuenta
                            </a>
                            
                            <div class="dropdown-divider"></div>
                            
                            <a href="<?= url('/logout') ?>" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= url('/login') ?>" class="nav-link">Ingresar</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/registro') ?>" class="btn btn-outline-light">Regístrate</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <?php if (isset($pageHeader)): ?>
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $pageHeader ?></h1>
                        </div>
                        <?php if (isset($breadcrumb)): ?>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <?= $breadcrumb ?>
                                </ol>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main content -->
        <div class="content">
            <div class="container">
                <?php displayFlashMessage(); ?>
                
                <!-- Contenido principal -->
                <div class="main-content">
                    <?php isset($viewFile) && !empty($viewFile) && file_exists($viewFile) ? require_once $viewFile : ''; ?>
                </div>
                <!-- /.main-content -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- Default to the left -->
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?= APP_NAME ?></h5>
                    <p class="text-muted">Tu plataforma de confianza</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/terminos') ?>">Términos y Condiciones</a></li>
                        <li><a href="<?= url('/privacidad') ?>">Política de Privacidad</a></li>
                        <li><a href="<?= url('/contacto') ?>">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <div class="social-icons">
                        <a href="#" class="mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="mr-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= url('/') ?>"><?= APP_NAME ?></a>.</strong> Todos los derechos reservados.
            </div>
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= url('plugins/adminlte/js/adminlte.min.js') ?>"></script>

<?php if (isset($extraJs)): ?>
    <?= $extraJs ?>
<?php endif; ?>

</body>
</html>