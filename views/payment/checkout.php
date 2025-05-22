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

                    <!-- Opciones de pago -->
                    <div id="payment-options">
                        <h5>Selecciona tu método de pago:</h5>
                        
                        <div class="row mt-4">
                            <!-- Pago con Tarjeta -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-option h-100" data-method="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                                        <h5>Tarjeta de Crédito/Débito</h5>
                                        <p class="text-muted">Visa, Mastercard, American Express</p>
                                        <button type="button" class="btn btn-primary btn-block" id="pay-with-card">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Pagar con Tarjeta
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pago con Yape -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-option h-100" data-method="yape">
                                    <div class="card-body text-center">
                                        <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                                        <h5>Yape</h5>
                                        <p class="text-muted">Pago rápido y seguro</p>
                                        <button type="button" class="btn btn-success btn-block" id="pay-with-yape">
                                            <i class="fas fa-mobile-alt mr-2"></i>
                                            Pagar con Yape
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información de seguridad -->
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <strong>Pago 100% seguro:</strong> Serás redirigido a la plataforma segura de Izipay para completar tu pago. 
                            Después del pago, regresarás automáticamente a nuestro sitio.
                        </div>
                    </div>

                    <!-- Indicador de procesamiento -->
                    <div id="processing-indicator" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Procesando...</span>
                        </div>
                        <p class="mt-2">Preparando el pago seguro...</p>
                        <p class="text-muted">Serás redirigido en unos segundos</p>
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
                    <h6><i class="fas fa-question-circle mr-1"></i>¿Cómo funciona?</h6>
                    <ol class="small">
                        <li>Selecciona tu método de pago preferido</li>
                        <li>Serás redirigido a la plataforma segura de Izipay</li>
                        <li>Completa tus datos de pago</li>
                        <li>Regresa automáticamente a nuestro sitio</li>
                        <li>¡Tu suscripción estará activa inmediatamente!</li>
                    </ol>
                    
                    <hr>
                    
                    <h6><i class="fas fa-headset mr-1"></i>¿Necesitas ayuda?</h6>
                    <p class="small text-muted mb-2">
                        Si tienes problemas con tu pago:
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
    const processingIndicator = document.getElementById('processing-indicator');
    const paymentOptions = document.getElementById('payment-options');
    
    function showError(message) {
        console.error('Payment Error:', message);
        paymentErrorMessage.textContent = message;
        paymentError.classList.remove('d-none');
        
        // Ocultar processing y mostrar opciones
        processingIndicator.classList.add('d-none');
        paymentOptions.classList.remove('d-none');
    }
    
    function hideError() {
        paymentError.classList.add('d-none');
    }
    
    function showProcessing() {
        paymentOptions.classList.add('d-none');
        processingIndicator.classList.remove('d-none');
        hideError();
    }
    
    // Procesar pago con método específico
    function processPayment(method) {
        console.log('Procesando pago con método:', method);
        showProcessing();
        
        // Crear sesión de pago
        fetch(window.checkoutConfig.processUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                plan_id: window.checkoutConfig.planId,
                payment_method: method,
                csrf_token: window.checkoutConfig.csrfToken
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success && data.session.paymentUrl) {
                console.log('Redirigiendo a:', data.session.paymentUrl);
                
                // Pequeña demora para que el usuario vea el mensaje
                setTimeout(function() {
                    window.location.href = data.session.paymentUrl;
                }, 1000);
            } else {
                showError(data.error || 'Error al inicializar el pago');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showError('Error de conexión al procesar el pago');
        });
    }
    
    // Event listeners para los botones
    document.getElementById('pay-with-card').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        processPayment('card');
    });
    
    document.getElementById('pay-with-yape').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        processPayment('yape');
    });
    
    // Efectos hover para las tarjetas
    const paymentCards = document.querySelectorAll('.payment-option');
    paymentCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
});
</script>

<style>
.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-option:hover {
    border-color: #007bff;
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.payment-option.selected {
    border-color: #28a745;
    background-color: #f8f9fa;
}

#processing-indicator {
    padding: 40px 20px;
}

.fa-3x {
    font-size: 3rem;
}
</style>