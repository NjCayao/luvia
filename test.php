<?php
// test-sms.php - Coloca este archivo en la raíz de tu proyecto

// Incluir config y archivo de servicio
require_once __DIR__ . '/config/app.php'; // Para acceder a APP_NAME y constantes
require_once __DIR__ . '/services/SmsService.php';

// Crear instancia del servicio
$smsService = new SmsService();

// Número de prueba (reemplaza con un número real para probar)
$testPhone = '+51999999999'; // Cambia este número por uno real para pruebas

// Generar un código de verificación aleatorio
$verificationCode = rand(100000, 999999);

// Enviar el código
$result = $smsService->sendVerificationCode($testPhone, $verificationCode);

// Mostrar resultado
echo "Resultado del envío:\n";
echo "==================\n";
echo "Éxito: " . ($result['success'] ? 'SÍ' : 'NO') . "\n";
echo "Mensaje: " . $result['message'] . "\n";

if (isset($result['sid'])) {
    echo "SID de Twilio: " . $result['sid'] . "\n";
    echo "¡SMS enviado correctamente a través de Twilio!\n";
} else if (isset($result['code'])) {
    echo "Código enviado (simulado): " . $result['code'] . "\n";
    echo "Teléfono destino: " . $result['to'] . "\n";
    echo "¡SMS simulado correctamente! Esto funciona solo en modo desarrollo.\n";
    echo "Para enviar SMS reales, edita config/twilio.php y establece 'use_real_sms' a true.\n";
}

echo "\n";