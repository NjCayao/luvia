<?php
// services/SmsService.php

class SmsService {
    private $apiKey;
    private $apiSecret;
    private $from;
    
    public function __construct() {
        // Configuración para un proveedor de SMS (ejemplo con Twilio)
        $this->apiKey = 'YOUR_SMS_API_KEY';
        $this->apiSecret = 'YOUR_SMS_API_SECRET';
        $this->from = 'CitasWeb';
    }
    
    /**
     * Envía un mensaje SMS
     */
    public function sendSms($to, $message) {
        // Normalizar número de teléfono (asegurar formato internacional para Perú)
        $to = $this->normalizePhoneNumber($to);
        
        // Para desarrollo, solo simular envío
        if (APP_ENV === 'development') {
            // Guardar en log
            error_log("SMS enviado (simulado) a $to: $message");
            return [
                'success' => true,
                'message' => 'SMS simulado enviado correctamente',
                'code' => $this->extractVerificationCode($message)
            ];
        }
        
        // En producción, aquí iría la integración real con el proveedor de SMS
        // Ejemplo con Twilio:
        
        /*
        $client = new Client($this->apiKey, $this->apiSecret);
        try {
            $result = $client->messages->create(
                $to,
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );
            
            return [
                'success' => true,
                'message' => 'SMS enviado correctamente',
                'id' => $result->sid
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al enviar SMS: ' . $e->getMessage()
            ];
        }
        */
        
        // Para este ejemplo, simular éxito
        return [
            'success' => true,
            'message' => 'SMS enviado correctamente',
            'code' => $this->extractVerificationCode($message)
        ];
    }
    
    /**
     * Envía un código de verificación
     */
    public function sendVerificationCode($to, $code) {
        $message = "Tu código de verificación para CitasWeb es: $code. Válido por 24 horas.";
        return $this->sendSms($to, $message);
    }
    
    /**
     * Normaliza un número de teléfono para formato internacional
     */
    private function normalizePhoneNumber($phone) {
        // Eliminar espacios y caracteres no numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Añadir prefijo +51 de Perú si no está presente
        if (strlen($phone) === 9 && substr($phone, 0, 1) === '9') {
            $phone = '+51' . $phone;
        } else if (strlen($phone) === 11 && substr($phone, 0, 2) === '51') {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Extrae el código de verificación de un mensaje
     */
    private function extractVerificationCode($message) {
        preg_match('/(\d{6})/', $message, $matches);
        return $matches[1] ?? null;
    }
}