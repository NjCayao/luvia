<?php
// Archivo de depuración temporal

// Incluir configuraciones y funciones necesarias
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Establecer cabeceras para JSON
header('Content-Type: application/json');

try {
    // Simular datos POST
    $userId = 5;
    $email = 'cliente@example.com';
    $phone = '959876543';
    $status = 'active';
    
    // Conexión a la base de datos
    $conn = getDbConnection();
    
    // Verificar que existe el usuario
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $checkStmt->execute([$userId]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Actualizar usuario
    $updateStmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, status = ? WHERE id = ?");
    $result = $updateStmt->execute([$email, $phone, $status, $userId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ]);
    } else {
        echo json_encode(['error' => 'Error al actualizar usuario']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}