<?php
// services/IzipayService.php - IMPLEMENTACIÓN CON REDIRECCIÓN EXTERNA

require_once __DIR__ . '/../config/izipay.php';

class IzipayService {
    private $config;
    
    public function __construct() {
        $this->config = getIzipayConfig();
    }
    
    /**
     * Crea una sesión de pago y devuelve la URL de redirección
     */
    public function createPaymentSession($amount, $orderId, $userEmail, $description = '') {
        error_log("=== IZIPAY REDIRECT SESSION ===");
        error_log("Amount: $amount");
        error_log("Order ID: $orderId");
        error_log("User Email: $userEmail");
        
        // Convertir a céntimos
        $amountCents = (int)($amount * 100);
        
        // URLs de retorno completas
        $successUrl = getFullIzipayUrl('/pago/confirmacion');
        $failedUrl = getFullIzipayUrl('/pago/fallido');
        $ipnUrl = getFullIzipayUrl('/api/pago/ipn');
        
        // Preparar datos para la API REST V4.0
        $requestData = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ],
            'metadata' => [
                'description' => $description
            ],
            // URLs de retorno
            'contrib' => [
                'successUrl' => $successUrl,
                'errorUrl' => $failedUrl,
                'ipnTargetUrl' => $ipnUrl
            ]
        ];
        
        error_log("Request data: " . json_encode($requestData));
        error_log("Success URL: " . $successUrl);
        error_log("Failed URL: " . $failedUrl);
        error_log("IPN URL: " . $ipnUrl);
        
        // Hacer petición a la API REST
        $response = $this->makeApiRequest($requestData);
        
        if (!$response || !isset($response['answer']['webRedirectRequest'])) {
            throw new Exception('Error al crear sesión de pago: ' . ($response['answer']['errorMessage'] ?? 'Respuesta inválida'));
        }
        
        $redirectData = $response['answer']['webRedirectRequest'];
        $paymentUrl = $redirectData['redirectURL'];
        
        error_log("Payment URL generada: $paymentUrl");
        error_log("=== END IZIPAY SESSION ===");
        
        return [
            'paymentUrl' => $paymentUrl,
            'redirectData' => $redirectData,
            'orderId' => $orderId
        ];
    }
    
    /**
     * Alternativa: Crear FormToken para redirección manual
     */
    public function createFormToken($amount, $orderId, $userEmail, $description = '') {
        error_log("=== IZIPAY FORM TOKEN ===");
        
        // Convertir a céntimos
        $amountCents = (int)($amount * 100);
        
        // URLs de retorno completas
        $successUrl = getFullIzipayUrl('/pago/confirmacion');
        $failedUrl = getFullIzipayUrl('/pago/fallido');
        
        // Preparar datos básicos
        $requestData = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ]
        ];
        
        $response = $this->makeApiRequest($requestData);
        
        if (!$response || !isset($response['answer']['formToken'])) {
            throw new Exception('Error al crear FormToken: ' . ($response['answer']['errorMessage'] ?? 'Respuesta inválida'));
        }
        
        $formToken = $response['answer']['formToken'];
        
        // Construir URL de redirección manual
        $paymentUrl = $this->config['clientEndpoint'] . '/vads-payment/?kr-form-token=' . $formToken;
        
        error_log("FormToken: $formToken");
        error_log("Payment URL: $paymentUrl");
        error_log("=== END FORM TOKEN ===");
        
        return [
            'formToken' => $formToken,
            'paymentUrl' => $paymentUrl,
            'successUrl' => $successUrl,
            'failedUrl' => $failedUrl,
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
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // No seguir redirecciones automáticamente
        
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
     * Procesa la notificación IPN
     */
    public function processIpnNotification($requestBody, $headers) {
        error_log("=== IPN NOTIFICATION ===");
        error_log("Request body: $requestBody");
        
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
        $status = $data['orderStatus'] ?? 'UNKNOWN';
        
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
     * Verifica la firma de un hash (para IPN)
     */
    public function verifyHash($hash, $data) {
        $calculatedHash = hash_hmac('sha256', $data, $this->config['hmacKey']);
        return hash_equals($calculatedHash, $hash);
    }
    
    // Métodos de compatibilidad
    public function createCardPaymentSession($amount, $orderId, $userEmail, $description = '') {
        return $this->createFormToken($amount, $orderId, $userEmail, $description);
    }
    
    public function createYapePaymentSession($amount, $orderId, $userEmail, $description = '') {
        return $this->createFormToken($amount, $orderId, $userEmail, $description);
    }
}