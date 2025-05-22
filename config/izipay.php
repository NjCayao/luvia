<?php
// config/izipay.php - NUEVA CONFIGURACIÓN PARA API REST V4.0

// Entorno (TEST o PRODUCTION)
define('IZIPAY_ENVIRONMENT', 'TEST');

// Credenciales para API REST V4.0 - VERIFICAR ESTAS CREDENCIALES EN TU PANEL
define('IZIPAY_TEST_USERNAME', '13448745'); // Mismo que merchant ID
define('IZIPAY_TEST_PASSWORD', 'testpassword_9Fjd8w0VmP0lMLkWCIOOCEyTmQl2NBiH1ilk98X6E9b5q');
define('IZIPAY_TEST_PUBLIC_KEY', '13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb');
define('IZIPAY_TEST_HMAC_KEY', '1WmjIjvBCSHmnfA6GFmZrqsN9wUROW4DN2YRnk0yMadu9');

// URLs de la API REST V4.0
define('IZIPAY_TEST_API_ENDPOINT', 'https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment');
define('IZIPAY_TEST_CLIENT_ENDPOINT', 'https://api.micuentaweb.pe');

// Credenciales para producción
define('IZIPAY_PRODUCTION_USERNAME', '13448745');
define('IZIPAY_PRODUCTION_PASSWORD', 'CAMBIAR_POR_CONTRASEÑA_PRODUCCION');
define('IZIPAY_PRODUCTION_PUBLIC_KEY', 'CAMBIAR_POR_CLAVE_PUBLICA_PRODUCCION');
define('IZIPAY_PRODUCTION_HMAC_KEY', 'CAMBIAR_POR_HMAC_PRODUCCION');
define('IZIPAY_PRODUCTION_API_ENDPOINT', 'https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment');
define('IZIPAY_PRODUCTION_CLIENT_ENDPOINT', 'https://api.micuentaweb.pe');

// URLs de retorno
define('IZIPAY_RETURN_URL', '/pago/confirmacion');
define('IZIPAY_CANCEL_URL', '/pago/fallido');
define('IZIPAY_NOTIFICATION_URL', '/api/pago/ipn');

// Obtener configuración basada en el entorno
function getIzipayConfig() {
    if (IZIPAY_ENVIRONMENT === 'PRODUCTION') {
        return [
            'username' => IZIPAY_PRODUCTION_USERNAME,
            'password' => IZIPAY_PRODUCTION_PASSWORD,
            'publicKey' => IZIPAY_PRODUCTION_PUBLIC_KEY,
            'hmacKey' => IZIPAY_PRODUCTION_HMAC_KEY,
            'apiEndpoint' => IZIPAY_PRODUCTION_API_ENDPOINT,
            'clientEndpoint' => IZIPAY_PRODUCTION_CLIENT_ENDPOINT
        ];
    } else {
        return [
            'username' => IZIPAY_TEST_USERNAME,
            'password' => IZIPAY_TEST_PASSWORD,
            'publicKey' => IZIPAY_TEST_PUBLIC_KEY,
            'hmacKey' => IZIPAY_TEST_HMAC_KEY,
            'apiEndpoint' => IZIPAY_TEST_API_ENDPOINT,
            'clientEndpoint' => IZIPAY_TEST_CLIENT_ENDPOINT
        ];
    }
}

// Obtener URL completa
function getFullIzipayUrl($path) {
    $isProduction = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'erophia.com';
    
    if ($isProduction) {
        return 'https://erophia.com' . $path;
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . $host . $basePath . $path;
    }
}