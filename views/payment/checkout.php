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

                    <!-- Selección de método de pago -->
                    <div id="payment-methods">
                        <h5 class="mb-4">Elige tu método de pago:</h5>
                        
                        <div class="row">
                            <!-- Yape - Más popular en Perú -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-card h-100" data-method="yape">
                                    <div class="card-body text-center">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-mobile-alt fa-4x text-success"></i>
                                        </div>
                                        <h5 class="text-success">Yape</h5>
                                        <p class="text-muted small">Pago instantáneo con tu celular</p>
                                        <span class="badge badge-success mb-3">MÁS POPULAR</span>
                                        <button type="button" class="btn btn-success btn-block" data-method="yape">
                                            <i class="fas fa-mobile-alt mr-2"></i>
                                            Pagar con Yape
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- QR -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-card h-100" data-method="qr">
                                    <div class="card-body text-center">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-qrcode fa-4x text-info"></i>
                                        </div>
                                        <h5 class="text-info">Código QR</h5>
                                        <p class="text-muted small">Escanea con cualquier app</p>
                                        <span class="badge badge-info mb-3">RÁPIDO</span>
                                        <button type="button" class="btn btn-info btn-block" data-method="qr">
                                            <i class="fas fa-qrcode mr-2"></i>
                                            Pagar con QR
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tarjetas -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-card h-100" data-method="card">
                                    <div class="card-body text-center">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-credit-card fa-4x text-primary"></i>
                                        </div>
                                        <h5 class="text-primary">Tarjeta</h5>
                                        <p class="text-muted small">Visa, Mastercard, Amex</p>
                                        <div class="card-brands mb-3">
                                            <i class="fab fa-cc-visa fa-2x mr-2 text-primary"></i>
                                            <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-block" data-method="card">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Pagar con Tarjeta
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Plin -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-card h-100" data-method="plin">
                                    <div class="card-body text-center">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-university fa-4x text-warning"></i>
                                        </div>
                                        <h5 class="text-warning">Plin</h5>
                                        <p class="text-muted small">Con tu número Interbank</p>
                                        <button type="button" class="btn btn-warning btn-block" data-method="plin">
                                            <i class="fas fa-university mr-2"></i>
                                            Pagar con Plin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de pago (oculto inicialmente) -->
                    <div id="payment-form" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 id="payment-title">Completar Pago</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="back-to-methods">
                                <i class="fas fa-arrow-left mr-1"></i> Cambiar método
                            </button>
                        </div>
                        
                        <!-- Contenedor del SDK -->
                        <div id="izipay-container">
                            <!-- Aquí se carga el formulario del SDK -->
                        </div>
                    </div>

                    <!-- Estado de carga -->
                    <div id="loading" class="text-center d-none py-5">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                        <h5 id="loading-text">Preparando pago seguro...</h5>
                        <p class="text-muted">Por favor espera un momento</p>
                    </div>

                    <!-- Error -->
                    <div class="alert alert-danger d-none" id="error-container">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Error:</strong> <span id="error-message"></span>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="retry-payment">
                                <i class="fas fa-redo mr-1"></i> Reintentar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="back-to-start">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
                            </button>
                        </div>
                    </div>
                    
                    <!-- Información de seguridad -->
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <strong>Pago 100% Seguro:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Procesado por Izipay (certificado PCI DSS)</li>
                            <li>Encriptación SSL de 256 bits</li>
                            <li>No almacenamos datos de tu tarjeta</li>
                            <li>Compatible con 3D Secure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Resumen del pedido -->
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
                    <?php else: ?>
                        <p><strong>Beneficios:</strong></p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-1"></i> Acceso a todos los perfiles</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Ver fotos y videos completos</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Contacto directo por WhatsApp</li>
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
                </div>
            </div>
            
            <!-- Información sobre Yape -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-mobile-alt text-success mr-1"></i>¿Por qué Yape?</h6>
                    <ul class="small text-muted">
                        <li>✅ Más de 17 millones de usuarios</li>
                        <li>✅ Pago instantáneo y seguro</li>
                        <li>✅ Sin comisiones adicionales</li>
                        <li>✅ Disponible 24/7</li>
                    </ul>
                    
                    <hr>
                    
                    <h6><i class="fas fa-headset mr-1"></i>¿Necesitas ayuda?</h6>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-envelope mr-1"></i> soporte@erophia.com<br>
                        <i class="fas fa-phone mr-1"></i> +51 xxx xxx xxx
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SDK Web de Izipay - VERSIÓN OFICIAL -->
<?php
require_once __DIR__ . '/../../config/izipay_sdk_web.php';
$sdkConfig = getIzipaySdkConfig();
$returnUrls = getSdkReturnUrls();
?>

<script 
    src="<?= $sdkConfig['jsEndpoint'] ?>"
    kr-public-key="<?= htmlspecialchars($sdkConfig['publicKey']) ?>"
    kr-post-url-success="<?= htmlspecialchars($returnUrls['success']) ?>"
    kr-post-url-refused="<?= htmlspecialchars($returnUrls['failed']) ?>"
    kr-language="es-ES">
</script>

<link rel="stylesheet" href="<?= $sdkConfig['cssEndpoint'] ?>">
<script src="<?= $sdkConfig['jsClassicEndpoint'] ?>"></script>

<!-- Configuración -->
<script>
window.checkoutConfig = {
    planId: <?= $plan['id'] ?>,
    amount: <?= $plan['price'] ?>,
    planName: '<?= addslashes($plan['name']) ?>',
    csrfToken: '<?= getCsrfToken() ?>',
    processUrl: '<?= url('/pago/procesar-session-sdk') ?>', // Nueva URL para SDK Web
    successUrl: '<?= $returnUrls['success'] ?>',
    failedUrl: '<?= $returnUrls['failed'] ?>'
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias DOM
    const paymentMethods = document.getElementById('payment-methods');
    const paymentForm = document.getElementById('payment-form');
    const loading = document.getElementById('loading');
    const errorContainer = document.getElementById('error-container');
    const loadingText = document.getElementById('loading-text');
    const errorMessage = document.getElementById('error-message');
    const paymentTitle = document.getElementById('payment-title');
    const izipayContainer = document.getElementById('izipay-container');
    
    let currentMethod = null;
    let currentSession = null;
    let isProcessing = false;
    
    // Estados de la UI
    function showPaymentMethods() {
        paymentMethods.classList.remove('d-none');
        paymentForm.classList.add('d-none');
        loading.classList.add('d-none');
        errorContainer.classList.add('d-none');
        currentMethod = null;
        isProcessing = false;
    }
    
    function showLoading(text = 'Preparando pago seguro...') {
        paymentMethods.classList.add('d-none');
        paymentForm.classList.add('d-none');
        errorContainer.classList.add('d-none');
        loading.classList.remove('d-none');
        loadingText.textContent = text;
        isProcessing = true;
    }
    
    function showError(msg) {
        console.error('Payment Error:', msg);
        paymentMethods.classList.add('d-none');
        paymentForm.classList.add('d-none');
        loading.classList.add('d-none');
        errorContainer.classList.remove('d-none');
        errorMessage.textContent = msg;
        isProcessing = false;
    }
    
    function showPaymentForm(method) {
        paymentMethods.classList.add('d-none');
        loading.classList.add('d-none');
        errorContainer.classList.add('d-none');
        paymentForm.classList.remove('d-none');
        
        const methodNames = {
            'yape': 'Pago con Yape',
            'qr': 'Pago con QR',
            'card': 'Pago con Tarjeta',
            'plin': 'Pago con Plin'
        };
        paymentTitle.textContent = methodNames[method] || 'Completar Pago';
        isProcessing = false;
    }
    
    // Inicializar pago
    async function initializePayment(method) {
        if (isProcessing) return;
        
        console.log('=== INICIANDO PAGO CON SDK WEB ===');
        console.log('Método:', method);
        
        currentMethod = method;
        
        const loadingTexts = {
            'yape': 'Configurando Yape...',
            'qr': 'Generando código QR...',
            'card': 'Preparando formulario de tarjeta...',
            'plin': 'Configurando Plin...'
        };
        
        showLoading(loadingTexts[method] || 'Preparando pago...');
        
        try {
            // Crear sesión de pago
            const response = await fetch(window.checkoutConfig.processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    plan_id: window.checkoutConfig.planId,
                    payment_method: method,
                    csrf_token: window.checkoutConfig.csrfToken
                })
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error HTTP ${response.status}: ${errorText}`);
            }
            
            const data = await response.json();
            console.log('Respuesta del servidor:', data);
            
            if (!data.success) {
                throw new Error(data.error || 'Error al crear la sesión de pago');
            }
            
            currentSession = data.session;
            
            // Configurar formulario específico
            await setupPaymentMethod(method, data.session);
            
        } catch (error) {
            console.error('Error:', error);
            showError(error.message || 'Error al inicializar el pago');
        }
    }
    
    // Configurar método de pago específico
    async function setupPaymentMethod(method, session) {
        console.log('=== CONFIGURANDO MÉTODO:', method.toUpperCase(), '===');
        
        // Verificar que el SDK esté disponible
        if (typeof KR === 'undefined') {
            throw new Error('SDK de Izipay no disponible');
        }
        
        // Limpiar contenedor
        izipayContainer.innerHTML = '';
        
        try {
            // Configurar HTML específico para cada método
            switch (method) {
                case 'yape':
                    await setupYapePayment(session);
                    break;
                case 'qr':
                    await setupQRPayment(session);
                    break;
                case 'card':
                    await setupCardPayment(session);
                    break;
                case 'plin':
                    await setupPlinPayment(session);
                    break;
                default:
                    throw new Error('Método de pago no soportado');
            }
            
        } catch (error) {
            console.error('Error configurando método:', error);
            throw error;
        }
    }
    
    // Configurar Yape
    async function setupYapePayment(session) {
        izipayContainer.innerHTML = `
            <div class="yape-container">
                <div class="text-center mb-4">
                    <i class="fas fa-mobile-alt fa-4x text-success mb-3"></i>
                    <h5>Pagar con Yape</h5>
                    <p class="text-muted">Completa el pago con tu app Yape</p>
                </div>
                
                <div class="kr-embedded" 
                     kr-form-token=""
                     kr-payment-method="yape">
                </div>
                
                <button class="kr-payment-button btn btn-success btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    Pagar S/. ${session.amount.toFixed(2)} con Yape
                </button>
            </div>
        `;
        
        await renderKryptonForm(session);
    }
    
    // Configurar QR
    async function setupQRPayment(session) {
        izipayContainer.innerHTML = `
            <div class="qr-container">
                <div class="text-center mb-4">
                    <i class="fas fa-qrcode fa-4x text-info mb-3"></i>
                    <h5>Pagar con QR</h5>
                    <p class="text-muted">Escanea el código con tu billetera móvil</p>
                </div>
                
                <div class="kr-embedded" 
                     kr-form-token=""
                     kr-payment-method="qr">
                </div>
            </div>
        `;
        
        await renderKryptonForm(session);
    }
    
    // Configurar Tarjeta
    async function setupCardPayment(session) {
        izipayContainer.innerHTML = `
            <div class="card-container">
                <div class="text-center mb-4">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h5>Pagar con Tarjeta</h5>
                    <div class="mb-3">
                        <i class="fab fa-cc-visa fa-2x mr-2 text-primary"></i>
                        <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                    </div>
                </div>
                
                <div class="kr-embedded" kr-form-token="">
                </div>
                
                <button class="kr-payment-button btn btn-primary btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-lock mr-2"></i>
                    Pagar S/. ${session.amount.toFixed(2)}
                </button>
            </div>
        `;
        
        await renderKryptonForm(session);
    }
    
    // Configurar Plin
    async function setupPlinPayment(session) {
        izipayContainer.innerHTML = `
            <div class="plin-container">
                <div class="text-center mb-4">
                    <i class="fas fa-university fa-4x text-warning mb-3"></i>
                    <h5>Pagar con Plin</h5>
                    <p class="text-muted">Usa tu número Interbank</p>
                </div>
                
                <div class="kr-embedded" 
                     kr-form-token=""
                     kr-payment-method="plin">
                </div>
                
                <button class="kr-payment-button btn btn-warning btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-university mr-2"></i>
                    Pagar S/. ${session.amount.toFixed(2)} con Plin
                </button>
            </div>
        `;
        
        await renderKryptonForm(session);
    }
    
    // Renderizar formulario Krypton
    async function renderKryptonForm(session) {
        return new Promise((resolve, reject) => {
            console.log('Renderizando formulario Krypton...');
            
            // Configurar datos para el formulario
            const embeddedElement = document.querySelector('.kr-embedded');
            if (!embeddedElement) {
                reject(new Error('Elemento .kr-embedded no encontrado'));
                return;
            }
            
            // Configurar atributos del formulario
            embeddedElement.setAttribute('kr-form-token', ''); // Se genera automáticamente
            
            // Configurar eventos del SDK
            KR.onError(function(event) {
                console.error('Error del SDK:', event);
                reject(new Error(`Error: ${event.errorMessage} (${event.errorCode})`));
            });
            
            KR.onFormReady(function() {
                console.log('Formulario listo');
                showPaymentForm(currentMethod);
                
                // Mostrar botón si existe
                const button = document.querySelector('.kr-payment-button');
                if (button) {
                    button.style.display = 'block';
                }
                
                resolve();
            });
            
            KR.onSubmit(function(event) {
                console.log('Enviando pago:', event);
                showLoading('Procesando tu pago...');
                // El SDK maneja la redirección automáticamente
                return true;
            });
            
            // Renderizar elementos
            KR.renderElements();
        });
    }
    
    // Event listeners
    document.querySelectorAll('button[data-method]').forEach(button => {
        button.addEventListener('click', function() {
            const method = this.getAttribute('data-method');
            initializePayment(method);
        });
    });
    
    document.getElementById('back-to-methods').addEventListener('click', function() {
        if (typeof KR !== 'undefined') {
            try {
                KR.removeForms();
            } catch (e) {
                console.log('Error limpiando formularios:', e);
            }
        }
        showPaymentMethods();
    });
    
    document.getElementById('retry-payment').addEventListener('click', function() {
        if (currentMethod) {
            initializePayment(currentMethod);
        } else {
            showPaymentMethods();
        }
    });
    
    document.getElementById('back-to-start').addEventListener('click', showPaymentMethods);
    
    console.log('=== SDK WEB CHECKOUT INICIALIZADO ===');
});
</script>

<style>
.payment-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.payment-card[data-method="yape"]:hover {
    border-color: #28a745;
    box-shadow: 0 8px 25px rgba(40,167,69,0.3);
}

.payment-card[data-method="qr"]:hover {
    border-color: #17a2b8;
    box-shadow: 0 8px 25px rgba(23,162,184,0.3);
}

.payment-card[data-method="card"]:hover {
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0,123,255,0.3);
}

.payment-card[data-method="plin"]:hover {
    border-color: #ffc107;
    box-shadow: 0 8px 25px rgba(255,193,7,0.3);
}

.payment-icon {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kr-embedded {
    background: white;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    min-height: 250px;
}

.kr-payment-button {
    font-size: 1.125rem !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}

.kr-payment-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
}

/* Estilos específicos por método */
.yape-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e8 100%);
    padding: 20px;
    border-radius: 15px;
}

.qr-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    padding: 20px;
    border-radius: 15px;
}

.card-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e6f3ff 100%);
    padding: 20px;
    border-radius: 15px;
}

.plin-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff8e1 100%);
    padding: 20px;
    border-radius: 15px;
}
</style>