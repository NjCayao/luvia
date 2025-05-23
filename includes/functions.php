<?php
// includes/functions.php

/**
 * Redirecciona a una URL específica
 */
function redirect($url, $statusCode = 302) {
    // Si la URL comienza con /, construir URL completa usando la base
    if (strpos($url, '/') === 0) {
        $url = url(ltrim($url, '/')); 
    } elseif (strpos($url, 'http') !== 0) {
        // Si no es una URL absoluta, construirla
        $url = url($url);
    }
    
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Genera una URL completa basada en la URL base configurada
 */
function url($path = '') {
    // Si APP_URL está definido, usarlo como base
    if (defined('APP_URL')) {
        $baseUrl = APP_URL;
    } else {
        // Detectar automáticamente la base de la URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        
        // Obtener el directorio base
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        // Eliminar "/public" si está presente al final
        if (substr($scriptDir, -7) === '/public') {
            $baseDir = $scriptDir;
        } else {
            $baseDir = $scriptDir;
        }
        
        // Asegurar que la base termine con una barra diagonal
        $baseUrl = $protocol . $domainName . rtrim($baseDir, '/');
    }
    
    // Asegurar que $path no comience con barra diagonal para evitar duplicados
    $path = ltrim($path, '/');
    
    // Combinar base URL y path
    return rtrim($baseUrl, '/') . '/' . $path;
}

/**
 * Sanitiza la entrada del usuario para prevenir XSS
 */
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($value);
        }
    } else {
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    return $input;
}

/**
 * Genera un mensaje flash para mostrar en la siguiente solicitud
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Obtiene el mensaje flash y lo elimina
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flashMessage = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flashMessage;
    }
    return null;
}

/**
 * Muestra un mensaje flash si existe
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        
        $type = $message['type'];
        $text = $message['message'];
        
        echo "<div class='alert alert-{$type} alert-dismissible fade show'>
                {$text}
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
              </div>";
    }
}

/**
 * Genera un token CSRF para proteger formularios
 */
function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Alias para getCsrfToken() - mantiene compatibilidad con código existente
 */
function generateCsrfToken() {
    return getCsrfToken();
}

/**
 * Formatea una fecha para mostrarla
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    // Verificar si la fecha es null o vacía
    if ($date === null || empty($date)) {
        return '-'; // o cualquier texto que prefieras para fechas no disponibles
    }
    
    try {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        error_log("Error al formatear fecha: " . $e->getMessage());
        return 'Fecha inválida';
    }
}

/**
 * Genera una cadena aleatoria
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Verifica si un archivo es una imagen válida
 */
function isValidImage($file) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return false;
    }
    
    $mimeType = $imageInfo['mime'];
    return in_array($mimeType, ALLOWED_PHOTO_TYPES);
}

/**
 * Verifica si un archivo es un video válido
 */
function isValidVideo($file) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    return in_array($mimeType, ALLOWED_VIDEO_TYPES);
}

/**
 * Obtiene la extensión de un archivo
 */
function getFileExtension($file) {
    return pathinfo($file['name'], PATHINFO_EXTENSION);
}

/**
 * Sube un archivo y devuelve la ruta
 */
function uploadFile($file, $destination, $newName = null) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    // Crear directorio si no existe
    if (!file_exists(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }
    
    // Generar nuevo nombre si no se proporciona
    if ($newName === null) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = generateRandomString(20) . '.' . $extension;
        $destination = $destination . '/' . $newName;
    } else {
        $destination = $destination . '/' . $newName;
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $newName;
    }
    
    return false;
}

/**
 * Elimina un archivo
 */
function deleteFile($path) {
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Obtiene la extensión MIME de un archivo
 */
function getMimeType($file) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file);
    finfo_close($finfo);
    return $mimeType;
}

/**
 * Obtiene el nombre del dispositivo móvil
 */
function getMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $mobileKeywords = [
        'Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone',
        'webOS', 'BlackBerry', 'iPod', 'Opera Mini', 'IEMobile'
    ];
    
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return $keyword;
        }
    }
    
    return 'Desktop';
}

/**
 * Verifica si es una petición AJAX
 */
function isAjax() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * Obtiene la dirección IP real del usuario
 */
function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}