<?php
// public/index.php

// Cargamos configuración y funciones
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Obtener la ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'];

// Extraer la base path de la APP_URL configurada en app.php
$appUrlPath = parse_url(APP_URL, PHP_URL_PATH) ?: '';
$path = '/';

// Eliminar parámetros de consulta y normalizar la ruta
if ($requestUri !== '/') {
    $urlPath = parse_url($requestUri, PHP_URL_PATH);
    
    // Quitar el path base si existe
    if (!empty($appUrlPath) && strpos($urlPath, $appUrlPath) === 0) {
        $path = substr($urlPath, strlen($appUrlPath));
    } else {
        $path = $urlPath;
    }
    
    $path = '/' . trim($path, '/');
}

// Depuración de la ruta final
// echo "<!-- Debug - Final Path: " . htmlspecialchars($path) . " -->\n";

// Cargar rutas
require_once __DIR__ . '/../config/routes.php';

// Para depuración, revisar rutas disponibles
// echo "<!-- Debug - Routes: \n";
// foreach ($routes as $route => $handler) {
//     echo htmlspecialchars($route) . "\n";
// }
// echo "-->\n";

// Buscar la ruta en las definidas
$matchedRoute = false;
$routeParams = [];

foreach ($routes as $route => $handler) {
    // Convertir ruta en expresión regular para manejar parámetros
    $pattern = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route) . '$#';
    
    // Depuración del patrón
    // echo "<!-- Trying to match '{$path}' against pattern '{$pattern}' -->\n";
    
    if (preg_match($pattern, $path, $matches)) {
        $matchedRoute = true;
        
        // Extraer parámetros de la URL
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $routeParams[$key] = $value;
            }
        }
        
        // Verificar el método HTTP si está definido
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($handler['method']) && $handler['method'] !== $method) {
            // Método no permitido
            header('HTTP/1.1 405 Method Not Allowed');
            echo 'Método no permitido';
            exit;
        }
        
        // Verificar middleware de autenticación
        if (isset($handler['auth']) && $handler['auth'] === true) {
            if (!isLoggedIn()) {
                // Guardar URL para redirección después del login
                $_SESSION['redirect_after_login'] = $requestUri;
                
                // Redirigir a login
                header('Location: ' . APP_URL . '/login');
                exit;
            }
        }
        
        // Verificar middleware de administrador
        if (isset($handler['admin']) && $handler['admin'] === true) {
            if (!isAdmin()) {
                // No autorizado
                header('HTTP/1.1 403 Forbidden');
                echo 'Acceso denegado';
                exit;
            }
        }
        
        // Ejecutar el controlador
        $controller = $handler['controller'];
        $action = $handler['action'];
        
        // Verificar si el controlador existe
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
        if (!file_exists($controllerFile)) {
            echo "Controller file not found: {$controllerFile}";
            exit;
        }
        
        require_once $controllerFile;
        
        // Instanciar el controlador
        if (!class_exists($controller)) {
            echo "Controller class not found: {$controller}";
            exit;
        }
        
        $controllerInstance = new $controller();
        
        // Verificar si el método existe
        if (!method_exists($controllerInstance, $action)) {
            echo "Action method not found: {$action} in controller {$controller}";
            exit;
        }
        
        $controllerInstance->$action($routeParams);
        
        break;
    }
}

// Si no se encontró una ruta
if (!$matchedRoute) {
    // Depuración
    // echo "<!-- No route matched for path: " . htmlspecialchars($path) . " -->\n";
    
    header('HTTP/1.1 404 Not Found');
    $errorFile = __DIR__ . '/../views/errors/404.php';
    if (file_exists($errorFile)) {
        require_once $errorFile;
    } else {
        // Fallback error message in case file doesn't exist
        echo '<!DOCTYPE html><html><head><title>Página no encontrada - ' . APP_NAME . '</title></head><body>';
        echo '<h1>Error 404 - Página no encontrada</h1>';
        echo '<p>Lo sentimos, la página que estás buscando no existe.</p>';
        echo '<a href="' . APP_URL . '">Volver al inicio</a>';
        echo '</body></html>';
    }
}