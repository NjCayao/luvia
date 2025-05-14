<?php
// services/IzipayService.php

require_once __DIR__ . '/../config/izipay.php';

class IzipayService {
    private $config;
    
    public function __construct() {
        $this->config = getIzipayConfig();
    }
    
    /**
     * Crea una sesión de pago con tarjeta
     */
    public function createCardPaymentSession($amount, $orderId, $userEmail, $description = '') {
        // Convertir a céntimos
        $amountCents = (int)($amount * 100);
        
        $payload = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ],
            'paymentMethods' => ['CARD'],
            'metadata' => [
                'description' => $description
            ],
            'returnUrl' => getFullIzipayUrl(IZIPAY_RETURN_URL),
            'cancelUrl' => getFullIzipayUrl(IZIPAY_CANCEL_URL),
            'notificationUrl' => getFullIzipayUrl(IZIPAY_NOTIFICATION_URL)
        ];
        
        return $this->sendRequest('checkout/session', $payload);
    }
    
    /**
     * Crea una sesión de pago con Yape
     */
    public function createYapePaymentSession($amount, $orderId, $userEmail, $description = '') {
        $amountCents = (int)($amount * 100);
        
        $payload = [
            'amount' => $amountCents,
            'currency' => 'PEN',
            'orderId' => $orderId,
            'customer' => [
                'email' => $userEmail
            ],
            'paymentMethods' => ['YAPE'],
            'metadata' => [
                'description' => $description
            ],
            'returnUrl' => getFullIzipayUrl(IZIPAY_RETURN_URL),
            'cancelUrl' => getFullIzipayUrl(IZIPAY_CANCEL_URL),
            'notificationUrl' => getFullIzipayUrl(IZIPAY_NOTIFICATION_URL)
        ];
        
        return $this->sendRequest('checkout/session', $payload);
    }
    
    /**
     * Verifica el estado de un pago
     */
    public function checkPaymentStatus($sessionId) {
        return $this->sendRequest('checkout/session/' . $sessionId, [], 'GET');
    }
    
    /**
     * Envía una solicitud a la API de Izipay
     */
    private function sendRequest($endpoint, $payload = [], $method = 'POST') {
        $url = $this->config['endpointUrl'] . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Configurar método HTTP
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } else if ($method === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        
        // Generar firma para seguridad
        $signature = $this->generateSignature($payload);
        
        // Configurar headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Api-Key: ' . $this->config['apiKey'],
            'X-Merchant-Id: ' . $this->config['merchantId'],
            'X-Signature: ' . $signature
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Error en la solicitud cURL: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            // Log error
            error_log('Error en Izipay: ' . $response);
            throw new Exception('Error en la solicitud a Izipay: ' . $response);
        }
    }
    
    /**
     * Genera una firma para la solicitud
     */
    private function generateSignature($payload) {
        $dataToSign = json_encode($payload) . $this->config['secretKey'];
        return hash('sha256', $dataToSign);
    }
    
    /**
     * Verifica la firma de una notificación IPN
     */
    public function verifyIpnSignature($payload, $receivedSignature) {
        $calculatedSignature = $this->generateSignature($payload);
        return hash_equals($calculatedSignature, $receivedSignature);
    }
    
    /**
     * Procesa la notificación IPN
     */
    public function processIpnNotification($requestBody, $headers) {
        $payload = json_decode($requestBody, true);
        $signature = $headers['X-Signature'] ?? '';
        
        if (!$this->verifyIpnSignature($payload, $signature)) {
            throw new Exception('Firma IPN inválida');
        }
        
        return $payload;
    }
}