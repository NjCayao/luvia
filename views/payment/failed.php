<?php
// views/payment/failed.php
require_once __DIR__ . '/../layouts/main.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="fas fa-times-circle"></i> Pago Fallido</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-times-circle text-danger fa-5x mb-3"></i>
                        <h4>Lo sentimos, ha ocurrido un error con tu pago.</h4>
                        <p class="text-muted">
                            <?= htmlspecialchars($reason) ?>
                        </p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> Si se realizó algún cargo a tu tarjeta, 
                        se procesará la devolución automáticamente en los próximos días.
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Intentar Nuevamente
                        </a>
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