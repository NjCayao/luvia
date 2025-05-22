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

                    <!-- Formulario de pago integrado con JavaScript V4.0 -->
                    <div id="payment-form-container">
                        <h5>Información de pago:</h5>
                        
                        <!-- Indicador de carga -->
                        <div id="loading-indicator" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando formulario de pago...</span>
                            </div>
                            <p class="mt-2">Preparando formulario de pago seguro...</p>
                        </div>
                        
                        <!-- El formulario de pago se insertará aquí -->
                        <div id="payment-form" style="display: none;">
                            <!-- Formulario de Izipay se generará automáticamente aquí -->
                        </div>
                        
                        <!-- Botón de pago -->
                        <div class="mt-4" id="pay-button-container" style="display: none;">
                            <button type="button" class="btn btn-primary btn-lg btn-block" id="pay-button">
                                <i class="fas fa-credit-card mr-2"></i>
                                Pagar S/. <?= number_format($plan['price'], 2) ?>
                            </button>
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

<!-- CSS de Izipay - OBLIGATORIO en el HEAD -->
<link rel="stylesheet" href="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic-reset.css">

<!-- Script de Izipay V4.0 - OBLIGATORIO en el HEAD -->
<script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb"
        kr-post-url-success="<?= url('/pago/confirmacion') ?>"
        kr-post-url-refused="<?= url('/pago/fallido') ?>">
</script>

<!-- Tema clásico de Izipay -->
<script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>

<!-- Datos para JavaScript -->
<script>
    // Configuración para el checkout
    window.checkoutConfig = {
        planId: <?= $plan['id'] ?>,
        amount: <?= $plan['price'] ?>,
        csrfToken: '<?= getCsrfToken() ?>',
        processUrl: '<?= url('/pago/procesar-session') ?>'
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentError = document.getElementById('paymentError');
    const paymentErrorMessage = document.getElementById('paymentErrorMessage');
    const loadingIndicator = document.getElementById('loading-indicator');
    const paymentForm = document.getElementById('payment-form');
    const payButtonContainer = document.getElementById('pay-button-container');
    
    function showError(message) {
        paymentErrorMessage.textContent = message;
        paymentError.classList.remove('d-none');
        paymentError.scrollIntoView({ behavior: 'smooth' });
    }
    
    function hideError() {
        paymentError.classList.add('d-none');
    }
    
    // Inicializar formulario de pago
    function initPaymentForm() {
        hideError();
        
        // Crear sesión de pago
        fetch(window.checkoutConfig.processUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                plan_id: window.checkoutConfig.planId,
                csrf_token: window.checkoutConfig.csrfToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadIzipayScript(data.session);
            } else {
                showError(data.error || 'Error al inicializar el pago');
                loadingIndicator.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error de conexión al inicializar el pago');
            loadingIndicator.style.display = 'none';
        });
    }
    
    // Ya no necesitamos cargar el script dinámicamente
    function loadIzipayScript(session) {
        // El script ya está cargado en el head
        setupPaymentForm(session.formToken);
    }
    
    // Configurar formulario de pago
    function setupPaymentForm(formToken) {
        // Ocultar loading
        loadingIndicator.style.display = 'none';
        
        // Mostrar formulario
        paymentForm.style.display = 'block';
        payButtonContainer.style.display = 'block';
        
        // Insertar formulario de Izipay
        paymentForm.innerHTML = '<div class="kr-embedded" kr-form-token="' + formToken + '"></div>';
        
        // Configurar botón de pago
        document.getElementById('pay-button').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            
            // El formulario de Izipay manejará el envío
            if (typeof KR !== 'undefined') {
                KR.submit();
            } else {
                showError('Error: Sistema de pago no disponible');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pagar S/. <?= number_format($plan['price'], 2) ?>';
            }
        });
        
        // Eventos de Izipay
        if (typeof KR !== 'undefined') {
            KR.onError(function(event) {
                showError('Error en el pago: ' + (event.KR.result.message || 'Error desconocido'));
                document.getElementById('pay-button').disabled = false;
                document.getElementById('pay-button').innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pagar S/. <?= number_format($plan['price'], 2) ?>';
            });
            
            KR.onFormReady(function() {
                console.log('Formulario de pago listo');
            });
            
            KR.onSubmit(function(event) {
                console.log('Pago enviado');
                return true;
            });
        }
    }
    
    // Inicializar
    initPaymentForm();
});
</script>