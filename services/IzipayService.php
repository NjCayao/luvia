<?php
// services/IzipayService.php - NUEVA IMPLEMENTACIÓN CON API REST V4.0

require_once __DIR__ . '/../config/izipay.php';

class IzipayService {
    private $config;
    
    public function __construct() {
        $this->config = getIzipayConfig();
    }
    
    /**
     * Crea un FormToken para el pago - NUEVA IMPLEMENTACIÓN REST V4.0
     */
    public function createPaymentSession($amount, $orderId, $userEmail, $description = '') {
        error_log("=== IZIPAY REST API V4.0 SESSION ===");
        error_log("Amount: $amount");
        error_log("Order ID: $orderId");
        error_log("User Email: $userEmail");
        
        // Convertir a céntimos
        $amountCents = (int)($amount * 100);
        
        // Preparar datos para la API REST
        $requestData = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ],
            'metadata' => [
                'description' => $description
            ]
        ];
        
        error_log("Request data: " . json_encode($requestData));
        
        // Hacer petición a la API REST
        $response = $this->makeApiRequest($requestData);
        
        if (!$response || !isset($response['answer']['formToken'])) {
            throw new Exception('Error al crear FormToken: ' . ($response['answer']['errorMessage'] ?? 'Respuesta inválida'));
        }
        
        $formToken = $response['answer']['formToken'];
        
        error_log("FormToken generado: $formToken");
        error_log("=== END IZIPAY SESSION ===");
        
        return [
            'formToken' => $formToken,
            'publicKey' => $this->config['publicKey'],
            'clientEndpoint' => $this->config['clientEndpoint'],
            'orderId' => $orderId
        ];
    }
    
    /**
     * Hace petición a la API REST de Izipay
     */
    private function makeApiRequest($data) {
        $url = $this->config['apiEndpoint'];
        $auth = base64_encode($this->config['username'] . ':' . $this->config['password']);
        
        $headers = [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        error_log("API URL: $url");
        error_log("Auth header: Basic $auth");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        error_log("HTTP Code: $httpCode");
        error_log("Response: $response");
        
        if ($error) {
            throw new Exception("Error CURL: $error");
        }
        
        if ($httpCode !== 200) {
            throw new Exception("Error HTTP $httpCode: $response");
        }
        
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decodificando JSON: " . json_last_error_msg());
        }
        
        return $decoded;
    }
    
    /**
     * Verifica la firma de un hash (para IPN)
     */
    public function verifyHash($hash, $data) {
        $calculatedHash = hash_hmac('sha256', $data, $this->config['hmacKey']);
        return hash_equals($calculatedHash, $hash);
    }
    
    /**
     * Procesa la notificación IPN - ADAPTADO PARA REST V4.0
     */
    public function processIpnNotification($requestBody, $headers) {
        error_log("=== IPN NOTIFICATION V4.0 ===");
        error_log("Request body: $requestBody");
        error_log("Headers: " . print_r($headers, true));
        
        // Verificar que tenemos datos
        if (empty($requestBody)) {
            throw new Exception('IPN body vacío');
        }
        
        // Decodificar datos JSON
        $data = json_decode($requestBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('IPN JSON inválido');
        }
        
        // Verificar firma si está presente
        if (isset($headers['kr-hash'])) {
            if (!$this->verifyHash($headers['kr-hash'], $requestBody)) {
                throw new Exception('Firma IPN inválida');
            }
        }
        
        // Extraer información de la transacción
        $orderDetails = $data['orderDetails'] ?? [];
        $transactionDetails = $data['transactionDetails'] ?? [];
        
        $orderId = $orderDetails['orderId'] ?? null;
        $status = $transactionDetails['cardDetails']['effectiveStrongAuthentication'] ?? 
                  $data['orderStatus'] ?? 'UNKNOWN';
        
        // Mapear estado
        switch ($status) {
            case 'PAID':
            case 'AUTHORISED':
                $mappedStatus = 'COMPLETED';
                break;
            case 'UNPAID':
            case 'REFUSED':
            case 'CANCELLED':
                $mappedStatus = 'FAILED';
                break;
            case 'WAITING_AUTHORISATION':
            case 'UNDER_VERIFICATION':
                $mappedStatus = 'PROCESSING';
                break;
            default:
                $mappedStatus = 'FAILED';
        }
        
        error_log("=== END IPN ===");
        
        return [
            'status' => $mappedStatus,
            'orderId' => $orderId,
            'transactionDetails' => $transactionDetails,
            'orderDetails' => $orderDetails
        ];
    }
    
    /**
     * Métodos de compatibilidad con el código existente
     */
    public function createCardPaymentSession($amount, $orderId, $userEmail, $description = '') {
        return $this->createPaymentSession($amount, $orderId, $userEmail, $description);
    }
    
    public function createYapePaymentSession($amount, $orderId, $userEmail, $description = '') {
        return $this->createPaymentSession($amount, $orderId, $userEmail, $description);
    }
    
    /**
     * Verifica el estado del pago desde los parámetros de retorno
     */
    public function checkPaymentStatus($orderId) {
        // En la nueva implementación, el estado se verifica por IPN
        // o por consulta directa a la API si es necesario
        
        // Por ahora, returnear estado pendiente
        return [
            'status' => 'PENDING',
            'orderId' => $orderId
        ];
    }
}