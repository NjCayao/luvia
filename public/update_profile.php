<?php

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Profile.php';

// Establecer encabezado para JSON
header('Content-Type: application/json');

// Verificar si el usuario está logueado y es admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit;
}

// Obtener datos
$profileId = isset($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;
$name = $_POST['name'] ?? '';
$gender = $_POST['gender'] ?? '';
$description = $_POST['description'] ?? '';
$whatsapp = $_POST['whatsapp'] ?? '';
$city = $_POST['city'] ?? '';
$location = $_POST['location'] ?? '';
$schedule = $_POST['schedule'] ?? '';
$isVerified = isset($_POST['is_verified']) ? (bool)$_POST['is_verified'] : false;

// Validar datos
$errors = [];

if (empty($profileId)) {
    $errors['profile_id'] = 'ID de perfil inválido';
}

if (empty($name)) {
    $errors['name'] = 'El nombre es obligatorio';
}

if (empty($gender)) {
    $errors['gender'] = 'El género es obligatorio';
} else if (!in_array($gender, ['female', 'male', 'trans'])) {
    $errors['gender'] = 'Género inválido';
}

if (empty($description)) {
    $errors['description'] = 'La descripción es obligatoria';
}

if (empty($whatsapp)) {
    $errors['whatsapp'] = 'El número de WhatsApp es obligatorio';
}

if (empty($city)) {
    $errors['city'] = 'La ciudad es obligatoria';
}

if (empty($location)) {
    $errors['location'] = 'La ubicación es obligatoria';
}

if (empty($schedule)) {
    $errors['schedule'] = 'El horario es obligatorio';
}

// Si hay errores, devolver respuesta JSON
if (!empty($errors)) {
    echo json_encode(['errors' => $errors]);
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
        echo json_encode(['error' => 'Perfil no encontrado']);
        exit;
    }

    // Actualizar perfil
    $stmt = $conn->prepare("UPDATE profiles SET 
        name = ?, 
        gender = ?, 
        description = ?, 
        whatsapp = ?, 
        city = ?, 
        location = ?, 
        schedule = ?, 
        is_verified = ?, 
        updated_at = NOW() 
        WHERE id = ?");
    
    $result = $stmt->execute([
        $name,
        $gender,
        $description,
        $whatsapp,
        $city,
        $location,
        $schedule,
        $isVerified ? 1 : 0,
        $profileId
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'redirect' => url('/admin/perfil/' . $profileId)
        ]);
    } else {
        echo json_encode(['error' => 'No se pudo actualizar el perfil']);
    }
} catch (Exception $e) {
    error_log('Error al actualizar perfil: ' . $e->getMessage());
    echo json_encode(['error' => 'Error al actualizar perfil: ' . $e->getMessage()]);
}