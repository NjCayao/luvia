<?php
// config/izipay.php - CONFIGURACIÓN A VERIFICAR

// 1. VERIFICAR EN TU PANEL DE IZIPAY:
// - Merchant ID: debe coincidir con IZIPAY_TEST_USERNAME
// - Clave de Test: debe coincidir con IZIPAY_TEST_PASSWORD
// - Clave pública: debe coincidir con IZIPAY_TEST_PUBLIC_KEY
// - Clave HMAC: debe coincidir con IZIPAY_TEST_HMAC_KEY

// 2. URLs DE RETORNO EN EL PANEL DE IZIPAY (configurar en el back office):
// URL de retorno exitoso: https://erophia.com/pago/confirmacion (o http://localhost/luvia/public/pago/confirmacion)
// URL de retorno fallido: https://erophia.com/pago/fallido (o http://localhost/luvia/public/pago/fallido)
// URL de notificación IPN: https://erophia.com/api/pago/ipn (o http://localhost/luvia/public/api/pago/ipn)

// Entorno (TEST o PRODUCTION)
define('IZIPAY_ENVIRONMENT', 'TEST');

// Credenciales para API REST V4.0 - ACTUALIZAR CON TUS DATOS REALES
define('IZIPAY_TEST_USERNAME', '13448745'); // Tu Merchant ID
define('IZIPAY_TEST_PASSWORD', 'testpassword_9Fjd8w0VmP0lMLkWCIOOCEyTmQl2NBiH1ilk98X6E9b5q'); // Tu clave de test
define('IZIPAY_TEST_PUBLIC_KEY', '13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb'); // Tu clave pública
define('IZIPAY_TEST_HMAC_KEY', '1WmjIjvBCSHmnfA6GFmZrqsN9wUROW4DN2YRnk0yMadu9'); // Tu clave HMAC

// URLs de la API REST V4.0 - ESTAS SON CORRECTAS
define('IZIPAY_TEST_API_ENDPOINT', 'https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment');
define('IZIPAY_TEST_CLIENT_ENDPOINT', 'https://api.micuentaweb.pe');

// Credenciales para producción (cambiar cuando vayas a producción)
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

// Resto del archivo... (funciones existentes)
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