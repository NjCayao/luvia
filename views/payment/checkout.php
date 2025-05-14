<?php
// views/payment/checkout.php

// Incluir encabezado
require_once __DIR__ . '/../layouts/main.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Realizar Pago</h3>
                </div>
                <div class="card-body">
                    <h4 class="mb-4">Plan: <?= htmlspecialchars($plan['name']) ?></h4>
                    <p>Duración: <?= $plan['duration'] ?> días</p>
                    <p>Precio: S/. <?= number_format($plan['price'], 2) ?></p>

                    <hr>

                    <h5>Selecciona tu método de pago:</h5>

                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="card-tab" data-toggle="tab" href="#card" role="tab">Tarjeta de Crédito/Débito</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="yape-tab" data-toggle="tab" href="#yape" role="tab">Yape</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="paymentTabContent">
                        <!-- Tarjeta de Crédito/Débito -->
                        <div class="tab-pane fade show active" id="card" role="tabpanel">
                            <form id="cardPaymentForm">
                                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        Pagar con Tarjeta S/. <?= number_format($plan['price'], 2) ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Yape -->
                        <div class="tab-pane fade" id="yape" role="tabpanel">
                            <form id="yapePaymentForm">
                                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-lg btn-block">
                                        Pagar con Yape S/. <?= number_format($plan['price'], 2) ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Resumen</h4>
                </div>
                <div class="card-body">
                    <p><strong>Plan:</strong> <?= htmlspecialchars($plan['name']) ?></p>
                    <p><strong>Duración:</strong> <?= $plan['duration'] ?> días</p>

                    <?php if ($user['user_type'] === 'advertiser'): ?>
                        <p><strong>Beneficios:</strong></p>
                        <ul>
                            <li>Hasta <?= $plan['max_photos'] ?> fotos</li>
                            <li>Hasta <?= $plan['max_videos'] ?> videos</li>
                            <?php if ($plan['featured']): ?>
                                <li>Perfil destacado</li>
                            <?php endif; ?>
                        </ul>
                    <?php elseif ($user['user_type'] === 'visitor'): ?>
                        <p><strong>Beneficios:</strong></p>
                        <ul>
                            <li>Acceso a todos los perfiles</li>
                            <li>Ver fotos y videos completos</li>
                            <li>Contacto directo por WhatsApp</li>
                        </ul>
                    <?php endif; ?>

                    <hr>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto-renew" name="auto_renew" checked>
                        <label class="form-check-label" for="auto-renew">
                            Activar renovación automática
                        </label>
                        <small class="form-text text-muted">
                            Tu suscripción se renovará automáticamente al finalizar el período.
                            Puedes cancelar la renovación en cualquier momento.
                        </small>
                    </div>

                    <h5>Total: S/. <?= number_format($plan['price'], 2) ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Formulario de pago con tarjeta
        document.getElementById('cardPaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Mostrar indicador de carga
            this.querySelector('button').disabled = true;
            this.querySelector('button').innerHTML = 'Procesando...';

            fetch('/pago/procesar-tarjeta', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirigir a la página de pago de Izipay
                        window.location.href = data.redirect_url;
                    } else {
                        // Mostrar error
                        alert('Error: ' + data.error);
                        this.querySelector('button').disabled = false;
                        this.querySelector('button').innerHTML = 'Pagar con Tarjeta S/. <?= number_format($plan['price'], 2) ?>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error en la comunicación con el servidor');
                    this.querySelector('button').disabled = false;
                    this.querySelector('button').innerHTML = 'Pagar con Tarjeta S/. <?= number_format($plan['price'], 2) ?>';
                });
        });

        // Formulario de pago con Yape
        document.getElementById('yapePaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Mostrar indicador de carga
            this.querySelector('button').disabled = true;
            this.querySelector('button').innerHTML = 'Procesando...';

            fetch('/pago/procesar-yape', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirigir a la página de pago de Izipay
                        window.location.href = data.redirect_url;
                    } else {
                        // Mostrar error
                        alert('Error: ' + data.error);
                        this.querySelector('button').disabled = false;
                        this.querySelector('button').innerHTML = 'Pagar con Yape S/. <?= number_format($plan['price'], 2) ?>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error en la comunicación con el servidor');
                    this.querySelector('button').disabled = false;
                    this.querySelector('button').innerHTML = 'Pagar con Yape S/. <?= number_format($plan['price'], 2) ?>';
                });
        });
    });
</script>

<?php
// Incluir pie de página
require_once __DIR__ . '/../layouts/footer.php';
?>