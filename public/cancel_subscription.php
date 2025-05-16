<?php

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Subscription.php';

// Verificar si el usuario está logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    if (isAjax()) {
        http_response_code(401);
        echo json_encode(['error' => 'Debes iniciar sesión']);
        exit;
    } else {
        setFlashMessage('warning', 'Debes iniciar sesión para acceder a esta página');
        redirect('/login');
        exit;
    }
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isAjax()) {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        exit;
    } else {
        setFlashMessage('danger', 'Método no permitido');
        redirect('/usuario/suscripciones');
        exit;
    }
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    if (isAjax()) {
        http_response_code(403);
        echo json_encode(['error' => 'Token CSRF inválido']);
        exit;
    } else {
        setFlashMessage('danger', 'Token CSRF inválido');
        redirect('/usuario/suscripciones');
        exit;
    }
}

// Obtener ID de suscripción
$subscriptionId = $_POST['subscription_id'] ?? 0;
$redirectUrl = $_POST['redirect_url'] ?? '/admin/suscripciones';

// Verificar que sea un ID válido
if ($subscriptionId <= 0) {
    if (isAjax()) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de suscripción inválido']);
        exit;
    } else {
        setFlashMessage('danger', 'ID de suscripción inválido');
        redirect($redirectUrl);
        exit;
    }
}

try {
    // Obtener conexión a la base de datos
    $conn = getDbConnection();

    // Verificar que la suscripción exista
    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE id = ?");
    $stmt->execute([$subscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscription) {
        if (isAjax()) {
            http_response_code(404);
            echo json_encode(['error' => 'Suscripción no encontrada']);
            exit;
        } else {
            setFlashMessage('danger', 'Suscripción no encontrada');
            redirect($redirectUrl);
            exit;
        }
    }

    // Verificar que el usuario tenga permisos para cancelar esta suscripción
    if ($subscription['user_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] !== 'admin') {
        if (isAjax()) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para cancelar esta suscripción']);
            exit;
        } else {
            setFlashMessage('danger', 'No tienes permiso para cancelar esta suscripción');
            redirect($redirectUrl);
            exit;
        }
    }

    // Cancelar la renovación automática
    $stmt = $conn->prepare("UPDATE subscriptions SET auto_renew = 0, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$subscriptionId]);

    if ($result) {
        if (isAjax()) {
            echo json_encode([
                'success' => true,
                'message' => 'La renovación automática ha sido cancelada'
            ]);
        } else {
            setFlashMessage('success', 'La renovación automática ha sido cancelada');
            redirect($redirectUrl);
        }
    } else {
        if (isAjax()) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cancelar la renovación']);
        } else {
            setFlashMessage('danger', 'Error al cancelar la renovación');
            redirect($redirectUrl);
        }
    }
} catch (Exception $e) {
    error_log('Error al cancelar suscripción: ' . $e->getMessage());
    
    if (isAjax()) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al cancelar renovación: ' . $e->getMessage()]);
    } else {
        setFlashMessage('danger', 'Error al cancelar renovación: ' . $e->getMessage());
        redirect($redirectUrl);
    }
}

exit;