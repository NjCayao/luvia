<!-- views/payment/checkout.php - CON SDK WEB DE IZIPAY -->
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

                    <!-- Opciones de pago con SDK Web -->
                    <div id="payment-options">
                        <h5 class="mb-4">Elige tu método de pago:</h5>
                        
                        <div class="row">
                            <!-- Yape - MÁS DESTACADO -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-method-card h-100" data-method="yape">
                                    <div class="card-body text-center">
                                        <div class="method-icon mb-3">
                                            <i class="fas fa-mobile-alt fa-3x text-success"></i>
                                        </div>
                                        <h5 class="text-success">Yape</h5>
                                        <p class="text-muted small">Pago instantáneo con tu celular</p>
                                        <span class="badge badge-success">MÁS POPULAR</span>
                                        <button type="button" class="btn btn-success btn-block mt-3" id="pay-with-yape">
                                            <i class="fas fa-mobile-alt mr-2"></i>
                                            Pagar con Yape
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- QR - SEGUNDO MÁS POPULAR -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-method-card h-100" data-method="qr">
                                    <div class="card-body text-center">
                                        <div class="method-icon mb-3">
                                            <i class="fas fa-qrcode fa-3x text-info"></i>
                                        </div>
                                        <h5 class="text-info">QR</h5>
                                        <p class="text-muted small">Escanea con cualquier app</p>
                                        <span class="badge badge-info">RÁPIDO</span>
                                        <button type="button" class="btn btn-info btn-block mt-3" id="pay-with-qr">
                                            <i class="fas fa-qrcode mr-2"></i>
                                            Pagar con QR
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tarjetas -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-method-card h-100" data-method="card">
                                    <div class="card-body text-center">
                                        <div class="method-icon mb-3">
                                            <i class="fas fa-credit-card fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="text-primary">Tarjeta</h5>
                                        <p class="text-muted small">Visa, Mastercard, Amex</p>
                                        <div class="card-logos">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-block mt-3" id="pay-with-card">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Pagar con Tarjeta
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Plin -->
                            <div class="col-md-6 mb-3">
                                <div class="card payment-method-card h-100" data-method="plin">
                                    <div class="card-body text-center">
                                        <div class="method-icon mb-3">
                                            <i class="fas fa-university fa-3x text-warning"></i>
                                        </div>
                                        <h5 class="text-warning">Plin</h5>
                                        <p class="text-muted small">Interbank - Con tu celular</p>
                                        <button type="button" class="btn btn-warning btn-block mt-3" id="pay-with-plin">
                                            <i class="fas fa-university mr-2"></i>
                                            Pagar con Plin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información de seguridad -->
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <strong>Pago 100% seguro:</strong> Procesado por Izipay con encriptación SSL.
                            Todos los métodos son seguros y confiables.
                        </div>
                    </div>

                    <!-- Formulario de pago (se muestra según el método elegido) -->
                    <div id="payment-form-container" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 id="payment-method-title">Completar Pago</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="back-to-methods">
                                <i class="fas fa-arrow-left mr-1"></i> Cambiar método
                            </button>
                        </div>
                        
                        <!-- Contenedor dinámico del formulario -->
                        <div id="izipay-form-wrapper">
                            <!-- Se genera dinámicamente según el método -->
                        </div>
                    </div>

                    <!-- Estados de carga y error -->
                    <div id="loading-state" class="text-center d-none py-5">
                        <div class="spinner-border text-primary mb-3"></div>
                        <h5 id="loading-text">Preparando pago seguro...</h5>
                    </div>

                    <div class="alert alert-danger d-none" id="error-state">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="error-message"></span>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="retry-payment">
                                <i class="fas fa-redo mr-1"></i> Reintentar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Resumen igual que antes -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-receipt mr-2"></i>Resumen</h4>
                </div>
                <div class="card-body">
                    <p><strong>Plan:</strong> <?= htmlspecialchars($plan['name']) ?></p>
                    <p><strong>Duración:</strong> <?= $plan['duration'] ?> días</p>
                    <hr>
                    <div class="row">
                        <div class="col-6"><strong>Total:</strong></div>
                        <div class="col-6 text-right"><strong class="text-primary">S/. <?= number_format($plan['price'], 2) ?></strong></div>
                    </div>
                </div>
            </div>
            
            <!-- Información de Yape -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-mobile-alt mr-1 text-success"></i>¿Por qué elegir Yape?</h6>
                    <ul class="small">
                        <li>✅ Pago instantáneo</li>
                        <li>✅ Sin comisiones adicionales</li>
                        <li>✅ Más seguro que efectivo</li>
                        <li>✅ Usado por +17 millones de peruanos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SDK Web de Izipay -->
<script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="<?php
            require_once __DIR__ . '/../../config/izipay.php';
            $config = getIzipayConfig();
            echo htmlspecialchars($config['publicKey']);
        ?>"
        kr-post-url-success="<?= htmlspecialchars(url('/pago/confirmacion')) ?>"
        kr-post-url-refused="<?= htmlspecialchars(url('/pago/fallido')) ?>"
        kr-language="es-ES">
</script>
<link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
<script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>

<!-- Configuración -->
<script>
window.checkoutConfig = {
    planId: <?= $plan['id'] ?>,
    amount: <?= $plan['price'] ?>,
    planName: '<?= addslashes($plan['name']) ?>',
    csrfToken: '<?= getCsrfToken() ?>',
    processUrl: '<?= url('/pago/procesar-session') ?>'
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.getElementById('payment-options');
    const paymentFormContainer = document.getElementById('payment-form-container');
    const loadingState = document.getElementById('loading-state');
    const errorState = document.getElementById('error-state');
    const loadingText = document.getElementById('loading-text');
    const errorMessage = document.getElementById('error-message');
    const formWrapper = document.getElementById('izipay-form-wrapper');
    const methodTitle = document.getElementById('payment-method-title');
    const backToMethodsBtn = document.getElementById('back-to-methods');
    
    let currentMethod = null;
    let isProcessing = false;
    
    // Estados de la interfaz
    function showPaymentOptions() {
        paymentOptions.classList.remove('d-none');
        paymentFormContainer.classList.add('d-none');
        loadingState.classList.add('d-none');
        errorState.classList.add('d-none');
        currentMethod = null;
        isProcessing = false;
    }
    
    function showLoading(text = 'Preparando pago seguro...') {
        paymentOptions.classList.add('d-none');
        paymentFormContainer.classList.add('d-none');
        errorState.classList.add('d-none');
        loadingState.classList.remove('d-none');
        loadingText.textContent = text;
        isProcessing = true;
    }
    
    function showError(msg) {
        console.error('Payment Error:', msg);
        paymentOptions.classList.add('d-none');
        paymentFormContainer.classList.add('d-none');
        loadingState.classList.add('d-none');
        errorState.classList.remove('d-none');
        errorMessage.textContent = msg;
        isProcessing = false;
    }
    
    function showPaymentForm(method) {
        paymentOptions.classList.add('d-none');
        loadingState.classList.add('d-none');
        errorState.classList.add('d-none');
        paymentFormContainer.classList.remove('d-none');
        
        // Actualizar título según método
        const methodNames = {
            'yape': 'Pago con Yape',
            'qr': 'Pago con QR',
            'card': 'Pago con Tarjeta',
            'plin': 'Pago con Plin'
        };
        methodTitle.textContent = methodNames[method] || 'Completar Pago';
        isProcessing = false;
    }
    
    // Inicializar pago con método específico
    async function initializePayment(method) {
        if (isProcessing) return;
        
        console.log('Iniciando pago con método:', method);
        currentMethod = method;
        
        const methodTexts = {
            'yape': 'Configurando Yape...',
            'qr': 'Generando código QR...',
            'card': 'Preparando formulario de tarjeta...',
            'plin': 'Configurando Plin...'
        };
        
        showLoading(methodTexts[method] || 'Preparando pago...');
        
        try {
            // Crear sesión de pago
            const response = await fetch(window.checkoutConfig.processUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    plan_id: window.checkoutConfig.planId,
                    payment_method: method,
                    csrf_token: window.checkoutConfig.csrfToken
                })
            });
            
            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.success || !data.session?.formToken) {
                throw new Error(data.error || 'Error al crear la sesión de pago');
            }
            
            // Configurar formulario específico del método
            await setupPaymentMethod(method, data.session.formToken);
            
        } catch (error) {
            console.error('Error inicializando pago:', error);
            showError(error.message || 'Error al inicializar el pago');
        }
    }
    
    // Configurar método de pago específico
    async function setupPaymentMethod(method, formToken) {
        console.log('Configurando método:', method, 'con token:', formToken.substring(0, 20) + '...');
        
        // Limpiar contenedor
        formWrapper.innerHTML = '';
        
        // Verificar que KR esté disponible
        if (typeof KR === 'undefined') {
            throw new Error('SDK de Izipay no disponible');
        }
        
        try {
            // Configurar según el método
            switch (method) {
                case 'yape':
                    await setupYapePayment(formToken);
                    break;
                case 'qr':
                    await setupQRPayment(formToken);
                    break;
                case 'card':
                    await setupCardPayment(formToken);
                    break;
                case 'plin':
                    await setupPlinPayment(formToken);
                    break;
                default:
                    throw new Error('Método de pago no soportado');
            }
            
        } catch (error) {
            console.error('Error configurando método:', error);
            throw error;
        }
    }
    
    // Configurar pago con Yape
    async function setupYapePayment(formToken) {
        formWrapper.innerHTML = `
            <div class="yape-payment">
                <div class="text-center mb-4">
                    <i class="fas fa-mobile-alt fa-4x text-success mb-3"></i>
                    <h5>Pagar con Yape</h5>
                    <p class="text-muted">Usa tu app Yape para completar el pago</p>
                </div>
                
                <div class="kr-embedded" kr-form-token="${formToken}" kr-payment-method="yape">
                    <!-- Formulario Yape se carga aquí -->
                </div>
                
                <button class="kr-payment-button btn btn-success btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    Pagar S/. ${window.checkoutConfig.amount.toFixed(2)} con Yape
                </button>
            </div>
        `;
        
        await renderIzipayForm();
    }
    
    // Configurar pago con QR
    async function setupQRPayment(formToken) {
        formWrapper.innerHTML = `
            <div class="qr-payment">
                <div class="text-center mb-4">
                    <i class="fas fa-qrcode fa-4x text-info mb-3"></i>
                    <h5>Pagar con QR</h5>
                    <p class="text-muted">Escanea el código con tu billetera móvil</p>
                </div>
                
                <div class="kr-embedded" kr-form-token="${formToken}" kr-payment-method="qr">
                    <!-- Código QR se genera aquí -->
                </div>
            </div>
        `;
        
        await renderIzipayForm();
    }
    
    // Configurar pago con tarjeta
    async function setupCardPayment(formToken) {
        formWrapper.innerHTML = `
            <div class="card-payment">
                <div class="text-center mb-4">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h5>Pagar con Tarjeta</h5>
                    <div class="card-logos">
                        <i class="fab fa-cc-visa fa-2x mr-2"></i>
                        <i class="fab fa-cc-mastercard fa-2x"></i>
                    </div>
                </div>
                
                <div class="kr-embedded" kr-form-token="${formToken}">
                    <!-- Formulario de tarjeta se carga aquí -->
                </div>
                
                <button class="kr-payment-button btn btn-primary btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-lock mr-2"></i>
                    Pagar S/. ${window.checkoutConfig.amount.toFixed(2)}
                </button>
            </div>
        `;
        
        await renderIzipayForm();
    }
    
    // Configurar pago con Plin
    async function setupPlinPayment(formToken) {
        formWrapper.innerHTML = `
            <div class="plin-payment">
                <div class="text-center mb-4">
                    <i class="fas fa-university fa-4x text-warning mb-3"></i>
                    <h5>Pagar con Plin</h5>
                    <p class="text-muted">Usa tu número Interbank</p>
                </div>
                
                <div class="kr-embedded" kr-form-token="${formToken}" kr-payment-method="plin">
                    <!-- Formulario Plin se carga aquí -->
                </div>
                
                <button class="kr-payment-button btn btn-warning btn-lg btn-block mt-3" style="display: none;">
                    <i class="fas fa-university mr-2"></i>
                    Pagar S/. ${window.checkoutConfig.amount.toFixed(2)} con Plin
                </button>
            </div>
        `;
        
        await renderIzipayForm();
    }
    
    // Renderizar formulario de Izipay
    async function renderIzipayForm() {
        return new Promise((resolve, reject) => {
            // Configurar eventos
            KR.onError(function(event) {
                console.error('Error Izipay:', event);
                reject(new Error(`Error: ${event.errorMessage} (${event.errorCode})`));
            });
            
            KR.onFormReady(function() {
                console.log('Formulario Izipay listo');
                showPaymentForm(currentMethod);
                
                // Mostrar botón si existe
                const button = document.querySelector('.kr-payment-button');
                if (button) {
                    button.style.display = 'block';
                }
                
                resolve();
            });
            
            KR.onSubmit(function(event) {
                console.log('Pago enviado:', event);
                showLoading('Procesando tu pago...');
                return true;
            });
            
            // Renderizar
            KR.renderElements();
        });
    }
    
    // Event listeners para botones de métodos de pago
    document.getElementById('pay-with-yape').addEventListener('click', () => initializePayment('yape'));
    document.getElementById('pay-with-qr').addEventListener('click', () => initializePayment('qr'));
    document.getElementById('pay-with-card').addEventListener('click', () => initializePayment('card'));
    document.getElementById('pay-with-plin').addEventListener('click', () => initializePayment('plin'));
    
    // Botón volver
    backToMethodsBtn.addEventListener('click', function() {
        if (typeof KR !== 'undefined') {
            try {
                KR.removeForms();
            } catch (e) {
                console.log('Error limpiando formularios:', e);
            }
        }
        showPaymentOptions();
    });
    
    // Botón retry
    document.getElementById('retry-payment').addEventListener('click', function() {
        if (currentMethod) {
            initializePayment(currentMethod);
        } else {
            showPaymentOptions();
        }
    });
    
    console.log('SDK Web Izipay inicializado');
});
</script>

<style>
.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-method-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.payment-method-card[data-method="yape"]:hover {
    border-color: #28a745;
    box-shadow: 0 8px 25px rgba(40,167,69,0.3);
}

.payment-method-card[data-method="qr"]:hover {
    border-color: #17a2b8;
    box-shadow: 0 8px 25px rgba(23,162,184,0.3);
}

.payment-method-card[data-method="card"]:hover {
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0,123,255,0.3);
}

.payment-method-card[data-method="plin"]:hover {
    border-color: #ffc107;
    box-shadow: 0 8px 25px rgba(255,193,7,0.3);
}

.method-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-logos i {
    font-size: 1.5rem;
    margin: 0 5px;
}

.kr-embedded {
    background: white;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    min-height: 200px;
}

.kr-payment-button {
    font-size: 1.1rem !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}

.kr-payment-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
}

/* Estilos específicos por método */
.yape-payment {
    background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e8 100%);
    padding: 20px;
    border-radius: 15px;
}

.qr-payment {
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    padding: 20px;
    border-radius: 15px;
}

.card-payment {
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    padding: 20px;
    border-radius: 15px;
}

.plin-payment {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff8e1 100%);
    padding: 20px;
    border-radius: 15px;
}
</style>