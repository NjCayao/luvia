<?php
// services/IzipayService.php

require_once __DIR__ . '/../config/izipay.php';

class IzipayService {
    private $config;
    
    public function __construct() {
        $this->config = getIzipayConfig();
    }
    
    /**
     * Crea una sesión de pago con tarjeta - ADAPTADO PARA MICUENTAWEB
     */
    public function createCardPaymentSession($amount, $orderId, $userEmail, $description = '') {
        // Convertir a céntimos
        $amountCents = (int)($amount * 100);
        
        // Preparar datos para el formulario de micuentaweb
        $formData = [
            'vads_site_id' => $this->config['merchantId'],
            'vads_amount' => $amountCents,
            'vads_currency' => '604', // PEN currency code
            'vads_trans_date' => gmdate('YmdHis'),
            'vads_trans_id' => substr(str_pad($orderId, 6, '0', STR_PAD_LEFT), -6),
            'vads_order_id' => $orderId,
            'vads_payment_config' => 'SINGLE',
            'vads_page_action' => 'PAYMENT',
            'vads_action_mode' => 'INTERACTIVE',
            'vads_version' => 'V2',
            'vads_cust_email' => $userEmail,
            'vads_order_info' => $description,
            'vads_url_return' => $this->getCallbackUrl(IZIPAY_RETURN_URL),
            'vads_url_cancel' => $this->getCallbackUrl(IZIPAY_CANCEL_URL),
            'vads_url_check' => $this->getCallbackUrl(IZIPAY_NOTIFICATION_URL),
            'vads_capture_delay' => '0',
            'vads_validation_mode' => '0'
        ];
        
        // Generar la firma
        $signature = $this->generateSignature($formData);
        $formData['signature'] = $signature;
        
        // Crear URL de redirección
        $redirectUrl = $this->config['endpointUrl'] . '?' . http_build_query($formData);
        
        return [
            'sessionId' => $orderId,
            'redirectUrl' => $redirectUrl,
            'formData' => $formData
        ];
    }
    
    /**
     * Crea una sesión de pago con Yape - MISMO FLUJO
     */
    public function createYapePaymentSession($amount, $orderId, $userEmail, $description = '') {
        // Para Yape, usamos el mismo flujo que tarjeta en micuentaweb
        // La diferencia se maneja en el lado del usuario final
        return $this->createCardPaymentSession($amount, $orderId, $userEmail, $description);
    }
    
    /**
     * Verifica el estado de un pago - ADAPTADO
     */
    public function checkPaymentStatus($orderId) {
        // Para micuentaweb, verificamos a través de los parámetros de retorno
        // Esta función se llama cuando el usuario regresa del pago
        
        if (isset($_GET['vads_trans_status'])) {
            $status = $_GET['vads_trans_status'];
            $transactionId = $_GET['vads_trans_uuid'] ?? $_GET['vads_trans_id'] ?? null;
            
            // Verificar la firma de la respuesta
            if (!$this->verifyReturnSignature($_GET)) {
                throw new Exception('Firma de retorno inválida');
            }
            
            // Mapear estados de micuentaweb a nuestros estados
            switch ($status) {
                case 'AUTHORISED':
                case 'CAPTURED':
                    return [
                        'status' => 'COMPLETED',
                        'transactionId' => $transactionId,
                        'orderId' => $orderId
                    ];
                case 'REFUSED':
                case 'CANCELLED':
                    return [
                        'status' => 'FAILED',
                        'transactionId' => $transactionId,
                        'orderId' => $orderId,
                        'errorMessage' => 'Pago rechazado o cancelado'
                    ];
                case 'WAITING_AUTHORISATION':
                case 'UNDER_VERIFICATION':
                    return [
                        'status' => 'PROCESSING',
                        'transactionId' => $transactionId,
                        'orderId' => $orderId
                    ];
                default:
                    return [
                        'status' => 'UNKNOWN',
                        'transactionId' => $transactionId,
                        'orderId' => $orderId
                    ];
            }
        }
        
        return [
            'status' => 'PENDING',
            'orderId' => $orderId
        ];
    }
    
    /**
     * Obtiene la URL completa para callbacks
     */
    private function getCallbackUrl($path) {
        $isProduction = isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'erophia.com';
        
        if ($isProduction) {
            return 'https://erophia.com/public' . $path;
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $basePath = dirname($_SERVER['SCRIPT_NAME']);
            return $protocol . $host . $basePath . $path;
        }
    }
    
    /**
     * Genera la firma para micuentaweb
     */
    private function generateSignature($formData) {
        // Ordenar los campos alfabéticamente
        ksort($formData);
        
        // Construir la cadena de firma
        $signatureString = '';
        foreach ($formData as $key => $value) {
            if (strpos($key, 'vads_') === 0) {
                $signatureString .= $value . '+';
            }
        }
        
        // Añadir la clave secreta
        $signatureString .= $this->config['secretKey'];
        
        // Generar SHA-1
        return sha1($signatureString);
    }
    
    /**
     * Verifica la firma de retorno
     */
    private function verifyReturnSignature($data) {
        if (!isset($data['signature'])) {
            return false;
        }
        
        $receivedSignature = $data['signature'];
        unset($data['signature']);
        
        $calculatedSignature = $this->generateSignature($data);
        
        return hash_equals($calculatedSignature, $receivedSignature);
    }
    
    /**
     * Procesa la notificación IPN - ADAPTADO PARA MICUENTAWEB
     */
    public function processIpnNotification($requestBody, $headers) {
        // Para micuentaweb, los datos vienen en POST o GET
        $data = $_POST ?: $_GET;
        
        error_log("IPN Data: " . print_r($data, true));
        
        if (empty($data) || !isset($data['vads_trans_status'])) {
            throw new Exception('Datos IPN inválidos');
        }
        
        // Verificar la firma
        if (!$this->verifyReturnSignature($data)) {
            throw new Exception('Firma IPN inválida');
        }
        
        // Extraer información relevante
        $status = $data['vads_trans_status'];
        $orderId = $data['vads_order_id'] ?? $data['vads_trans_id'];
        $transactionId = $data['vads_trans_uuid'] ?? $data['vads_trans_id'];
        
        // Mapear estado
        switch ($status) {
            case 'AUTHORISED':
            case 'CAPTURED':
                $mappedStatus = 'COMPLETED';
                break;
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
        
        return [
            'status' => $mappedStatus,
            'orderId' => $orderId,
            'sessionId' => $orderId,
            'transactionId' => $transactionId,
            'errorMessage' => $mappedStatus === 'FAILED' ? 'Pago rechazado' : null
        ];
    }
}