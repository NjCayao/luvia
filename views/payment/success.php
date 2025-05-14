<?php
// views/payment/success.php
require_once __DIR__ . '/../layouts/main.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-check-circle"></i> Pago Exitoso</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                        <h4>¡Tu pago ha sido procesado correctamente!</h4>
                        <p>Tu suscripción ha sido activada y ya puedes disfrutar de todos los beneficios.</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Hemos enviado un comprobante de pago a tu correo electrónico.
                    </div>
                    
                    <div class="text-center mt-4">
                        <?php if ($_SESSION['user_type'] === 'advertiser'): ?>
                            <a href="<?= url('/usuario/medios') ?>" class="btn btn-primary">
                                <i class="fas fa-images"></i> Gestionar Fotos y Videos
                            </a>
                        <?php else: ?>
                            <a href="<?= url('/') ?>" class="btn btn-primary">
                                <i class="fas fa-search"></i> Explorar Perfiles
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= url('/usuario/dashboard') ?>" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-home"></i> Ir al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>