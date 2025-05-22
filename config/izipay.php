<?php
// config/izipay.php

// Entorno (TEST o PRODUCTION)
define('IZIPAY_ENVIRONMENT', 'TEST'); // ← Mantener en TEST para pruebas

// Credenciales para entorno de pruebas - CONFIGURADAS
define('IZIPAY_TEST_MERCHANT_ID', '13448745');
define('IZIPAY_TEST_API_KEY', '13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb');
define('IZIPAY_TEST_SECRET_KEY', 'testpassword_9Fjd8w0VmP0lMLkWCIOOCEyTmQl2NBiH1ilk98X6E9b5q');
define('IZIPAY_TEST_HMAC_KEY', '1WmjIjvBCSHmnfA6GFmZrqsN9wUROW4DN2YRnk0yMadu9');
define('IZIPAY_TEST_ENDPOINT', 'https://api.micuentaweb.pe/vads-payment/');

// Credenciales para entorno de producción - PARA MÁS ADELANTE
define('IZIPAY_PRODUCTION_MERCHANT_ID', '13448745');
define('IZIPAY_PRODUCTION_API_KEY', 'CAMBIAR_POR_CLAVE_PUBLICA_PRODUCCION');
define('IZIPAY_PRODUCTION_SECRET_KEY', 'CAMBIAR_POR_CONTRASEÑA_PRODUCCION');
define('IZIPAY_PRODUCTION_HMAC_KEY', 'CAMBIAR_POR_HMAC_PRODUCCION');
define('IZIPAY_PRODUCTION_ENDPOINT', 'https://api.micuentaweb.pe/vads-payment/');

// URLs de retorno
define('IZIPAY_RETURN_URL', '/pago/confirmacion');
define('IZIPAY_CANCEL_URL', '/pago/fallido');
define('IZIPAY_NOTIFICATION_URL', '/api/pago/ipn');

// Obtener configuración basada en el entorno
function getIzipayConfig() {
    if (IZIPAY_ENVIRONMENT === 'PRODUCTION') {
        return [
            'merchantId' => IZIPAY_PRODUCTION_MERCHANT_ID,
            'apiKey' => IZIPAY_PRODUCTION_API_KEY,
            'secretKey' => IZIPAY_PRODUCTION_SECRET_KEY,
            'hmacKey' => IZIPAY_PRODUCTION_HMAC_KEY,
            'endpointUrl' => IZIPAY_PRODUCTION_ENDPOINT
        ];
    } else {
        return [
            'merchantId' => IZIPAY_TEST_MERCHANT_ID,
            'apiKey' => IZIPAY_TEST_API_KEY,
            'secretKey' => IZIPAY_TEST_SECRET_KEY,
            'hmacKey' => IZIPAY_TEST_HMAC_KEY,
            'endpointUrl' => IZIPAY_TEST_ENDPOINT
        ];
    }
}

// Obtener URL completa - Actualizada para HTTPS
function getFullIzipayUrl($path) {
    // Detectar si estamos en producción
    $isProduction = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'erophia.com';
    
    if ($isProduction) {
        // En producción, siempre usar HTTPS
        $protocol = 'https://';
        $host = 'erophia.com';
        $basePath = '/public';
    } else {
        // En desarrollo, detectar automáticamente
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
    }
    
    return $protocol . $host . $basePath . $path;
}