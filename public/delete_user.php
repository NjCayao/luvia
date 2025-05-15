<?php
// delete_user.php - Coloca este archivo en la carpeta "public"

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Verificar si el usuario está logueado y es admin
session_start();
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    setFlashMessage('danger', 'No autorizado');
    redirect('/admin');
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('danger', 'Método no permitido');
    redirect('/admin/usuarios');
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', 'Token CSRF inválido');
    redirect('/admin/usuarios');
    exit;
}

// Obtener ID de usuario
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

// Validar ID
if ($userId <= 0) {
    setFlashMessage('danger', 'ID de usuario inválido');
    redirect('/admin/usuarios');
    exit;
}

try {
    // Obtener conexión a la base de datos
    $conn = getDbConnection();

    // Verificar que el usuario exista
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        setFlashMessage('danger', 'Usuario no encontrado');
        redirect('/admin/usuarios');
        exit;
    }

    // No permitir eliminar administradores
    if ($user['user_type'] === 'admin') {
        setFlashMessage('danger', 'No se pueden eliminar administradores');
        redirect('/admin/usuarios');
        exit;
    }

    // Marcar usuario como eliminado (soft delete)
    $stmt = $conn->prepare("UPDATE users SET status = 'deleted' WHERE id = ?");
    $result = $stmt->execute([$userId]);

    if ($result) {
        setFlashMessage('success', 'Usuario eliminado correctamente');
    } else {
        setFlashMessage('danger', 'No se pudo eliminar el usuario');
    }
} catch (Exception $e) {
    setFlashMessage('danger', 'Error: ' . $e->getMessage());
}

// Redirigir de vuelta
redirect('/admin/usuarios');