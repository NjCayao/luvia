<?php
// update_user.php - Coloca este archivo en la carpeta "public"

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Establecer encabezado para JSON
header('Content-Type: application/json');

// Verificar si el usuario está logueado y es admin
session_start();
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit;
}

// Obtener datos
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$status = $_POST['status'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// Validar datos
$errors = [];

if (empty($userId)) {
    $errors['user_id'] = 'ID de usuario inválido';
}

if (empty($status)) {
    $errors['status'] = 'El estado es obligatorio';
} else if (!in_array($status, ['pending', 'active', 'suspended', 'deleted'])) {
    $errors['status'] = 'Estado inválido';
}

if (empty($email)) {
    $errors['email'] = 'El correo electrónico es obligatorio';
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Formato de correo electrónico inválido';
}

if (empty($phone)) {
    $errors['phone'] = 'El teléfono es obligatorio';
}

// Si hay errores, devolver respuesta JSON
if (!empty($errors)) {
    echo json_encode(['errors' => $errors]);
    exit;
}

// Limpiar teléfono
$phone = preg_replace('/\D/', '', $phone);

try {
    // Obtener conexión a la base de datos
    $conn = getDbConnection();

    // Verificar que el usuario exista
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    // Verificar si el email ya existe y no es del mismo usuario
    if ($email !== $user['email']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            echo json_encode(['errors' => ['email' => 'Este correo electrónico ya está registrado']]);
            exit;
        }
    }

    // Verificar si el teléfono ya existe y no es del mismo usuario
    if ($phone !== $user['phone']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
        $stmt->execute([$phone, $userId]);
        if ($stmt->fetch()) {
            echo json_encode(['errors' => ['phone' => 'Este teléfono ya está registrado']]);
            exit;
        }
    }

    // Construir consulta SQL
    $sql = "UPDATE users SET status = ?, email = ?, phone = ?";
    $params = [$status, $email, $phone];

    // Si se proporcionó una nueva contraseña, actualizarla
    if (!empty($_POST['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Finalizar consulta
    $sql .= " WHERE id = ?";
    $params[] = $userId;

    // Ejecutar consulta
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'redirect' => url('/admin/usuario/' . $userId)
        ]);
    } else {
        echo json_encode(['error' => 'No se pudo actualizar el usuario']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}