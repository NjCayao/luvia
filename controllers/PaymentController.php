<?php
// controllers/PaymentController.php - ACTUALIZADO PARA API V4.0

require_once __DIR__ . '/../services/IzipayService.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/auth.php';

class PaymentController
{
    private $izipayService;

    public function __construct()
    {
        $this->izipayService = new IzipayService();
    }

    // Mostrar página de checkout
    public function checkout($params)
    {
        $planId = $params['planId'] ?? 0;

        // Obtener plan
        $plan = Plan::getById($planId);

        if (!$plan) {
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

    // Nuevo método: Procesar sesión de pago - REEMPLAZA LOS MÉTODOS ANTERIORES
    public function processPaymentSession()
    {
        header('Content-Type: application/json');

        // Verificar si hay un usuario logueado
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }

        // Leer datos JSON
        $input = json_decode(file_get_contents('php://input'), true);

        // Verificar token CSRF
        if (!isset($input['csrf_token']) || !verifyCsrfToken($input['csrf_token'])) {
            echo json_encode(['error' => 'Token CSRF inválido']);
            exit;
        }

        // Obtener datos
        $planId = $input['plan_id'] ?? 0;

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

            // Crear sesión en Izipay usando la nueva API
            $session = $this->izipayService->createFormToken(
                $plan['price'],
                $orderId,
                $user['email'],
                'Plan ' . $plan['name'] . ' - ' . $plan['duration'] . ' días'
            );

            // Actualizar pago con datos de la sesión
            Payment::update($paymentId, [
                'izipay_session_id' => $session['formToken'] // Usar formToken como identificador
            ]);

            // Devolver respuesta exitosa
            echo json_encode([
                'success' => true,
                'session' => $session
            ]);
        } catch (Exception $e) {
            // Log del error
            error_log('Error en sesión de pago: ' . $e->getMessage());

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

    // Mantener métodos existentes para compatibilidad
    public function processCardPayment()
    {
        $this->processPaymentSession();
    }

    public function processYapePayment()
    {
        $this->processPaymentSession();
    }

    // Manejar confirmación de pago - ACTUALIZADO PARA V4.0
    public function confirmation()
    {
        error_log("=== PAYMENT CONFIRMATION V4.0 ===");
        error_log("GET params: " . print_r($_GET, true));
        error_log("POST params: " . print_r($_POST, true));

        // En V4.0, la confirmación puede venir por diferentes parámetros
        $orderId = $_POST['kr-order-id'] ?? $_GET['vads_order_id'] ?? $_POST['orderId'] ?? '';
        $status = $_POST['kr-answer-type'] ?? $_GET['kr-answer-type'] ?? 'unknown';

        if (empty($orderId)) {
            setFlashMessage('danger', 'Información de pago no válida');
            redirect('/pago/planes');
            exit;
        }

        try {
            // Obtener pago de la base de datos
            $payment = Payment::getByOrderId($orderId);

            if (!$payment) {
                throw new Exception('Pago no encontrado en base de datos');
            }

            error_log("Payment found: " . print_r($payment, true));
            error_log("Status from response: $status");

            // Procesar según el estado
            if ($status === 'payment' || $status === 'success') {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $_POST['kr-trans-uuid'] ?? $_POST['kr-trans-id'] ?? null
                ]);

                // Activar suscripción
                $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);

                // Redirigir a éxito
                setFlashMessage('success', '¡Pago procesado exitosamente!');
                redirect('/pago/exito');
            } else if ($status === 'error' || $status === 'refused' || $status === 'cancel') {
                // Pago fallido
                $errorMessage = $_POST['kr-error-message'] ?? 'Pago rechazado';

                Payment::update($payment['id'], [
                    'payment_status' => 'failed',
                    'error_message' => $errorMessage
                ]);

                // Redirigir a fallo
                redirect('/pago/fallido?razon=' . urlencode($errorMessage));
            } else {
                // Estado desconocido - tratarlo como pendiente
                Payment::update($payment['id'], [
                    'payment_status' => 'processing'
                ]);

                setFlashMessage('info', 'Tu pago está siendo procesado. Te notificaremos cuando esté listo.');
                redirect('/usuario/dashboard');
            }
        } catch (Exception $e) {
            error_log('Error en confirmación de pago V4.0: ' . $e->getMessage());

            setFlashMessage('danger', 'Error al verificar el pago. Si el dinero fue descontado, será reembolsado automáticamente.');
            redirect('/usuario/dashboard');
        }

        exit;
    }

    // Manejar notificaciones IPN - ACTUALIZADO PARA V4.0
    public function ipnHandler()
    {
        error_log("=== IPN Handler V4.0 Called ===");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));

        try {
            // Obtener datos de la petición
            $requestBody = file_get_contents('php://input');
            $headers = getallheaders();

            error_log("IPN Raw Body: " . $requestBody);
            error_log("IPN Headers: " . print_r($headers, true));

            // Procesar notificación usando la nueva implementación
            $notification = $this->izipayService->processIpnNotification($requestBody, $headers);

            error_log("IPN Processed Notification: " . json_encode($notification));

            // Buscar pago por order ID
            $orderId = $notification['orderId'];
            $payment = Payment::getByOrderId($orderId);

            if (!$payment) {
                error_log("IPN: Pago no encontrado - OrderID: $orderId");
                throw new Exception('Pago no encontrado');
            }

            // Actualizar según el estado
            $status = $notification['status'];

            if ($status === 'COMPLETED') {
                // Pago exitoso
                Payment::update($payment['id'], [
                    'payment_status' => 'completed',
                    'transaction_id' => $notification['transactionDetails']['transactionId'] ?? null
                ]);

                // Activar suscripción solo si no está ya activa
                if ($payment['payment_status'] !== 'completed') {
                    $this->activateSubscription($payment['user_id'], $payment['plan_id'], $payment['id']);
                }
            } else if ($status === 'FAILED') {
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
            error_log('Error en notificación IPN V4.0: ' . $e->getMessage());
            error_log('IPN Stack trace: ' . $e->getTraceAsString());

            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        exit;
    }

    // Mostrar página de éxito
    public function success()
    {
        $pageTitle = 'Pago Exitoso';
        $pageHeader = 'Pago Procesado';

        $viewFile = __DIR__ . '/../views/payment/success.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    // Mostrar página de fallo
    public function failed()
    {
        $reason = $_GET['razon'] ?? 'Error desconocido';

        $pageTitle = 'Pago Fallido';
        $pageHeader = 'Error en el Pago';

        $viewFile = __DIR__ . '/../views/payment/failed.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Activa o extiende una suscripción
     */
    private function activateSubscription($userId, $planId, $paymentId)
    {
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

            if ($currentEndDate < $now) {
                $newEndDate = $endDate;
            } else {
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

        error_log("Subscription activated for user $userId with plan $planId");
    }
}
