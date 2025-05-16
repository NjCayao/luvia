<?php

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Profile.php';

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

// Obtener ID del perfil
$profileId = isset($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;

// Validar ID
if ($profileId <= 0) {
    setFlashMessage('danger', 'ID de perfil inválido');
    redirect('/admin/perfiles');
    exit;
}

try {
    // Obtener conexión a la base de datos
    $conn = getDbConnection();

    // Verificar que el perfil exista
    $stmt = $conn->prepare("SELECT * FROM profiles WHERE id = ?");
    $stmt->execute([$profileId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        setFlashMessage('danger', 'Perfil no encontrado');
        redirect('/admin/perfiles');
        exit;
    }

    // Actualizar perfil a verificado
    $stmt = $conn->prepare("UPDATE profiles SET is_verified = 1, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$profileId]);

    if ($result) {
        setFlashMessage('success', 'Perfil verificado correctamente');
    } else {
        setFlashMessage('danger', 'No se pudo verificar el perfil');
    }
} catch (Exception $e) {
    setFlashMessage('danger', 'Error: ' . $e->getMessage());
    error_log('Error al verificar perfil: ' . $e->getMessage());
}

// Redirigir de vuelta
if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
    redirect($_POST['redirect_url']);
} else {
    redirect('/admin/perfil/' . $profileId);
}