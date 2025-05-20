<?php
// services/SmsService.php

// Incluir la biblioteca de Twilio
// Si estás usando Composer:
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Si no, incluir manualmente la biblioteca
    require_once __DIR__ . '/../twilio-php-main/src/Twilio/autoload.php'; // Ajusta la ruta según sea necesario
}

use Twilio\Rest\Client;

class SmsService {
    private $twilioSid;
    private $twilioToken;
    private $twilioPhone;
    private $useRealSms;
    
    public function __construct() {
        // Cargar configuración desde archivo
        $config = include(__DIR__ . '/../config/twilio.php');
        
        $this->twilioSid = $config['sid'];
        $this->twilioToken = $config['token'];
        $this->twilioPhone = $config['phone'];
        $this->useRealSms = $config['use_real_sms'];
        
        // Para propósitos de prueba, puedes forzar el uso de SMS real
        // (elimina esta línea en producción)
        // $this->useRealSms = true;
    }
    
    /**
     * Envía un mensaje SMS
     */
    public function sendSms($to, $message) {
        // Normalizar número de teléfono (asegurar formato internacional)
        $to = $this->normalizePhoneNumber($to);
        
        // Para desarrollo, solo simular envío si useRealSms es false
        if (!$this->useRealSms) {
            // Guardar en log para debugging
            error_log("SMS simulado enviado a $to: $message");
            
            return [
                'success' => true,
                'message' => 'SMS simulado enviado correctamente',
                'code' => $this->extractVerificationCode($message),
                'to' => $to
            ];
        }
        
        // En producción o para pruebas, enviar SMS real con Twilio
        try {
            $client = new Client($this->twilioSid, $this->twilioToken);
            
            $result = $client->messages->create(
                $to, // Número de destino
                [
                    'from' => $this->twilioPhone, // Número de Twilio
                    'body' => $message // Mensaje
                ]
            );
            
            // Registrar el envío en logs para tener historial
            error_log("SMS enviado a $to (SID: {$result->sid})");
            
            return [
                'success' => true,
                'message' => 'SMS enviado correctamente',
                'sid' => $result->sid
            ];
        } catch (\Exception $e) {
            // Registrar el error
            error_log("Error al enviar SMS a $to: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al enviar SMS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Envía un código de verificación
     */
    public function sendVerificationCode($to, $code) {
        $appName = defined('APP_NAME') ? APP_NAME : 'Luvia';
        $message = "Tu código de verificación para $appName es: $code. Válido por 24 horas.";
        return $this->sendSms($to, $message);
    }
    
    /**
     * Normaliza un número de teléfono para formato internacional
     */
    private function normalizePhoneNumber($phone) {
        // Limpiar el número de caracteres no numéricos, excepto el +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Si ya empieza con +, asumimos que ya está en formato internacional
        if (substr($phone, 0, 1) === '+') {
            return $phone;
        }
        
        // Si empieza con un número, procesamos según el caso
        if (is_numeric(substr($phone, 0, 1))) {
            // Si empieza con 51 (código de Perú), añadir el signo +
            if (substr($phone, 0, 2) === '51' && strlen($phone) >= 11) {
                return '+' . $phone;
            }
            
            // Si es un número peruano (9 dígitos empezando con 9)
            if (strlen($phone) === 9 && substr($phone, 0, 1) === '9') {
                return '+51' . $phone;
            }
            
            // Para otros números, asumimos que ya tiene el código de país pero sin el +
            return '+' . $phone;
        }
        
        // En caso de formato desconocido, lo devolvemos tal cual está
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