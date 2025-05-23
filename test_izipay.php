<?php
// test_izipay.php - SCRIPT DE DIAGNÓSTICO PARA IZIPAY
// Colocar en la raíz del proyecto y acceder desde el navegador

// Solo ejecutar en modo desarrollo
if (!isset($_GET['debug']) || $_GET['debug'] !== 'izipay') {
    die('Acceso denegado');
}

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/izipay.php';
require_once __DIR__ . '/services/IzipayService.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico Izipay</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .section { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Diagnóstico de Configuración Izipay</h1>
    
    <div class="section">
        <h2>1. Verificación de Configuración</h2>
        <?php
        $config = getIzipayConfig();
        
        echo "<p><strong>Entorno:</strong> " . IZIPAY_ENVIRONMENT . "</p>";
        echo "<p><strong>Username:</strong> " . $config['username'] . "</p>";
        echo "<p><strong>Password:</strong> " . substr($config['password'], 0, 20) . "... (length: " . strlen($config['password']) . ")</p>";
        echo "<p><strong>Public Key:</strong> " . substr($config['publicKey'], 0, 30) . "... (length: " . strlen($config['publicKey']) . ")</p>";
        echo "<p><strong>HMAC Key:</strong> " . substr($config['hmacKey'], 0, 20) . "... (length: " . strlen($config['hmacKey']) . ")</p>";
        echo "<p><strong>API Endpoint:</strong> " . $config['apiEndpoint'] . "</p>";
        echo "<p><strong>Client Endpoint:</strong> " . $config['clientEndpoint'] . "</p>";
        
        // Verificar si las credenciales parecen válidas
        $issues = [];
        if (empty($config['username']) || $config['username'] === '13448745') {
            $issues[] = "Username parece ser el valor de ejemplo";
        }
        if (empty($config['password']) || strpos($config['password'], 'testpassword_') === 0) {
            $issues[] = "Password parece ser el valor de ejemplo";
        }
        if (strpos($config['publicKey'], 'testpublickey_') !== false) {
            $issues[] = "Public Key parece ser el valor de ejemplo";
        }
        
        if (empty($issues)) {
            echo '<p class="success status">✓ Configuración parece correcta</p>';
        } else {
            echo '<p class="warning status">⚠ Posibles problemas:</p>';
            foreach ($issues as $issue) {
                echo "<p class=\"warning\">• $issue</p>";
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. URLs de Retorno</h2>
        <?php
        $successUrl = getFullIzipayUrl('/pago/confirmacion');
        $failedUrl = getFullIzipayUrl('/pago/fallido');
        $ipnUrl = getFullIzipayUrl('/api/pago/ipn');
        
        echo "<p><strong>Success URL:</strong> $successUrl</p>";
        echo "<p><strong>Failed URL:</strong> $failedUrl</p>";
        echo "<p><strong>IPN URL:</strong> $ipnUrl</p>";
        
        // Verificar que las URLs sean HTTPS en producción
        if (IZIPAY_ENVIRONMENT === 'PRODUCTION') {
            if (strpos($successUrl, 'https://') !== 0) {
                echo '<p class="error">⚠ URLs deben ser HTTPS en producción</p>';
            } else {
                echo '<p class="success">✓ URLs usan HTTPS</p>';
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Test de Conectividad</h2>
        <?php
        echo "<p>Probando conexión a: " . $config['apiEndpoint'] . "</p>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $config['apiEndpoint'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<p class=\"error\">✗ Error de conexión: $error</p>";
        } else {
            echo "<p class=\"success\">✓ Conexión exitosa (HTTP $httpCode)</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Test de Autenticación</h2>
        <?php
        echo "<p>Probando autenticación con datos mínimos...</p>";
        
        try {
            $izipayService = new IzipayService();
            
            // Datos de prueba
            $testData = [
                'amount' => 500, // 5.00 PEN en céntimos
                'currency' => 'PEN',
                'orderId' => 'TEST-' . time(),
                'customer' => [
                    'email' => 'test@example.com'
                ]
            ];
            
            $url = $config['apiEndpoint'];
            $auth = base64_encode($config['username'] . ':' . $config['password']);
            
            $headers = [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
                'Accept: application/json'
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($testData),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
            
            if ($error) {
                echo "<p class=\"error\">✗ Error cURL: $error</p>";
            } else {
                if ($httpCode === 200) {
                    echo '<p class="success">✓ Autenticación exitosa</p>';
                    
                    $decoded = json_decode($response, true);
                    if (isset($decoded['answer']['formToken'])) {
                        echo '<p class="success">✓ FormToken recibido correctamente</p>';
                    } else {
                        echo '<p class="warning">⚠ Respuesta sin FormToken</p>';
                    }
                } elseif ($httpCode === 401) {
                    echo '<p class="error">✗ Error 401: Credenciales incorrectas</p>';
                } elseif ($httpCode === 403) {
                    echo '<p class="error">✗ Error 403: Acceso denegado</p>';
                } elseif ($httpCode === 406) {
                    echo '<p class="error">✗ Error 406: Not Acceptable - Verificar headers y datos</p>';
                } else {
                    echo "<p class=\"error\">✗ Error HTTP $httpCode</p>";
                }
                
                echo "<pre>Response: " . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
            }
            
        } catch (Exception $e) {
            echo "<p class=\"error\">✗ Excepción: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Recomendaciones</h2>
        <ul>
            <li><strong>Verificar en el Panel de Izipay:</strong> 
                <a href="https://secure.micuentaweb.pe/vads-merchant/" target="_blank">https://secure.micuentaweb.pe/vads-merchant/</a>
            </li>
            <li><strong>Configurar URLs de retorno</strong> en el panel de Izipay</li>
            <li><strong>Verificar que las credenciales</strong> sean las correctas del panel</li>
            <li><strong>Probar primero en entorno TEST</strong> antes de ir a producción</li>
        </ul>
        
        <?php if ($httpCode === 406): ?>
        <div style="background: #ffeeee; padding: 15px; border-left: 4px solid #ff0000;">
            <h3>Error 406 - Posibles Soluciones:</h3>
            <ol>
                <li><strong>Verificar credenciales:</strong> Usuario y contraseña deben ser exactos</li>
                <li><strong>Verificar headers:</strong> Content-Type debe ser application/json</li>
                <li><strong>Verificar datos:</strong> Estructura del JSON debe coincidir con la documentación</li>
                <li><strong>Contactar soporte de Izipay</strong> si el problema persiste</li>
            </ol>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>6. Información del Servidor</h2>
        <p><strong>PHP Version:</strong> <?= PHP_VERSION ?></p>
        <p><strong>cURL Version:</strong> <?= curl_version()['version'] ?? 'No disponible' ?></p>
        <p><strong>OpenSSL:</strong> <?= curl_version()['ssl_version'] ?? 'No disponible' ?></p>
        <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?></p>
        <p><strong>Host:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Desconocido' ?></p>
    </div>
</body>
</html>