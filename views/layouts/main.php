<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>

    <!-- Favicon y iconos -->
    <link rel="icon" type="image/x-icon" href="<?= url('img/favicon.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= url('img/favicon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= url('img/favicon.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= url('img/favicon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= url('img/favicon.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= url('img/favicon.png') ?>">

    <!-- Meta tags básicos -->
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME . ' - Encuentros para Adultos' ?></title>
    <meta name="description" content="<?= isset($pageDescription) ? $pageDescription : 'Encuentra compañía de calidad. Miles de perfiles verificados disponibles 24/7. Discreto, seguro y confiable.' ?>">
    <meta name="keywords" content="escorts, acompañantes, citas, adultos, <?= isset($pageKeywords) ? $pageKeywords : '' ?>">
    <meta name="author" content="<?= APP_NAME ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= isset($currentUrl) ? $currentUrl : url($_SERVER['REQUEST_URI']) ?>">
    <meta property="og:title" content="<?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME . ' - Encuentros para Adultos' ?>">
    <meta property="og:description" content="<?= isset($pageDescription) ? $pageDescription : 'Encuentra compañía de calidad. Miles de perfiles verificados disponibles 24/7.' ?>">
    <meta property="og:image" content="<?= isset($pageImage) ? url($pageImage) : url('img/favicon.png') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?= APP_NAME ?>">
    <meta property="og:locale" content="es_PE">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= isset($currentUrl) ? $currentUrl : url($_SERVER['REQUEST_URI']) ?>">
    <meta name="twitter:title" content="<?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?>">
    <meta name="twitter:description" content="<?= isset($pageDescription) ? $pageDescription : 'Encuentra compañía de calidad. Miles de perfiles verificados disponibles 24/7.' ?>">
    <meta name="twitter:image" content="<?= isset($pageImage) ? url($pageImage) : url('img/favicon.png') ?>">

    <!-- WhatsApp específico -->
    <meta property="whatsapp:image" content="<?= isset($pageImage) ? url($pageImage) : url('img/favicon.png') ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= isset($currentUrl) ? $currentUrl : url($_SERVER['REQUEST_URI']) ?>">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url('plugins/adminlte/css/adminlte.min.css') ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">

    <link rel="stylesheet" href="<?= url('css/media-protection.css') ?>">

    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>

    <?php   
    // SOLO para páginas de checkout (usar condición)

    $isCheckoutPage = strpos($_SERVER['REQUEST_URI'], '/pago/checkout/') !== false;

    if ($isCheckoutPage): ?>
        <!-- CSS de Izipay V4.0 -->
        <link rel="stylesheet" href="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic-reset.css">

        <!-- Script principal de Izipay V4.0 -->
        <script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
            kr-public-key="<?php
                            require_once __DIR__ . '/../../config/izipay.php';
                            $config = getIzipayConfig();
                            echo $config['publicKey'];
                            ?>"
            kr-post-url-success="<?= url('/pago/confirmacion') ?>"
            kr-post-url-refused="<?= url('/pago/fallido') ?>">
        </script>

        <!-- Tema clásico de Izipay -->
        <script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>
    <?php endif; ?>


</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-dark navbar-pink">
            <div class="container">
                <!-- <a href="<?= url('/') ?>" class="navbar-brand">
                <span class="brand-text font-weight-light"><b><?= APP_NAME ?></b></span>
            </a> -->
                <a href="<?= url('/') ?>" class="navbar-brand">
                    <img src="<?= url('img/logo_blanco.png') ?>" alt="<?= APP_NAME ?>" class="brand-logo">
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
                            <a href="<?= url('/categoria/female') ?>" class="nav-link">Erophia</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url('/categoria/male') ?>" class="nav-link">Erophian</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= url('/categoria/trans') ?>" class="nav-link">Eromix</a>
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
                                    <a href="<?= url('/usuario/dashboard') ?>" class="dropdown-item">
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
                            <a href="<?= url('/contacto') ?>" class="nav-link">Contacto</a>
                        </li>
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
                        <?php
                        if (isset($viewFile) && !empty($viewFile) && file_exists($viewFile)) {
                            require_once $viewFile;
                        }
                        ?>
                    </div>
                    <!-- /.main-content -->
                </div>
                <!-- /.container -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php require_once __DIR__ . '/footer.php'; ?>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= url('plugins/adminlte/js/adminlte.min.js') ?>"></script>

    <script src="<?= url('js/media-protection.js') ?>"></script>

    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>

    <script>
        // Configuración personalizada para la marca de agua
        window.luviaMediaConfig = {
            watermarkText: 'Erophia.com',
            watermarkOpacity: 0.8
        };
    </script>

</body>

</html>

<style>
    .brand-logo {
        height: 40px;
        /* Ajusta la altura según necesites */
        width: auto;
        max-width: 200px;
        /* Ancho máximo para evitar que sea muy grande */
    }

    /* Para pantallas pequeñas */
    @media (max-width: 768px) {
        .brand-logo {
            height: 35px;
            max-width: 150px;
        }
    }

    /* Si quieres que el logo sea más pequeño en dispositivos muy pequeños */
    @media (max-width: 480px) {
        .brand-logo {
            height: 30px;
            max-width: 120px;
        }
    }
</style>