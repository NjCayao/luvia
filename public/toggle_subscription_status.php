<?php
// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Subscription.php';

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
    redirect('/admin/suscripciones');
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('danger', 'Token CSRF inválido');
    redirect('/admin/suscripciones');
    exit;
}

// Obtener datos
$subscriptionId = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validar datos
if ($subscriptionId <= 0) {
    setFlashMessage('danger', 'ID de suscripción inválido');
    redirect('/admin/suscripciones');
    exit;
}

if (!in_array($status, ['active', 'expired', 'cancelled', 'trial'])) {
    setFlashMessage('danger', 'Estado inválido');
    redirect('/admin/suscripciones');
    exit;
}

try {
    // Obtener conexión a la base de datos
    $conn = getDbConnection();

    // Verificar que la suscripción exista
    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE id = ?");
    $stmt->execute([$subscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscription) {
        setFlashMessage('danger', 'Suscripción no encontrada');
        redirect('/admin/suscripciones');
        exit;
    }

    // Actualizar estado directamente
    $stmt = $conn->prepare("UPDATE subscriptions SET status = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$status, $subscriptionId]);

    if ($result) {
        setFlashMessage('success', 'Estado de suscripción actualizado correctamente');
    } else {
        setFlashMessage('danger', 'No se pudo actualizar el estado de la suscripción');
    }

    redirect('/admin/suscripcion/' . $subscriptionId);
} catch (Exception $e) {
    error_log('Error al cambiar estado de suscripción: ' . $e->getMessage());
    setFlashMessage('danger', 'Error al actualizar estado: ' . $e->getMessage());
    redirect('/admin/suscripcion/' . $subscriptionId);
}

exit;