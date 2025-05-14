<?php
// config/app.php

// Información básica de la aplicación
define('APP_NAME', 'Luvia');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/luvia/public'); // Cambiar en producción
define('APP_ENV', 'development'); // 'development' o 'production'

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Configuración de sesiones - DEBE ir ANTES de cualquier output o session_start()
// Verificar si la sesión ya está iniciada para evitar advertencias
if (session_status() === PHP_SESSION_NONE) {
    // Configurar opciones de sesión
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (APP_ENV === 'production') {
        ini_set('session.cookie_secure', 1);
    }
    session_name('citasweb_session');
    
    // Ahora sí iniciar la sesión
    session_start();
}

// Configuración de errores
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}

// Directorio de uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');

// Límites de subida de archivos
define('MAX_PHOTO_SIZE', 5 * 1024 * 1024); // 5 MB
define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50 MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm']);

// Configuración de verificación
define('SMS_VERIFICATION_ENABLED', true);
define('EMAIL_VERIFICATION_ENABLED', true);
define('VERIFICATION_CODE_EXPIRY', 24 * 60 * 60); // 24 horas en segundos

// Configuración de planes
define('FREE_TRIAL_DAYS', 15);
define('VISITOR_SUBSCRIPTION_PRICE', 5.00); // Precio para visitantes (5 soles)