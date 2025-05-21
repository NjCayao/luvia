<?php
// ajax_districts.php - Archivo dedicado para cargar distritos

// Incluir archivos necesarios
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Establecer encabezados para JSON
header('Content-Type: application/json');

// Verificar que se proporcione un ID de provincia
if (!isset($_GET['province_id']) || empty($_GET['province_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de provincia no proporcionado'
    ]);
    exit;
}

$provinceId = (int)$_GET['province_id'];

try {
    // Conectar a la base de datos
    $conn = getDbConnection();
    
    // Consultar distritos
    $stmt = $conn->prepare("SELECT id, name FROM districts WHERE province_id = ? ORDER BY name");
    $stmt->execute([$provinceId]);
    $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Devolver resultado
    echo json_encode([
        'success' => true,
        'districts' => $districts
    ]);
} catch (Exception $e) {
    // Registrar error para depuración
    error_log("Error al cargar distritos: " . $e->getMessage());
    
    // Devolver error
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar distritos: ' . $e->getMessage()
    ]);
}