<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Province.php';
require_once __DIR__ . '/../models/District.php';

// Definir los datos de provincias y distritos
$provincesData = [
    'Lima' => [
        'Lima', 'Barranca', 'Cajatambo', 'Canta', 'Cañete', 'Huaral', 
        'Huarochirí', 'Huaura', 'Oyón', 'Yauyos'
    ],
    'Amazonas' => [
        'Chachapoyas', 'Bagua', 'Bongará', 'Condorcanqui', 'Luya', 
        'Rodríguez de Mendoza', 'Utcubamba'
    ],
    // ... añadir todas las provincias y distritos
];

$conn = getDbConnection();
$conn->beginTransaction();

try {
    foreach ($provincesData as $provinceName => $districts) {
        // Verificar si la provincia ya existe
        $province = Province::getByName($provinceName);
        
        if (!$province) {
            // Crear la provincia si no existe
            $provinceId = Province::create($provinceName);
        } else {
            $provinceId = $province['id'];
        }
        
        // Crear los distritos
        foreach ($districts as $districtName) {
            // Verificar si el distrito ya existe
            $district = District::getByNameAndProvince($districtName, $provinceId);
            
            if (!$district) {
                // Crear el distrito si no existe
                District::create($provinceId, $districtName);
            }
        }
    }
    
    $conn->commit();
    echo "Datos de ubicaciones cargados correctamente.\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error al cargar datos: " . $e->getMessage() . "\n";
}