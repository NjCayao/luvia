<?php

// Incluir archivos necesarios
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Cabeceras JSON
header('Content-Type: application/json');

// Obtener ID de provincia
$provinceId = isset($_GET['province_id']) ? (int) $_GET['province_id'] : 0;

if (!$provinceId) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de provincia no válido',
        'districts' => []
    ]);
    exit;
}

try {
    // Obtener conexión a base de datos
    $conn = getDbConnection();
    
    // Consultar distritos
    $stmt = $conn->prepare("SELECT id, name FROM districts WHERE province_id = ? ORDER BY name");
    $stmt->execute([$provinceId]);
    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'districts' => $districts
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'districts' => []
    ]);
}