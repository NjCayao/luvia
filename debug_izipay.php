<?php
// debug_izipay.php - Crear este archivo temporal para probar credenciales

require_once 'config/izipay.php';

// Obtener configuración
$config = getIzipayConfig();

echo "<h2>Debug Izipay Credenciales</h2>";
echo "<h3>Configuración actual:</h3>";
echo "<ul>";
echo "<li><strong>Environment:</strong> " . IZIPAY_ENVIRONMENT . "</li>";
echo "<li><strong>Username:</strong> " . $config['username'] . "</li>";
echo "<li><strong>Password:</strong> " . substr($config['password'], 0, 10) . "...</li>";
echo "<li><strong>Public Key:</strong> " . substr($config['publicKey'], 0, 20) . "...</li>";
echo "<li><strong>API Endpoint:</strong> " . $config['apiEndpoint'] . "</li>";
echo "</ul>";

// Probar autenticación básica
$auth = base64_encode($config['username'] . ':' . $config['password']);
echo "<h3>Auth Header:</h3>";
echo "<code>Authorization: Basic " . $auth . "</code>";

// Hacer una petición de prueba
echo "<h3>Prueba de API:</h3>";

$testData = [
    'amount' => 500, // 5 soles en céntimos
    'currency' => 'PEN',
    'orderId' => 'TEST-' . time(),
    'customer' => [
        'email' => 'test@example.com'
    ]
];

$headers = [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json',
    'Accept: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['apiEndpoint']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";
echo "<p><strong>CURL Error:</strong> " . ($error ?: 'None') . "</p>";
echo "<h4>Response:</h4>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Analizar respuesta
if ($httpCode === 200) {
    $decoded = json_decode($response, true);
    if ($decoded && isset($decoded['answer']['formToken'])) {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ ÉXITO:</strong> FormToken generado correctamente!<br>";
        echo "<strong>FormToken:</strong> " . substr($decoded['answer']['formToken'], 0, 50) . "...";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ ERROR:</strong> Respuesta sin FormToken";
        echo "</div>";
    }
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ ERROR HTTP:</strong> " . $httpCode;
    echo "</div>";
}

// Verificar si las credenciales parecen válidas
if (strlen($config['username']) < 5) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>⚠️ ADVERTENCIA:</strong> El username parece muy corto";
    echo "</div>";
}

if (strlen($config['password']) < 20) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>⚠️ ADVERTENCIA:</strong> El password parece muy corto para API V4.0";
    echo "</div>";
}
?>