<?php
// includes/auth.php

require_once __DIR__ . '/../models/User.php';

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Verificar si el usuario es administrador
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Iniciar sesión de usuario
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_email'] = $user['email'];
    
    // Actualizar último login
    User::updateLastLogin($user['id']);
    
    return true;
}

// Cerrar sesión
function logoutUser() {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Si se desea destruir la sesión completamente, borrar también la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Finalmente, destruir la sesión
    session_destroy();
    
    return true;
}

// Obtener usuario actual
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return User::getById($_SESSION['user_id']);
}

// Verificar contraseña
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Generar token único
function generateToken() {
    return bin2hex(random_bytes(32));
}