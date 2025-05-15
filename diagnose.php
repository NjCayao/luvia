<?php
// diagnose-views.php
// Este archivo comprueba específicamente los archivos de vistas problemáticos

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar las constantes y funciones necesarias
require_once 'config/config.php'; // Ajusta la ruta según tu estructura
require_once 'includes/functions.php';

echo "<h1>Diagnóstico de Vistas del Admin</h1>";

// Lista de vistas a verificar
$views = [
    'user_edit.php' => 'views/admin/user_edit.php',
    'edit_profile.php' => 'views/admin/edit_profile.php',
    'plan_edit.php' => 'views/admin/plan_edit.php',
    'plan_create.php' => 'views/admin/plan_create.php',
    'admin.php' => 'views/layouts/admin.php'
];

echo "<h2>Archivos de vistas:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Vista</th><th>Ruta</th><th>Existe</th><th>Tamaño</th><th>Permisos</th><th>Contenido</th></tr>";

foreach ($views as $name => $path) {
    $exists = file_exists($path);
    $size = $exists ? filesize($path) : 'N/A';
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>$path</td>";
    echo "<td>" . ($exists ? '✅ Sí' : '❌ No') . "</td>";
    echo "<td>$size bytes</td>";
    echo "<td>$perms</td>";
    
    // Mostrar las primeras líneas del contenido si existe
    if ($exists) {
        $content = file_get_contents($path);
        $preview = substr(htmlspecialchars($content), 0, 150) . (strlen($content) > 150 ? '...' : '');
        echo "<td><pre style='margin: 0;'>$preview</pre></td>";
    } else {
        echo "<td>N/A</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Probar la inclusión manual de cada vista
echo "<h2>Prueba de inclusión:</h2>";

foreach ($views as $name => $path) {
    if (file_exists($path)) {
        echo "<h3>$name:</h3>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;'>";
        
        // Variables que podría necesitar la vista
        $pageTitle = "Prueba de $name";
        $pageHeader = "Prueba de inclusión de $name";
        $user = ['id' => 1, 'email' => 'test@example.com', 'user_type' => 'admin', 'status' => 'active'];
        $profile = ['id' => 1, 'name' => 'Perfil de prueba', 'user_id' => 1];
        $plan = ['id' => 1, 'name' => 'Plan de prueba', 'price' => 99.99];
        
        try {
            // Capturar la salida para evitar que rompa la página de diagnóstico
            ob_start();
            include $path;
            $output = ob_get_clean();
            
            if (!empty($output)) {
                echo $output;
            } else {
                echo "<p>La vista se incluyó correctamente pero no generó salida.</p>";
            }
        } catch (Throwable $e) {
            echo "<div style='color: red;'>";
            echo "<p><strong>Error al incluir la vista:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>En archivo:</strong> " . $e->getFile() . " (línea " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
        
        echo "</div>";
    }
}