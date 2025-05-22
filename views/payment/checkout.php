<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-credit-card mr-2"></i>Realizar Pago</h3>
                </div>
                <div class="card-body">
                    <h4 class="mb-4">Plan: <?= htmlspecialchars($plan['name']) ?></h4>
                    <p><strong>Duración:</strong> <?= $plan['duration'] ?> días</p>
                    <p><strong>Precio:</strong> S/. <?= number_format($plan['price'], 2) ?></p>

                    <hr>

                    <h5>Selecciona tu método de pago:</h5>

                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="card-tab" data-toggle="tab" href="#card" role="tab">
                                <i class="fas fa-credit-card mr-1"></i>Tarjeta de Crédito/Débito
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="yape-tab" data-toggle="tab" href="#yape" role="tab">
                                <i class="fab fa-yape mr-1"></i>Yape
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="paymentTabContent">
                        <!-- Tarjeta de Crédito/Débito -->
                        <div class="tab-pane fade show active" id="card" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pago seguro procesado por Izipay. Aceptamos Visa, Mastercard y otras tarjetas.
                            </div>
                            
                            <form id="cardPaymentForm">
                                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="cardPayBtn">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Pagar con Tarjeta S/. <?= number_format($plan['price'], 2) ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Yape -->
                        <div class="tab-pane fade" id="yape" role="tabpanel">
                            <div class="alert alert-success">
                                <i class="fas fa-mobile-alt mr-1"></i>
                                Pago rápido y seguro con tu aplicación Yape.
                            </div>
                            
                            <form id="yapePaymentForm">
                                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-lg btn-block" id="yapePayBtn">
                                        <i class="fab fa-yape mr-2"></i>
                                        Pagar con Yape S/. <?= number_format($plan['price'], 2) ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Mensaje de error -->
                    <div class="alert alert-danger mt-3 d-none" id="paymentError">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span id="paymentErrorMessage"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-receipt mr-2"></i>Resumen</h4>
                </div>
                <div class="card-body">
                    <p><strong>Plan:</strong> <?= htmlspecialchars($plan['name']) ?></p>
                    <p><strong>Duración:</strong> <?= $plan['duration'] ?> días</p>

                    <?php if ($user['user_type'] === 'advertiser'): ?>
                        <p><strong>Beneficios:</strong></p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-1"></i> Hasta <?= $plan['max_photos'] ?> fotos</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Hasta <?= $plan['max_videos'] ?> videos</li>
                            <?php if ($plan['featured']): ?>
                                <li><i class="fas fa-check text-success mr-1"></i> Perfil destacado</li>
                            <?php endif; ?>
                            <li><i class="fas fa-check text-success mr-1"></i> Estadísticas de perfil</li>
                        </ul>
                    <?php elseif ($user['user_type'] === 'visitor'): ?>
                        <p><strong>Beneficios:</strong></p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-1"></i> Acceso a todos los perfiles</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Ver fotos y videos completos</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Contacto directo por WhatsApp</li>
                            <?php if ($plan['featured']): ?>
                                <li><i class="fas fa-check text-success mr-1"></i> Acceso prioritario a nuevos perfiles</li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>

                    <hr>
                    
                    <div class="row">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-right">S/. <?= number_format($plan['price'], 2) ?></div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-right"><strong class="text-primary">S/. <?= number_format($plan['price'], 2) ?></strong></div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Pago 100% seguro y encriptado
                        </small>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-question-circle mr-1"></i>¿Necesitas ayuda?</h6>
                    <p class="small text-muted mb-2">
                        Si tienes problemas con tu pago, puedes contactarnos:
                    </p>
                    <p class="small">
                        <i class="fas fa-envelope mr-1"></i> soporte@erophia.com<br>
                        <i class="fas fa-phone mr-1"></i> +51 xxx xxx xxx
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentError = document.getElementById('paymentError');
        const paymentErrorMessage = document.getElementById('paymentErrorMessage');
        
        function showError(message) {
            paymentErrorMessage.textContent = message;
            paymentError.classList.remove('d-none');
            // Scroll to error
            paymentError.scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideError() {
            paymentError.classList.add('d-none');
        }
        
        // Formulario de pago con tarjeta
        document.getElementById('cardPaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            hideError();

            const formData = new FormData(this);
            const button = document.getElementById('cardPayBtn');
            const originalText = button.innerHTML;

            // Mostrar indicador de carga
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            fetch('<?= url('/pago/procesar-tarjeta') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        // Redirigir a la página de pago de Izipay
                        window.location.href = data.redirect_url;
                    } else {
                        // Mostrar error
                        showError(data.error || 'Error al procesar el pago');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error de conexión. Por favor, intenta nuevamente.');
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        });

        // Formulario de pago con Yape
        document.getElementById('yapePaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            hideError();

            const formData = new FormData(this);
            const button = document.getElementById('yapePayBtn');
            const originalText = button.innerHTML;

            // Mostrar indicador de carga
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            fetch('<?= url('/pago/procesar-yape') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        // Redirigir a la página de pago de Izipay
                        window.location.href = data.redirect_url;
                    } else {
                        // Mostrar error
                        showError(data.error || 'Error al procesar el pago');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error de conexión. Por favor, intenta nuevamente.');
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        });
    });
</script>