<?php
// config/izipay_sdk_web.php - CONFIGURACIÓN PARA SDK WEB DE IZIPAY

// ===============================================
// CREDENCIALES DE TEST - SDK WEB (FUNCIONAN INMEDIATAMENTE)
// ===============================================

// Entorno
define('IZIPAY_SDK_ENVIRONMENT', 'TEST');

// Credenciales de prueba oficiales del SDK Web
define('IZIPAY_SDK_TEST_SHOP_ID', '13448745');
define('IZIPAY_SDK_TEST_PUBLIC_KEY', '13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb');
define('IZIPAY_SDK_TEST_PRIVATE_KEY', 'testpassword_9Fjd8w0VmP0lMLkWCIOOCEyTmQl2NBiH1ilk98X6E9b5q');
define('IZIPAY_SDK_TEST_HMAC_KEY', '1WmjIjvBCSHmnfA6GFmZrqsN9wUROW4DN2YRnk0yMadu9');

// URLs del SDK Web (diferentes al API REST)
define('IZIPAY_SDK_TEST_ENDPOINT', 'https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js');
define('IZIPAY_SDK_TEST_CSS_ENDPOINT', 'https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css');
define('IZIPAY_SDK_TEST_JS_ENDPOINT', 'https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js');

// Para producción (cambiar cuando vayas a producción)
define('IZIPAY_SDK_PRODUCTION_SHOP_ID', 'TU_SHOP_ID_REAL');
define('IZIPAY_SDK_PRODUCTION_PUBLIC_KEY', 'TU_PUBLIC_KEY_REAL');
define('IZIPAY_SDK_PRODUCTION_PRIVATE_KEY', 'TU_PRIVATE_KEY_REAL');
define('IZIPAY_SDK_PRODUCTION_HMAC_KEY', 'TU_HMAC_KEY_REAL');

// URLs de retorno
define('IZIPAY_SDK_SUCCESS_URL', '/pago/confirmacion');
define('IZIPAY_SDK_FAILED_URL', '/pago/fallido');
define('IZIPAY_SDK_IPN_URL', '/api/pago/ipn');

/**
 * Obtiene configuración del SDK Web
 */
function getIzipaySdkConfig() {
    if (IZIPAY_SDK_ENVIRONMENT === 'PRODUCTION') {
        return [
            'shopId' => IZIPAY_SDK_PRODUCTION_SHOP_ID,
            'publicKey' => IZIPAY_SDK_PRODUCTION_PUBLIC_KEY,
            'privateKey' => IZIPAY_SDK_PRODUCTION_PRIVATE_KEY,
            'hmacKey' => IZIPAY_SDK_PRODUCTION_HMAC_KEY,
            'jsEndpoint' => str_replace('/test/', '/prod/', IZIPAY_SDK_TEST_ENDPOINT),
            'cssEndpoint' => str_replace('/test/', '/prod/', IZIPAY_SDK_TEST_CSS_ENDPOINT)
        ];
    } else {
        return [
            'shopId' => IZIPAY_SDK_TEST_SHOP_ID,
            'publicKey' => IZIPAY_SDK_TEST_PUBLIC_KEY,
            'privateKey' => IZIPAY_SDK_TEST_PRIVATE_KEY,
            'hmacKey' => IZIPAY_SDK_TEST_HMAC_KEY,
            'jsEndpoint' => IZIPAY_SDK_TEST_ENDPOINT,
            'cssEndpoint' => IZIPAY_SDK_TEST_CSS_ENDPOINT,
            'jsClassicEndpoint' => IZIPAY_SDK_TEST_JS_ENDPOINT
        ];
    }
}

/**
 * Genera FormToken para SDK Web (método simplificado)
 */
function generateSdkFormToken($amount, $orderId, $userEmail, $description = '') {
    // Convertir a céntimos
    $amountCents = (int)($amount * 100);
    
    // Datos básicos para el token
    $tokenData = [
        'amount' => $amountCents,
        'currency' => 'PEN',
        'orderId' => $orderId,
        'mode' => IZIPAY_SDK_ENVIRONMENT,
        'version' => 4,
        'customer' => [
            'email' => $userEmail
        ],
        'metadata' => [
            'description' => $description
        ]
    ];
    
    // Para el SDK Web, el token se genera automáticamente
    // Solo necesitamos devolver la configuración
    return [
        'success' => true,
        'config' => getIzipaySdkConfig(),
        'tokenData' => $tokenData,
        'orderId' => $orderId
    ];
}

/**
 * URLs completas para el SDK
 */
function getSdkReturnUrls() {
    $isProduction = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'erophia.com';
    
    if ($isProduction) {
        $baseUrl = 'https://erophia.com';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));
        if (strpos($_SERVER['REQUEST_URI'], '/public/') !== false) {
            $basePath .= '/public';
        }
        $baseUrl = $protocol . $host . $basePath;
    }
    
    return [
        'success' => $baseUrl . IZIPAY_SDK_SUCCESS_URL,
        'failed' => $baseUrl . IZIPAY_SDK_FAILED_URL,
        'ipn' => $baseUrl . IZIPAY_SDK_IPN_URL
    ];
}