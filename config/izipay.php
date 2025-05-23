<?php
// config/izipay.php - CON VERIFICACIÓN DE CONFIGURACIÓN

// ⚠️ IMPORTANTE: VERIFICAR ESTAS CREDENCIALES EN EL PANEL DE IZIPAY
// Panel de Izipay: https://secure.micuentaweb.pe/vads-merchant/

// Entorno (TEST o PRODUCTION)
define('IZIPAY_ENVIRONMENT', 'TEST');

// ===============================================
// CREDENCIALES DE TEST - VERIFICAR EN EL PANEL
// ===============================================
define('IZIPAY_TEST_USERNAME', '13448745'); // Shop ID / Merchant ID
define('IZIPAY_TEST_PASSWORD', 'testpassword_9Fjd8w0VmP0lMLkWCIOOCEyTmQl2NBiH1ilk98X6E9b5q'); // Clave de test
define('IZIPAY_TEST_PUBLIC_KEY', '13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb'); // Clave pública
define('IZIPAY_TEST_HMAC_KEY', '1WmjIjvBCSHmnfA6GFmZrqsN9wUROW4DN2YRnk0yMadu9'); // Clave HMAC

// URLs de la API REST V4.0 - OFICIALES DE IZIPAY
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

/**
 * Obtiene configuración actual de Izipay
 */
function getIzipayConfig() {
    if (IZIPAY_ENVIRONMENT === 'PRODUCTION') {
        $config = [
            'username' => IZIPAY_PRODUCTION_USERNAME,
            'password' => IZIPAY_PRODUCTION_PASSWORD,
            'publicKey' => IZIPAY_PRODUCTION_PUBLIC_KEY,
            'hmacKey' => IZIPAY_PRODUCTION_HMAC_KEY,
            'apiEndpoint' => IZIPAY_PRODUCTION_API_ENDPOINT,
            'clientEndpoint' => IZIPAY_PRODUCTION_CLIENT_ENDPOINT
        ];
    } else {
        $config = [
            'username' => IZIPAY_TEST_USERNAME,
            'password' => IZIPAY_TEST_PASSWORD,
            'publicKey' => IZIPAY_TEST_PUBLIC_KEY,
            'hmacKey' => IZIPAY_TEST_HMAC_KEY,
            'apiEndpoint' => IZIPAY_TEST_API_ENDPOINT,
            'clientEndpoint' => IZIPAY_TEST_CLIENT_ENDPOINT
        ];
    }
    
    // Verificar que todas las credenciales estén configuradas
    foreach ($config as $key => $value) {
        if (empty($value) || strpos($value, 'CAMBIAR_POR_') !== false) {
            error_log("ERROR: Credencial de Izipay no configurada: $key");
        }
    }
    
    return $config;
}

/**
 * Construye URL completa para Izipay
 */
function getFullIzipayUrl($path) {
    // Detectar si estamos en producción
    $isProduction = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'erophia.com';
    
    if ($isProduction) {
        return 'https://erophia.com' . $path;
    } else {
        // Para desarrollo local
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        
        // Detectar si estamos en una subcarpeta
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Si el script está en /public/, ajustar la ruta base
        if (strpos($scriptName, '/public/') !== false) {
            $basePath = str_replace('/public/index.php', '', $scriptName);
            return $protocol . $host . $basePath . '/public' . $path;
        } else {
            // Acceso directo (con .htaccess funcionando)
            $basePath = dirname($scriptName);
            if ($basePath === '/') $basePath = '';
            return $protocol . $host . $basePath . $path;
        }
    }
}

/**
 * Función de debug para verificar configuración
 */
function debugIzipayConfig() {
    if (!defined('APP_DEBUG') || !APP_DEBUG) {
        return; // Solo en modo debug
    }
    
    error_log("=== IZIPAY CONFIG DEBUG ===");
    error_log("Environment: " . IZIPAY_ENVIRONMENT);
    error_log("Username: " . IZIPAY_TEST_USERNAME);
    error_log("Password: " . substr(IZIPAY_TEST_PASSWORD, 0, 20) . "...");
    error_log("Public Key: " . substr(IZIPAY_TEST_PUBLIC_KEY, 0, 30) . "...");
    error_log("HMAC Key: " . substr(IZIPAY_TEST_HMAC_KEY, 0, 20) . "...");
    error_log("API Endpoint: " . IZIPAY_TEST_API_ENDPOINT);
    error_log("Client Endpoint: " . IZIPAY_TEST_CLIENT_ENDPOINT);
    
    $testUrl = getFullIzipayUrl('/pago/confirmacion');
    error_log("Test Return URL: $testUrl");
    error_log("=== END CONFIG DEBUG ===");
}

// Llamar debug si estamos en modo desarrollo
if (defined('APP_DEBUG') && APP_DEBUG) {
    debugIzipayConfig();
}