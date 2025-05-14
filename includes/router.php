<?php
// includes/router.php

// Incluir todos los controladores
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProfileController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/SubscriptionController.php';

// Cargar rutas definidas
require_once __DIR__ . '/../config/routes.php';

/**
 * Enruta una solicitud HTTP a su controlador correspondiente
 */
function routeRequest($uri) {
    global $routes;
    
    // Método de solicitud actual
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Eliminar slash final si existe
    $uri = rtrim($uri, '/');
    
    // Si la URI está vacía, establecer a '/'
    if (empty($uri)) {
        $uri = '/';
    }
    
    // Variables para almacenar parámetros de ruta
    $params = [];
    $matchedRoute = null;
    
    // Buscar coincidencia entre las rutas definidas
    foreach ($routes as $route => $config) {
        // Convertir patrones de ruta en expresiones regulares
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Verificar el método HTTP si está especificado
            if (isset($config['method']) && $config['method'] !== $requestMethod) {
                continue;
            }
            
            $matchedRoute = $config;
            
            // Extraer parámetros de la URL
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            
            break;
        }
    }
    
    // Si no hay coincidencia, mostrar página 404
    if (!$matchedRoute) {
        header('HTTP/1.0 404 Not Found');
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }
    
    // Verificar requisitos de autenticación
    if (isset($matchedRoute['auth']) && $matchedRoute['auth'] && !isLoggedIn()) {
        setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
        
        // Guardar URL actual para redireccionar después del login
        $_SESSION['redirect_after_login'] = $uri;
        
        redirect('/login');
        exit;
    }
    
    // Verificar requisitos de administrador
    if (isset($matchedRoute['admin']) && $matchedRoute['admin'] && (!isLoggedIn() || !isAdmin())) {
        setFlashMessage('danger', 'Acceso denegado');
        
        if (!isLoggedIn()) {
            redirect('/login');
        } else {
            redirect('/');
        }
        exit;
    }
    
    // Instanciar el controlador y llamar a la acción
    $controllerName = $matchedRoute['controller'];
    $actionName = $matchedRoute['action'];
    
    $controller = new $controllerName();
    $controller->$actionName($params);
}