<?php
// services/IzipayService.php - VERSIÓN CORREGIDA CON DEBUG COMPLETO

require_once __DIR__ . '/../config/izipay.php';

class IzipayService {
    private $config;
    
    public function __construct() {
        $this->config = getIzipayConfig();
    }
    
    /**
     * Crea un FormToken para Izipay V4.0
     */
    public function createFormToken($amount, $orderId, $userEmail, $description = '') {
        error_log("=== IZIPAY SERVICE DEBUG START ===");
        error_log("Amount: $amount PEN");
        error_log("Order ID: $orderId");
        error_log("User Email: $userEmail");
        error_log("Description: $description");
        
        // Validar datos de entrada
        if (empty($amount) || $amount <= 0) {
            throw new Exception('Monto inválido: ' . $amount);
        }
        
        if (empty($orderId)) {
            throw new Exception('Order ID no puede estar vacío');
        }
        
        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido: ' . $userEmail);
        }
        
        // Convertir a céntimos (Izipay requiere en céntimos)
        $amountCents = (int)($amount * 100);
        error_log("Amount in cents: $amountCents");
        
        // URLs de retorno CORRECTAS
        $successUrl = getFullIzipayUrl('/pago/confirmacion');
        $failedUrl = getFullIzipayUrl('/pago/fallido');
        $ipnUrl = getFullIzipayUrl('/api/pago/ipn');
        
        error_log("Success URL: $successUrl");
        error_log("Failed URL: $failedUrl");
        error_log("IPN URL: $ipnUrl");
        
        // Preparar datos según documentación oficial de Izipay V4.0
        $requestData = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ],
            'contrib' => [
                'successUrl' => $successUrl,
                'cancelUrl' => $failedUrl,
                'ipnTargetUrl' => $ipnUrl
            ]
        ];
        
        // Agregar descripción si existe
        if (!empty($description)) {
            $requestData['metadata'] = [
                'description' => $description
            ];
        }
        
        error_log("Request payload: " . json_encode($requestData, JSON_PRETTY_PRINT));
        
        // Hacer petición a la API
        $response = $this->makeApiRequest($requestData);
        
        // Validar respuesta
        if (!$response) {
            throw new Exception('Respuesta vacía del servidor de Izipay');
        }
        
        error_log("Raw API Response: " . json_encode($response, JSON_PRETTY_PRINT));
        
        // Verificar estructura de respuesta
        if (!isset($response['answer'])) {
            throw new Exception('Estructura de respuesta inválida - falta "answer"');
        }
        
        $answer = $response['answer'];
        
        // Verificar errores en la respuesta
        if (isset($answer['errorCode']) && $answer['errorCode'] !== null) {
            $errorMsg = 'Error de Izipay: ' . $answer['errorCode'];
            if (isset($answer['errorMessage'])) {
                $errorMsg .= ' - ' . $answer['errorMessage'];
            }
            if (isset($answer['detailedErrorMessage'])) {
                $errorMsg .= ' (' . $answer['detailedErrorMessage'] . ')';
            }
            throw new Exception($errorMsg);
        }
        
        // Verificar que tenemos el formToken
        if (!isset($answer['formToken']) || empty($answer['formToken'])) {
            throw new Exception('FormToken no recibido. Respuesta: ' . json_encode($answer));
        }
        
        $formToken = $answer['formToken'];
        
        // Construir URL de pago
        $paymentUrl = $this->config['clientEndpoint'] . '/vads-payment/?kr-form-token=' . $formToken;
        
        error_log("FormToken creado exitosamente: " . substr($formToken, 0, 20) . "...");
        error_log("Payment URL: $paymentUrl");
        error_log("=== IZIPAY SERVICE DEBUG END ===");
        
        return [
            'formToken' => $formToken,
            'paymentUrl' => $paymentUrl,
            'publicKey' => $this->config['publicKey'],
            'clientEndpoint' => $this->config['clientEndpoint'],
            'successUrl' => $successUrl,
            'failedUrl' => $failedUrl,
            'orderId' => $orderId
        ];
    }
    
    /**
     * Realiza petición HTTP a la API de Izipay
     */
    private function makeApiRequest($data) {
        $url = $this->config['apiEndpoint'];
        
        // Verificar configuración
        if (empty($this->config['username']) || empty($this->config['password'])) {
            throw new Exception('Credenciales de Izipay no configuradas correctamente');
        }
        
        // Preparar autenticación
        $auth = base64_encode($this->config['username'] . ':' . $this->config['password']);
        
        // Headers CORREGIDOS según documentación
        $headers = [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: ' . (APP_NAME ?? 'Luvia') . '/1.0'
        ];
        
        error_log("=== HTTP REQUEST DEBUG ===");
        error_log("URL: $url");
        error_log("Username: " . $this->config['username']);
        error_log("Password: " . substr($this->config['password'], 0, 10) . "...");
        error_log("Auth header: Basic " . substr($auth, 0, 20) . "...");
        error_log("Headers: " . implode(', ', $headers));
        
        // Configurar cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_VERBOSE => false,
            CURLOPT_USERAGENT => (APP_NAME ?? 'Luvia') . '/1.0',
            CURLOPT_FOLLOWLOCATION => false
        ]);
        
        // Ejecutar petición
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        
        error_log("HTTP Code: $httpCode");
        error_log("Content-Type: $contentType");
        error_log("Response length: " . strlen($response));
        
        curl_close($ch);
        
        // Verificar errores de cURL
        if ($error) {
            error_log("cURL Error: $error");
            throw new Exception("Error de conexión: $error");
        }
        
        // Log de la respuesta (limitada para no saturar logs)
        if (strlen($response) > 1000) {
            error_log("Response (truncated): " . substr($response, 0, 1000) . "...");
        } else {
            error_log("Full Response: $response");
        }
        
        // Verificar código de respuesta HTTP
        if ($httpCode === 406) {
            error_log("ERROR 406: Not Acceptable - Posibles causas:");
            error_log("1. Headers incorrectos");
            error_log("2. Credenciales inválidas");
            error_log("3. Datos del request inválidos");
            error_log("4. URL endpoint incorrecta");
            
            // Analizar respuesta para más detalles
            if (!empty($response)) {
                if (strpos($response, 'cookies') !== false) {
                    throw new Exception('Error 406: Problema con cookies/sesión de Izipay');
                } else if (strpos($response, 'authentication') !== false) {
                    throw new Exception('Error 406: Problema de autenticación con Izipay');
                } else {
                    throw new Exception('Error 406: ' . substr($response, 0, 200));
                }
            } else {
                throw new Exception('Error 406: Not Acceptable - Verificar configuración de Izipay');
            }
        }
        
        if ($httpCode === 401) {
            throw new Exception('Error 401: Credenciales de Izipay inválidas');
        }
        
        if ($httpCode === 403) {
            throw new Exception('Error 403: Acceso denegado por Izipay');
        }
        
        if ($httpCode !== 200) {
            throw new Exception("Error HTTP $httpCode: $response");
        }
        
        // Verificar que la respuesta sea JSON válido
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Respuesta JSON inválida: " . json_last_error_msg() . ". Respuesta: " . substr($response, 0, 200));
        }
        
        error_log("=== HTTP REQUEST SUCCESS ===");
        
        return $decoded;
    }
    
    /**
     * Procesa notificaciones IPN de Izipay
     */
    public function processIpnNotification($requestBody, $headers) {
        error_log("=== IPN NOTIFICATION ===");
        error_log("Request body: $requestBody");
        error_log("Headers: " . print_r($headers, true));
        
        if (empty($requestBody)) {
            throw new Exception('IPN body vacío');
        }
        
        $data = json_decode($requestBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('IPN JSON inválido: ' . json_last_error_msg());
        }
        
        // Verificar firma HMAC si está presente
        $krHash = $headers['kr-hash'] ?? $headers['KR-HASH'] ?? null;
        if ($krHash && !$this->verifyHash($krHash, $requestBody)) {
            throw new Exception('Firma IPN inválida');
        }
        
        $orderDetails = $data['orderDetails'] ?? [];
        $transactionDetails = $data['transactionDetails'] ?? [];
        
        $orderId = $orderDetails['orderId'] ?? null;
        $status = $data['orderStatus'] ?? 'UNKNOWN';
        
        // Mapear estado
        $mappedStatus = match($status) {
            'PAID', 'AUTHORISED' => 'COMPLETED',
            'UNPAID', 'REFUSED', 'CANCELLED' => 'FAILED',
            'WAITING_AUTHORISATION', 'UNDER_VERIFICATION' => 'PROCESSING',
            default => 'FAILED'
        };
        
        error_log("Mapped status: $mappedStatus for order: $orderId");
        error_log("=== END IPN ===");
        
        return [
            'status' => $mappedStatus,
            'orderId' => $orderId,
            'transactionDetails' => $transactionDetails,
            'orderDetails' => $orderDetails,
            'errorMessage' => $data['errorMessage'] ?? null
        ];
    }
    
    /**
     * Verifica firma HMAC
     */
    public function verifyHash($hash, $data) {
        $calculatedHash = hash_hmac('sha256', $data, $this->config['hmacKey']);
        return hash_equals($calculatedHash, $hash);
    }
}