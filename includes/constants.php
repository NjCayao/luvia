<?php
// includes/constants.php

// Directorios
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Configuración de media
define('MAX_PHOTO_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_VIDEO_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/quicktime']);

// Configuración de prueba gratuita
define('FREE_TRIAL_DAYS', 7);

// Verificación SMS
define('SMS_VERIFICATION_ENABLED', true);