<?php
// controllers/PaymentController.php

require_once __DIR__ . '/../services/IzipayService.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/auth.php';

class PaymentController {
    private $izipayService;
    
    public function __construct() {
        $this->izipayService = new IzipayService();
    }
    
    // Mostrar página de checkout
    public function checkout($params) {
        $planId = $params['planId'] ?? 0;
        
        // Obtener plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            // Plan no válido
            setFlashMessage('danger', 'Plan no válido');
            redirect('/pago/planes');
            exit;
        }
        
        // Obtener datos del usuario
        $user = User::getById($_SESSION['user_id']);
        
        $pageTitle = 'Checkout - ' . $plan['name'];
        $pageHeader = 'Realizar Pago';
        
        // Renderizar vista usando el layout principal
        $viewFile = __DIR__ . '/../views/payment/checkout.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Procesar pago con tarjeta
    public function processCardPayment() {
        header('Content-Type: application/json');
        
        // Verificar si hay un usuario logueado
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['error' => 'Token CSRF inválido']);
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
        
        // Generar ID de orden único
        $orderId = 'LUV-' . time() . '-' . $user['id'] . '-' . rand(1000, 9999);
        
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
            
            // Actualizar pago con ID de sesión (order_id para micuentaweb)
            Payment::update($paymentId, [
                'izipay_session_id' => $orderId
            ]);
            
            // Devolver respuesta
            echo json_encode([
                'success' => true,
                'redirect_url' => $session['redirectUrl'] ?? null,
                'session_id' => $orderId
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
        header('Content-Type: application/json');
        
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['error' => 'Token CSRF inválido']);
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
        
        // Generar ID de orden único
        $orderId = 'LUV-YAPE-' . time() . '-' . $user['id'] . '-' . rand(1000, 9999);
        
        try {
            // Crear registro de pago
            $paymentId = Payment::create([
                'user_id' => $user['id'],
                'plan_id' => $planId,
                'amount' => $plan['price'],
                'currency' => 'PEN',
                'payment_method' => 'yape',
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
            
            // Actualizar pago con ID de sesión (order_id para micuentaweb)  
            Payment::update($paymentId, [
                'izipay_session_id' => $orderId
            ]);
            
            // Devolver respuesta
            echo json_encode([
                'success' => true,
                'redirect_url' => $session['redirectUrl'] ?? null,
                'session_id' => $orderId
            ]);
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en pago con Yape: ' . $e->getMessage());
            
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
    
    // Manejar confirmación de pago - ADAPTADO PARA MICUENTAWEB
    public function confirmation() {
        // Para micuentaweb, los parámetros vienen en la URL de retorno
        $orderId = $_GET['vads_order_id'] ?? $_GET['vads_trans_id'] ?? '';
        
        if (empty($orderId)) {
            setFlashMessage('danger', 'Información de pago no válida');
            redirect('/pago/planes');
            exit;
        }
        
        try {
            // Verificar estado del pago usando los parámetros de retorno
            $paymentStatus = $this->izipayService->checkPaymentStatus($orderId);
            
            // Obtener pago de la base de datos
            $payment = Payment::getByOrderId($orderId);
            
            if (!$payment) {
                throw new Exception('Pago no encontrado en base de datos');
            }
            
            // Log para debugging
            error_log("Payment Status Response: " . json_encode($paymentStatus));
            
            // Procesar según el estado
            $status = $paymentStatus['status'] ?? 'UNKNOWN';
            
            if (in_array($status, ['COMPLETED', 'PAID', 'SUCCESSFUL', 'SUCCESS'])) {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $paymentStatus['transactionId'] ?? null
                ]);
                
                // Activar suscripción
                $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);
                
                // Redirigir a éxito
                setFlashMessage('success', '¡Pago procesado exitosamente!');
                redirect('/pago/exito');
                
            } else if (in_array($status, ['FAILED', 'CANCELLED', 'REJECTED', 'ERROR'])) {
                // Pago fallido
                $errorMessage = $paymentStatus['errorMessage'] ?? 'Pago rechazado';
                
                Payment::update($payment['id'], [
                    'payment_status' => 'failed',
                    'error_message' => $errorMessage
                ]);
                
                // Redirigir a fallo
                redirect('/pago/fallido?razon=' . urlencode($errorMessage));
                
            } else {
                // Estado pendiente o en proceso
                Payment::update($payment['id'], [
                    'payment_status' => 'processing'
                ]);
                
                // Redirigir a página de espera
                setFlashMessage('info', 'Tu pago está siendo procesado. Te notificaremos cuando esté listo.');
                redirect('/usuario/dashboard');
            }
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en confirmación de pago: ' . $e->getMessage());
            
            // Redirigir a error
            setFlashMessage('danger', 'Error al verificar el pago. Si el dinero fue descontado, será reembolsado automáticamente.');
            redirect('/usuario/dashboard');
        }
        
        exit;
    }
    
    // Manejar notificaciones IPN - MEJORADO
    public function ipnHandler() {
        // Log de la petición IPN
        error_log("IPN Handler Called");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));
        
        try {
            // Obtener datos de la petición
            $requestBody = file_get_contents('php://input');
            $headers = getallheaders();
            
            error_log("IPN Raw Body: " . $requestBody);
            error_log("IPN Headers: " . print_r($headers, true));
            
            // Procesar notificación
            $notification = $this->izipayService->processIpnNotification($requestBody, $headers);
            
            error_log("IPN Processed Notification: " . json_encode($notification));
            
            // Buscar pago por session ID o order ID
            $sessionId = $notification['sessionId'] ?? $notification['session_id'] ?? null;
            $orderId = $notification['orderId'] ?? $notification['order_id'] ?? null;
            
            $payment = null;
            
            if ($sessionId) {
                $payment = Payment::getBySessionId($sessionId);
            }
            
            if (!$payment && $orderId) {
                // Buscar por order_id como fallback
                $conn = getDbConnection();
                $stmt = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
                $stmt->execute([$orderId]);
                $payment = $stmt->fetch();
            }
            
            if (!$payment) {
                error_log("IPN: Pago no encontrado - SessionID: $sessionId, OrderID: $orderId");
                throw new Exception('Pago no encontrado');
            }
            
            // Mapear estado
            $status = $notification['status'] ?? $notification['paymentStatus'] ?? 'UNKNOWN';
            
            if (in_array($status, ['COMPLETED', 'PAID', 'SUCCESSFUL', 'SUCCESS'])) {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $notification['transactionId'] ?? $notification['id'] ?? null
                ]);
                
                // Activar suscripción solo si no está ya activa
                if ($payment['payment_status'] !== 'completed') {
                    $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);
                }
                
            } else if (in_array($status, ['FAILED', 'CANCELLED', 'REJECTED', 'ERROR'])) {
                // Pago fallido
                Payment::update($payment['id'], [
                    'payment_status' => 'failed',
                    'error_message' => $notification['errorMessage'] ?? $notification['message'] ?? 'Pago rechazado'
                ]);
            }
            
            // Responder éxito
            http_response_code(200);
            echo json_encode(['status' => 'success']);
            
        } catch (Exception $e) {
            // Log del error
            error_log('Error en notificación IPN: ' . $e->getMessage());
            error_log('IPN Stack trace: ' . $e->getTraceAsString());
            
            // Responder error
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        
        exit;
    }  
    
    // Mostrar página de éxito
    public function success() {
        $pageTitle = 'Pago Exitoso';
        $pageHeader = 'Pago Procesado';
        
        $viewFile = __DIR__ . '/../views/payment/success.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Mostrar página de fallo
    public function failed() {
        $reason = $_GET['razon'] ?? 'Error desconocido';
        
        $pageTitle = 'Pago Fallido';
        $pageHeader = 'Error en el Pago';
        
        $viewFile = __DIR__ . '/../views/payment/failed.php';
        require_once __DIR__ . '/../views/layouts/main.php';
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
        
        // Log para confirmar activación
        error_log("Subscription activated for user $userId with plan $planId");
    }
}