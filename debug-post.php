<?php
// test-user-model.php
// Herramienta para probar directamente las funciones del modelo User

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivos necesarios
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/models/User.php';

echo "<h1>Prueba de Modelo de Usuario</h1>";

// Formulario para probar getById
echo "<h2>Probar User::getById</h2>";
echo "<form method='GET'>";
echo "ID de usuario: <input type='number' name='id' value='" . (isset($_GET['id']) ? $_GET['id'] : '') . "'>";
echo "<button type='submit' name='action' value='getById'>Probar getById</button>";
echo "</form>";

// Formulario para probar update
echo "<h2>Probar User::update</h2>";
echo "<form method='POST'>";
echo "ID de usuario: <input type='number' name='id' required><br>";
echo "Status: <select name='status'>";
echo "<option value='active'>Activo</option>";
echo "<option value='suspended'>Suspendido</option>";
echo "<option value='pending'>Pendiente</option>";
echo "</select><br>";
echo "Email: <input type='email' name='email'><br>";
echo "Phone: <input type='text' name='phone'><br>";
echo "<input type='hidden' name='csrf_token' value='" . generateCsrfToken() . "'>";
echo "<button type='submit' name='action' value='update'>Probar update</button>";
echo "</form>";

// Procesar la acción getById
if (isset($_GET['action']) && $_GET['action'] === 'getById' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    echo "<h3>Resultados para getById($id):</h3>";
    
    try {
        $user = User::getById($id);
        
        if ($user) {
            echo "<div style='color:green'>Usuario encontrado:</div>";
            echo "<pre>" . print_r($user, true) . "</pre>";
        } else {
            echo "<div style='color:red'>Usuario no encontrado</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
    }
}

// Procesar la acción update
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        echo "<div style='color:red'>Token CSRF inválido</div>";
    } else {
        $id = (int)$_POST['id'];
        
        // Preparar datos a actualizar
        $data = [];
        
        if (isset($_POST['status']) && !empty($_POST['status'])) {
            $data['status'] = $_POST['status'];
        }
        
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $data['email'] = $_POST['email'];
        }
        
        if (isset($_POST['phone']) && !empty($_POST['phone'])) {
            $data['phone'] = $_POST['phone'];
        }
        
        echo "<h3>Resultados para update($id, " . json_encode($data) . "):</h3>";
        
        try {
            $result = User::update($id, $data);
            
            if ($result) {
                echo "<div style='color:green'>Usuario actualizado correctamente</div>";
                
                // Mostrar el usuario actualizado
                $updatedUser = User::getById($id);
                if ($updatedUser) {
                    echo "<pre>" . print_r($updatedUser, true) . "</pre>";
                }
            } else {
                echo "<div style='color:red'>No se pudo actualizar el usuario</div>";
            }
        } catch (Exception $e) {
            echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
        }
    }
}