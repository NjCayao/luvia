<?php
// controllers/PaymentController.php

require_once __DIR__ . '/../services/IzipayService.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../includes/auth.php';

class PaymentController {
    private $izipayService;
    
    public function __construct() {
        $this->izipayService = new IzipayService();
    }
    
    // Mostrar planes disponibles
    public function showPlans() {
        $userType = $_SESSION['user_type'] ?? 'visitor';
        $plans = Plan::getByUserType($userType);
        
        // Renderizar vista
        require_once __DIR__ . '/../views/payment/plans.php';
    }
    
    // Mostrar página de checkout
    public function checkout($params) {
        $planId = $params['planId'] ?? 0;
        
        // Obtener plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            // Plan no válido
            header('Location: /pago/planes?error=plan_no_valido');
            exit;
        }
        
        // Obtener datos del usuario
        $user = User::getById($_SESSION['user_id']);
        
        // Renderizar vista
        require_once __DIR__ . '/../views/payment/checkout.php';
    }
    
    // Procesar pago con tarjeta
    public function processCardPayment() {
        // Verificar si hay un usuario logueado
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }
        
        // Obtener datos
        $planId = $_POST['plan_id'] ?? 0;
        
        // Validar plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            echo json_encode(['error' => 'Plan inválido']);
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($_SESSION['user_id']);
        
        // Generar ID de orden
        $orderId = 'ORD-' . time() . '-' . $user['id'];
        
        try {
            // Crear registro de pago
            $paymentId = Payment::create([
                'user_id' => $user['id'],
                'plan_id' => $planId,
                'amount' => $plan['price'],
                'currency' => 'PEN',
                'payment_method' => 'card',
                'payment_status' => 'pending',
                'order_id' => $orderId
            ]);
            
            // Crear sesión en Izipay
            $session = $this->izipayService->createCardPaymentSession(
                $plan['price'],
                $orderId,
                $user['email'],
                'Plan ' . $plan['name'] . ' - ' . $plan['duration'] . ' días'
            );
            
            // Actualizar pago con ID de sesión
            Payment::update($paymentId, [
                'izipay_session_id' => $session['sessionId']
            ]);
            
            // Devolver respuesta
            echo json_encode([
                'success' => true,
                'redirect_url' => $session['redirectUrl'],
                'session_id' => $session['sessionId']
            ]);
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en pago con tarjeta: ' . $e->getMessage());
            
            // Actualizar pago si existe
            if (isset($paymentId)) {
                Payment::update($paymentId, [
                    'payment_status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
            
            // Devolver error
            echo json_encode([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    
    // Procesar pago con Yape
    public function processYapePayment() {
        // Implementación similar a processCardPayment
        // pero usando createYapePaymentSession
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }
        
        // Obtener datos
        $planId = $_POST['plan_id'] ?? 0;
        
        // Validar plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            echo json_encode(['error' => 'Plan inválido']);
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($_SESSION['user_id']);
        
        // Generar ID de orden
        $orderId = 'ORD-' . time() . '-' . $user['id'];
        
        try {
            // Crear registro de pago
            $paymentId = Payment::create([
                'user_id' => $user['id'],
                'plan_id' => $planId,
                'amount' => $plan['price'],
                'currency' => 'PEN',
                'payment_method' => 'card',
                'payment_status' => 'pending',
                'order_id' => $orderId
            ]);
            
            // Crear sesión en Izipay
            $session = $this->izipayService->createYapePaymentSession(
                $plan['price'],
                $orderId,
                $user['email'],
                'Plan ' . $plan['name'] . ' - ' . $plan['duration'] . ' días'
            );
            
            // Actualizar pago con ID de sesión
            Payment::update($paymentId, [
                'izipay_session_id' => $session['sessionId']
            ]);
            
            // Devolver respuesta
            echo json_encode([
                'success' => true,
                'redirect_url' => $session['redirectUrl'],
                'session_id' => $session['sessionId']
            ]);
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en pago con tarjeta: ' . $e->getMessage());
            
            // Actualizar pago si existe
            if (isset($paymentId)) {
                Payment::update($paymentId, [
                    'payment_status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
            
            // Devolver error
            echo json_encode([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
        exit
    }
    
    // Manejar confirmación de pago
    public function confirmation() {
        $sessionId = $_GET['session_id'] ?? '';
        
        if (empty($sessionId)) {
            header('Location: /pago/error');
            exit;
        }
        
        try {
            // Verificar estado del pago
            $paymentStatus = $this->izipayService->checkPaymentStatus($sessionId);
            
            // Obtener pago
            $payment = Payment::getBySessionId($sessionId);
            
            if (!$payment) {
                throw new Exception('Pago no encontrado');
            }
            
            // Actualizar estado según respuesta
            if ($paymentStatus['status'] === 'COMPLETED') {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $paymentStatus['transactionId'] ?? null
                ]);
                
                // Activar suscripción
                $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);
                
                // Redirigir a éxito
                header('Location: /pago/exito');
                
            } else if ($paymentStatus['status'] === 'FAILED') {
                // Pago fallido
                Payment::update($payment['id'], [
                    'payment_status' => 'failed',
                    'error_message' => $paymentStatus['errorMessage'] ?? 'Pago rechazado'
                ]);
                
                // Redirigir a fallo
                header('Location: /pago/fallido?razon=' . urlencode($paymentStatus['errorMessage'] ?? 'Pago rechazado'));
                
            } else {
                // Otro estado
                Payment::update($payment['id'], [
                    'payment_status' => 'processing'
                ]);
                
                // Redirigir a procesando
                header('Location: /pago/procesando');
            }
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en confirmación de pago: ' . $e->getMessage());
            
            // Redirigir a error
            header('Location: /pago/error?mensaje=' . urlencode('Error al verificar el pago'));
        }
        
        exit;
    }
    
    // Manejar notificaciones IPN
    public function ipnHandler() {
        try {
            // Obtener datos de la petición
            $requestBody = file_get_contents('php://input');
            $headers = getallheaders();
            
            // Procesar notificación
            $notification = $this->izipayService->processIpnNotification($requestBody, $headers);
            
            // Buscar pago
            $payment = Payment::getBySessionId($notification['sessionId']);
            
            if (!$payment) {
                throw new Exception('Pago no encontrado');
            }
            
            // Actualizar estado
            if ($notification['status'] === 'COMPLETED') {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $notification['transactionId'] ?? null
                ]);
                
                // Activar suscripción
                $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);
                
            } else if ($notification['status'] === 'FAILED') {
                // Pago fallido
                Payment::update($payment['id'], [
                    'payment_status' => 'failed',
                    'error_message' => $notification['errorMessage'] ?? 'Pago rechazado'
                ]);
            }
            
            // Responder éxito
            http_response_code(200);
            echo json_encode(['status' => 'success']);
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en notificación IPN: ' . $e->getMessage());
            
            // Responder error
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        
        exit;
    }  
    
    // Mostrar página de éxito
    public function success() {
        require_once __DIR__ . '/../views/payment/success.php';
    }
    
    // Mostrar página de fallo
    public function failed() {
        $reason = $_GET['razon'] ?? 'Error desconocido';
        require_once __DIR__ . '/../views/payment/failed.php';
    }
    
    // Mostrar página de error
    public function error() {
        $message = $_GET['mensaje'] ?? 'Ha ocurrido un error inesperado';
        require_once __DIR__ . '/../views/payment/error.php';
    }

    /**
     * Activa o extiende una suscripción
     */
    private function activateSubscription($userId, $planId, $paymentId) {
        // Obtener datos del plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            throw new Exception('Plan no encontrado');
        }
        
        // Verificar si ya existe una suscripción activa
        $existingSubscription = Subscription::getActiveByUserId($userId);
        
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime('+' . $plan['duration'] . ' days'));
        
        if ($existingSubscription) {
            // Extender suscripción existente
            $currentEndDate = new DateTime($existingSubscription['end_date']);
            $now = new DateTime();
            
            // Si la suscripción ya venció, comenzar desde hoy
            if ($currentEndDate < $now) {
                $newEndDate = $endDate;
            } else {
                // Si está activa, extender desde la fecha de fin actual
                $newEndDate = date('Y-m-d H:i:s', strtotime($existingSubscription['end_date'] . ' +' . $plan['duration'] . ' days'));
            }
            
            Subscription::update($existingSubscription['id'], [
                'plan_id' => $planId,
                'payment_id' => $paymentId,
                'status' => 'active',
                'end_date' => $newEndDate
            ]);
            
        } else {
            // Crear nueva suscripción
            Subscription::create([
                'user_id' => $userId,
                'plan_id' => $planId,
                'payment_id' => $paymentId,
                'status' => 'active',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'auto_renew' => false
            ]);
        }
    }
}