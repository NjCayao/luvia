<?php
// config/izipay.php

// Entorno (TEST o PRODUCTION)
define('IZIPAY_ENVIRONMENT', 'TEST');

// Credenciales para entorno de pruebas
define('IZIPAY_TEST_MERCHANT_ID', 'TEST_MERCHANT_ID');
define('IZIPAY_TEST_API_KEY', 'TEST_API_KEY');
define('IZIPAY_TEST_SECRET_KEY', 'TEST_SECRET_KEY');
define('IZIPAY_TEST_ENDPOINT', 'https://api.sandbox.izipay.pe/api/v1/');

// Credenciales para entorno de producción
define('IZIPAY_PRODUCTION_MERCHANT_ID', 'PRODUCTION_MERCHANT_ID');
define('IZIPAY_PRODUCTION_API_KEY', 'PRODUCTION_API_KEY');
define('IZIPAY_PRODUCTION_SECRET_KEY', 'PRODUCTION_SECRET_KEY');
define('IZIPAY_PRODUCTION_ENDPOINT', 'https://api.izipay.pe/api/v1/');

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
            'endpointUrl' => IZIPAY_PRODUCTION_ENDPOINT
        ];
    } else {
        return [
            'merchantId' => IZIPAY_TEST_MERCHANT_ID,
            'apiKey' => IZIPAY_TEST_API_KEY,
            'secretKey' => IZIPAY_TEST_SECRET_KEY,
            'endpointUrl' => IZIPAY_TEST_ENDPOINT
        ];
    }
}

// Obtener URL completa
function getFullIzipayUrl($path) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host . $path;
}