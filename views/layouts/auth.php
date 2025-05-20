<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= url('plugins/adminlte/css/adminlte.min.css') ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('css/auth.css') ?>">
    
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="hold-transition login-page">
    <div class="auth-background"></div>
    
    <div class="login-box">
        <div class="login-logo">
            <a href="<?= url('/') ?>"><b><?= APP_NAME ?></b></a>
            <p class="login-subtitle">Tu sitio de citas para adultos</p>
        </div>
        
        <!-- Flash Messages -->
        <?php displayFlashMessage(); ?>
        
        <!-- Card -->
        <div class="card">
            <div class="card-body login-card-body">       
                <?php isset($viewFile) && !empty($viewFile) && file_exists($viewFile) ? require_once $viewFile : ''; ?>
            </div>
            <!-- /.login-card-body -->
        </div>
        
        <div class="mt-4 text-center text-white">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> - Todos los derechos reservados</p>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= url('plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= url('plugins/adminlte/js/adminlte.min.js') ?>"></script>
    
    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>
    
    <script>
    // Add particle effect to login background
    document.addEventListener("DOMContentLoaded", function() {
        // Only create particles if we're not on a mobile device
        if (window.innerWidth > 768) {
            createParticles();
        }
    });
    
    function createParticles() {
        const body = document.querySelector('body');
        const particleContainer = document.createElement('div');
        particleContainer.className = 'particles';
        body.appendChild(particleContainer);
        
        // Create particles
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + 'vw';
            particle.style.top = Math.random() * 100 + 'vh';
            particle.style.animationDuration = 3 + Math.random() * 8 + 's';
            particle.style.animationDelay = Math.random() * 5 + 's';
            particleContainer.appendChild(particle);
        }
    }
    </script>
</body>
</html>