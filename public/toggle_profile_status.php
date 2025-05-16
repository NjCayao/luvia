<?php
// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/User.php';

// Verificar si el usuario está logueado y es admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    setFlashMessage('danger', 'No autorizado');
    redirect('/admin');
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('danger', 'Método no permitido');
    redirect('/admin/perfiles');
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', 'Token CSRF inválido');
    redirect('/admin/perfiles');
    exit;
}

// Obtener datos
$profileId = isset($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validar datos
if ($profileId <= 0) {
    setFlashMessage('danger', 'ID de perfil inválido');
    redirect('/admin/perfiles');
    exit;
}

if ($userId <= 0) {
    setFlashMessage('danger', 'ID de usuario inválido');
    redirect('/admin/perfiles');
    exit;
}

if (!in_array($status, ['active', 'suspended'])) {
    setFlashMessage('danger', 'Estado inválido');
    redirect('/admin/perfiles');
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
        redirect('/admin/perfiles');
        exit;
    }

    // No permitir cambiar el estado de un administrador
    if ($user['user_type'] === 'admin') {
        setFlashMessage('danger', 'No se puede cambiar el estado de un administrador');
        redirect('/admin/perfiles');
        exit;
    }

    // Actualizar estado
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $userId]);

    if ($result) {
        $message = $status === 'active' ? 'Usuario activado correctamente' : 'Usuario suspendido correctamente';
        setFlashMessage('success', $message);
    } else {
        setFlashMessage('danger', 'No se pudo actualizar el estado del usuario');
    }
} catch (Exception $e) {
    setFlashMessage('danger', 'Error: ' . $e->getMessage());
}

// Redirigir de vuelta
if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
    redirect($_POST['redirect_url']);
} else {
    redirect('/admin/perfil/' . $profileId);
}