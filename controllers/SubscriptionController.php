<?php
// controllers/SubscriptionController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

class SubscriptionController {
    /**
     * Muestra los planes disponibles
     */
    public function showPlans() {
        // Verificar inicio de sesión
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debes iniciar sesión para ver los planes');
            redirect('/login?redirect=' . urlencode('/pago/planes'));
            exit;
        }
        
        // Obtener tipo de usuario y planes correspondientes
        $userType = $_SESSION['user_type'];
        $plans = Plan::getByUserType($userType);
        
        // Obtener información del usuario
        $user = User::getById($_SESSION['user_id']);
        
        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($_SESSION['user_id']);
        
        // Verificar estado del período de prueba (para anunciantes)
        $trialStatus = null;
        if ($userType === 'advertiser') {
            $trialStatus = Subscription::checkTrialStatus($_SESSION['user_id']);
        }
        
        $pageTitle = 'Planes y Suscripciones';
        $pageHeader = 'Selecciona tu Plan';
        
        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/subscription/plans.php';
    }
    
    /**
     * Muestra el historial de suscripciones
     */
    public function history() {
        // Verificar inicio de sesión
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debes iniciar sesión para ver tu historial');
            redirect('/login');
            exit;
        }
        
        // Obtener suscripciones del usuario
        $subscriptions = Subscription::getByUserId($_SESSION['user_id']);
        
        // Obtener información del usuario
        $user = User::getById($_SESSION['user_id']);
        
        // Verificar si tiene suscripción activa
        $activeSubscription = Subscription::getActiveByUserId($_SESSION['user_id']);
        
        $pageTitle = 'Historial de Suscripciones';
        $pageHeader = 'Tu Historial de Suscripciones';
        
        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/subscription/history.php';
    }
    
    /**
     * Cancela una suscripción
     */
    public function cancel() {
        // Verificar inicio de sesión
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            exit;
        }
        
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF inválido']);
            exit;
        }
        
        // Obtener ID de suscripción
        $subscriptionId = $_POST['subscription_id'] ?? 0;
        
        // Verificar que la suscripción exista y pertenezca al usuario
        $subscription = Subscription::getById($subscriptionId);
        
        if (!$subscription || $subscription['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para cancelar esta suscripción']);
            exit;
        }
        
        // Cancelar renovación automática
        if (Subscription::update($subscriptionId, ['auto_renew' => false])) {
            echo json_encode([
                'success' => true,
                'message' => 'La renovación automática ha sido cancelada'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cancelar la renovación']);
        }
        
        exit;
    }
    
    /**
     * Activa la renovación automática
     */
    public function enableAutoRenew() {
        // Verificar inicio de sesión
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            exit;
        }
        
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF inválido']);
            exit;
        }
        
        // Obtener ID de suscripción
        $subscriptionId = $_POST['subscription_id'] ?? 0;
        
        // Verificar que la suscripción exista y pertenezca al usuario
        $subscription = Subscription::getById($subscriptionId);
        
        if (!$subscription || $subscription['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para modificar esta suscripción']);
            exit;
        }
        
        // Activar renovación automática
        if (Subscription::update($subscriptionId, ['auto_renew' => true])) {
            echo json_encode([
                'success' => true,
                'message' => 'La renovación automática ha sido activada'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al activar la renovación']);
        }
        
        exit;
    }
    
    /**
     * Redirecciona al checkout para renovar una suscripción
     */
    public function renew() {
        // Verificar inicio de sesión
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debes iniciar sesión para renovar tu suscripción');
            redirect('/login');
            exit;
        }
        
        // Obtener ID de suscripción
        $subscriptionId = $_GET['id'] ?? 0;
        
        // Verificar que la suscripción exista y pertenezca al usuario
        $subscription = Subscription::getById($subscriptionId);
        
        if (!$subscription || $subscription['user_id'] != $_SESSION['user_id']) {
            setFlashMessage('danger', 'No tienes permiso para renovar esta suscripción');
            redirect('/usuario/suscripciones');
            exit;
        }
        
        // Redireccionar al checkout con el mismo plan
        redirect('/pago/checkout/' . $subscription['plan_id']);
        exit;
    }
}