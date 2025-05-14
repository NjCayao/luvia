<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Media.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

class AdminController {
    /**
     * Constructor - Verifica que sea un administrador
     */
    public function __construct() {
        // Verificar que el usuario esté logueado y sea administrador
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debes iniciar sesión para acceder al panel de administración');
            redirect('/login?redirect=' . urlencode('/admin'));
            exit;
        }
        
        if (!isAdmin()) {
            setFlashMessage('danger', 'No tienes permisos para acceder al panel de administración');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Dashboard principal
     */
    public function dashboard() {
        // Estadísticas básicas
        $totalUsers = User::count();
        $totalAdvertisers = User::count(['user_type' => 'advertiser']);
        $totalVisitors = User::count(['user_type' => 'visitor']);
        $totalProfiles = Profile::count();
        
        // Usuarios recientes
        $recentUsers = User::getAll(5, 0);
        
        // Pagos recientes
        $recentPayments = Payment::getAll(5, 0);
        
        // Suscripciones por vencer en los próximos días
        $expiringSubscriptions = Subscription::getAboutToExpire();
        
        // Perfiles más vistos
        $topProfiles = Profile::getMostViewed(5);
        
        $pageTitle = 'Panel de Administración';
        $pageHeader = 'Dashboard';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    /**
     * Gestión de usuarios
     */
    public function users() {
        // Filtros
        $userType = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Paginación
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Preparar filtros
        $filters = [];
        if (!empty($userType)) {
            $filters['user_type'] = $userType;
        }
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        
        // Obtener usuarios
        $users = User::getAll($limit, $offset, $filters);
        $totalUsers = User::count($filters);
        
        // Calcular páginas
        $totalPages = ceil($totalUsers / $limit);
        
        $pageTitle = 'Gestión de Usuarios';
        $pageHeader = 'Usuarios';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/users.php';
    }
    
    /**
     * Ver detalle de usuario
     */
    public function viewUser($params) {
        $userId = $params['id'] ?? 0;
        
        // Obtener usuario
        $user = User::getById($userId);
        
        if (!$user) {
            setFlashMessage('danger', 'Usuario no encontrado');
            redirect('/admin/usuarios');
            exit;
        }
        
        // Obtener perfil si es anunciante
        $profile = null;
        if ($user['user_type'] === 'advertiser') {
            $profile = Profile::getByUserId($userId);
        }
        
        // Obtener suscripciones
        $subscriptions = Subscription::getByUserId($userId);
        
        // Obtener pagos
        $payments = Payment::getByUserId($userId);
        
        $pageTitle = 'Detalles de Usuario';
        $pageHeader = 'Usuario: ' . ($user['email'] ?? $user['phone']);
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/view_user.php';
    }
    
    /**
     * Editar usuario
     */
    public function editUser($params) {
        $userId = $params['id'] ?? 0;
        
        // Obtener usuario
        $user = User::getById($userId);
        
        if (!$user) {
            setFlashMessage('danger', 'Usuario no encontrado');
            redirect('/admin/usuarios');
            exit;
        }
        
        $pageTitle = 'Editar Usuario';
        $pageHeader = 'Editar Usuario';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/edit_user.php';
    }
    
    /**
     * Actualizar usuario
     */
    public function updateUser() {
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
        
        // Obtener datos
        $userId = $_POST['user_id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Validar datos
        $errors = [];
        
        if (empty($userId)) {
            $errors['user_id'] = 'ID de usuario inválido';
        }
        
        if (empty($status)) {
            $errors['status'] = 'El estado es obligatorio';
        } else if (!in_array($status, ['pending', 'active', 'suspended', 'deleted'])) {
            $errors['status'] = 'Estado inválido';
        }
        
        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es obligatorio';
        } else {
            $emailError = validateEmail($email);
            if ($emailError) {
                $errors['email'] = $emailError;
            }
        }
        
        if (empty($phone)) {
            $errors['phone'] = 'El teléfono es obligatorio';
        } else {
            $phoneError = validatePhone($phone);
            if ($phoneError) {
                $errors['phone'] = $phoneError;
            }
        }
        
        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Limpiar teléfono
        $phone = preg_replace('/\D/', '', $phone);
        
        // Verificar que el usuario exista
        $user = User::getById($userId);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
        
        // Verificar si el email ya existe y no es del mismo usuario
        if ($email !== $user['email']) {
            $existingUser = User::getByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                http_response_code(422);
                echo json_encode(['errors' => ['email' => 'Este correo electrónico ya está registrado']]);
                exit;
            }
        }
        
        // Verificar si el teléfono ya existe y no es del mismo usuario
        if ($phone !== $user['phone']) {
            $existingUser = User::getByPhone($phone);
            if ($existingUser && $existingUser['id'] != $userId) {
                http_response_code(422);
                echo json_encode(['errors' => ['phone' => 'Este teléfono ya está registrado']]);
                exit;
            }
        }
        
        // Actualizar usuario
        $userData = [
            'status' => $status,
            'email' => $email,
            'phone' => $phone
        ];
        
        // Si se proporcionó una nueva contraseña, actualizarla
        if (!empty($_POST['password'])) {
            $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        try {
            User::update($userId, $userData);
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'redirect' => url('/admin/usuario/' . $userId)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Gestión de perfiles
     */
    public function profiles() {
        // Filtros
        $gender = $_GET['gender'] ?? '';
        $city = $_GET['city'] ?? '';
        $verified = isset($_GET['verified']) ? (bool)$_GET['verified'] : null;
        $search = $_GET['search'] ?? '';
        
        // Paginación
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Preparar filtros
        $filters = [];
        if (!empty($gender)) {
            $filters['gender'] = $gender;
        }
        if (!empty($city)) {
            $filters['city'] = $city;
        }
        if ($verified !== null) {
            $filters['is_verified'] = $verified;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        
        // Obtener perfiles
        $profiles = Profile::getAll($limit, $offset, $filters);
        $totalProfiles = Profile::count($filters);
        
        // Calcular páginas
        $totalPages = ceil($totalProfiles / $limit);
        
        // Obtener ciudades para el filtro
        $cities = Profile::getAvailableCities();
        
        $pageTitle = 'Gestión de Perfiles';
        $pageHeader = 'Perfiles';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/profiles.php';
    }
    
    /**
     * Ver detalle de perfil
     */
    public function viewProfile($params) {
        $profileId = $params['id'] ?? 0;
        
        // Obtener perfil completo
        $profile = Profile::getProfileWithDetails($profileId);
        
        if (!$profile) {
            setFlashMessage('danger', 'Perfil no encontrado');
            redirect('/admin/perfiles');
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($profile['user_id']);
        
        $pageTitle = 'Detalles de Perfil';
        $pageHeader = 'Perfil: ' . $profile['name'];
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/view_profile.php';
    }
    
    /**
     * Editar perfil
     */
    public function editProfile($params) {
        $profileId = $params['id'] ?? 0;
        
        // Obtener perfil
        $profile = Profile::getById($profileId);
        
        if (!$profile) {
            setFlashMessage('danger', 'Perfil no encontrado');
            redirect('/admin/perfiles');
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($profile['user_id']);
        
        // Obtener ciudades disponibles
        $cities = Profile::getAvailableCities();
        
        $pageTitle = 'Editar Perfil';
        $pageHeader = 'Editar Perfil';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/edit_profile.php';
    }
    
    /**
     * Actualizar perfil
     */
    public function updateProfile() {
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
        
        // Obtener datos
        $profileId = $_POST['profile_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $description = $_POST['description'] ?? '';
        $whatsapp = $_POST['whatsapp'] ?? '';
        $city = $_POST['city'] ?? '';
        $location = $_POST['location'] ?? '';
        $schedule = $_POST['schedule'] ?? '';
        $isVerified = isset($_POST['is_verified']) ? (bool)$_POST['is_verified'] : false;
        
        // Validar datos
        $errors = [];
        
        if (empty($profileId)) {
            $errors['profile_id'] = 'ID de perfil inválido';
        }
        
        if (empty($name)) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (empty($gender)) {
            $errors['gender'] = 'El género es obligatorio';
        } else if (!in_array($gender, ['female', 'male', 'trans'])) {
            $errors['gender'] = 'Género inválido';
        }
        
        if (empty($description)) {
            $errors['description'] = 'La descripción es obligatoria';
        }
        
        if (empty($whatsapp)) {
            $errors['whatsapp'] = 'El número de WhatsApp es obligatorio';
        } else {
            $phoneError = validatePhone($whatsapp);
            if ($phoneError) {
                $errors['whatsapp'] = $phoneError;
            }
        }
        
        if (empty($city)) {
            $errors['city'] = 'La ciudad es obligatoria';
        }
        
        if (empty($location)) {
            $errors['location'] = 'La ubicación es obligatoria';
        }
        
        if (empty($schedule)) {
            $errors['schedule'] = 'El horario es obligatorio';
        }
        
        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Limpiar número de WhatsApp
        $whatsapp = preg_replace('/\D/', '', $whatsapp);
        
        // Verificar que el perfil exista
        $profile = Profile::getById($profileId);
        
        if (!$profile) {
            http_response_code(404);
            echo json_encode(['error' => 'Perfil no encontrado']);
            exit;
        }
        
        // Actualizar perfil
        $profileData = [
            'name' => $name,
            'gender' => $gender,
            'description' => $description,
            'whatsapp' => $whatsapp,
            'city' => $city,
            'location' => $location,
            'schedule' => $schedule,
            'is_verified' => $isVerified
        ];
        
        try {
            Profile::update($profileId, $profileData);
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'redirect' => url('/admin/perfil/' . $profileId)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar perfil: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Verificar perfil
     */
    public function verifyProfile() {
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
        
        // Obtener ID del perfil
        $profileId = $_POST['profile_id'] ?? 0;
        
        // Verificar que el perfil exista
        $profile = Profile::getById($profileId);
        
        if (!$profile) {
            http_response_code(404);
            echo json_encode(['error' => 'Perfil no encontrado']);
            exit;
        }
        
        // Verificar perfil
        try {
            Profile::update($profileId, ['is_verified' => true]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil verificado correctamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al verificar perfil: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Gestión de pagos
     */
    public function payments() {
        // Filtros
        $status = $_GET['status'] ?? '';
        $method = $_GET['method'] ?? '';
        $search = $_GET['search'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        // Paginación
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Preparar filtros
        $filters = [];
        if (!empty($status)) {
            $filters['payment_status'] = $status;
        }
        if (!empty($method)) {
            $filters['payment_method'] = $method;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($startDate)) {
            $filters['start_date'] = $startDate;
        }
        if (!empty($endDate)) {
            $filters['end_date'] = $endDate;
        }
        
        // Obtener pagos
        $payments = Payment::getAll($limit, $offset, $filters);
        $totalPayments = Payment::count($filters);
        
        // Calcular páginas
        $totalPages = ceil($totalPayments / $limit);
        
        // Estadísticas básicas
        $stats = [
            'total_amount' => Payment::getTotalAmount($filters),
            'completed_count' => Payment::count(array_merge($filters, ['payment_status' => 'completed'])),
            'pending_count' => Payment::count(array_merge($filters, ['payment_status' => 'pending'])),
            'failed_count' => Payment::count(array_merge($filters, ['payment_status' => 'failed']))
        ];
        
        $pageTitle = 'Gestión de Pagos';
        $pageHeader = 'Pagos';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/payments.php';
    }
    
    /**
     * Ver detalle de pago
     */
    public function viewPayment($params) {
        $paymentId = $params['id'] ?? 0;
        
        // Obtener pago
        $payment = Payment::getById($paymentId);
        
        if (!$payment) {
            setFlashMessage('danger', 'Pago no encontrado');
            redirect('/admin/pagos');
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($payment['user_id']);
        
        // Obtener plan
        $plan = Plan::getById($payment['plan_id']);
        
        $pageTitle = 'Detalles de Pago';
        $pageHeader = 'Pago #' . $payment['id'];
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/view_payment.php';
    }
    
    /**
     * Gestión de suscripciones
     */
    public function subscriptions() {
        // Filtros
        $status = $_GET['status'] ?? '';
        $userType = $_GET['user_type'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Paginación
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Preparar filtros
        $filters = [];
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        if (!empty($userType)) {
            $filters['user_type'] = $userType;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        
        // Obtener suscripciones
        $subscriptions = Subscription::getAll($limit, $offset, $filters);
        $totalSubscriptions = Subscription::count($filters);
        
        // Calcular páginas
        $totalPages = ceil($totalSubscriptions / $limit);
        
        // Estadísticas básicas
        $stats = [
            'active_count' => Subscription::count(array_merge($filters, ['status' => 'active'])),
            'trial_count' => Subscription::count(array_merge($filters, ['status' => 'trial'])),
            'expired_count' => Subscription::count(array_merge($filters, ['status' => 'expired'])),
            'auto_renew_count' => Subscription::countAutoRenew($filters)
        ];
        
        $pageTitle = 'Gestión de Suscripciones';
        $pageHeader = 'Suscripciones';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/subscriptions.php';
    }
    
    /**
     * Ver detalle de suscripción
     */
    public function viewSubscription($params) {
        $subscriptionId = $params['id'] ?? 0;
        
        // Obtener suscripción
        $subscription = Subscription::getById($subscriptionId);
        
        if (!$subscription) {
            setFlashMessage('danger', 'Suscripción no encontrada');
            redirect('/admin/suscripciones');
            exit;
        }
        
        // Obtener usuario
        $user = User::getById($subscription['user_id']);
        
        // Obtener plan
        $plan = Plan::getById($subscription['plan_id']);
        
        // Obtener pago
        $payment = null;
        if ($subscription['payment_id']) {
            $payment = Payment::getById($subscription['payment_id']);
        }
        
        $pageTitle = 'Detalles de Suscripción';
        $pageHeader = 'Suscripción #' . $subscription['id'];
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/view_subscription.php';
    }
    
    /**
     * Gestión de planes
     */
    public function plans() {
        // Filtros
        $userType = $_GET['user_type'] ?? '';
        
        // Preparar filtros
        $filters = [];
        if (!empty($userType)) {
            $filters['user_type'] = $userType;
        }
        
        // Obtener planes
        $plans = Plan::getAll($filters);
        
        $pageTitle = 'Gestión de Planes';
        $pageHeader = 'Planes';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/plans.php';
    }
    
    /**
     * Editar plan
     */
    public function editPlan($params) {
        $planId = $params['id'] ?? 0;
        
        // Obtener plan
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            setFlashMessage('danger', 'Plan no encontrado');
            redirect('/admin/planes');
            exit;
        }
        
        $pageTitle = 'Editar Plan';
        $pageHeader = 'Editar Plan';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/edit_plan.php';
    }
    
    /**
     * Crear nuevo plan
     */
    public function createPlan() {
        $pageTitle = 'Crear Plan';
        $pageHeader = 'Crear Plan';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/create_plan.php';
    }
    
    /**
     * Guarda un plan (nuevo o existente)
     */
    public function savePlan() {
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
        
        // Obtener datos
        $planId = $_POST['plan_id'] ?? 0; // 0 para nuevo plan
        $name = $_POST['name'] ?? '';
        $userType = $_POST['user_type'] ?? '';
        $duration = $_POST['duration'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $maxPhotos = $_POST['max_photos'] ?? null;
        $maxVideos = $_POST['max_videos'] ?? null;
        $featured = isset($_POST['featured']) ? (bool)$_POST['featured'] : false;
        $description = $_POST['description'] ?? '';
        
        // Validar datos
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (empty($userType)) {
            $errors['user_type'] = 'El tipo de usuario es obligatorio';
        } else if (!in_array($userType, ['advertiser', 'visitor'])) {
            $errors['user_type'] = 'Tipo de usuario inválido';
        }
        
        if (empty($duration) || !is_numeric($duration) || $duration <= 0) {
            $errors['duration'] = 'La duración debe ser un número positivo';
        }
        
        if (empty($price) || !is_numeric($price) || $price < 0) {
            $errors['price'] = 'El precio debe ser un número no negativo';
        }
        
        if ($userType === 'advertiser') {
            if (empty($maxPhotos) || !is_numeric($maxPhotos) || $maxPhotos <= 0) {
                $errors['max_photos'] = 'El máximo de fotos debe ser un número positivo';
            }
            
            if (empty($maxVideos) || !is_numeric($maxVideos) || $maxVideos <= 0) {
                $errors['max_videos'] = 'El máximo de videos debe ser un número positivo';
            }
        }
        
        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Preparar datos del plan
        $planData = [
            'name' => $name,
            'user_type' => $userType,
            'duration' => (int)$duration,
            'price' => (float)$price,
            'max_photos' => $userType === 'advertiser' ? (int)$maxPhotos : null,
            'max_videos' => $userType === 'advertiser' ? (int)$maxVideos : null,
            'featured' => $featured,
            'description' => $description
        ];
        
        try {
            if ($planId > 0) {
                // Verificar que el plan exista
                $plan = Plan::getById($planId);
                
                if (!$plan) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Plan no encontrado']);
                    exit;
                }
                
                // Actualizar plan existente
                Plan::update($planId, $planData);
                $message = 'Plan actualizado correctamente';
            } else {
                // Crear nuevo plan
                $planId = Plan::create($planData);
                $message = 'Plan creado correctamente';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'redirect' => url('/admin/planes')
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar plan: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Eliminar plan
     */
    public function deletePlan() {
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
        
        // Obtener ID del plan
        $planId = $_POST['plan_id'] ?? 0;
        
        // Verificar que el plan exista
        $plan = Plan::getById($planId);
        
        if (!$plan) {
            http_response_code(404);
            echo json_encode(['error' => 'Plan no encontrado']);
            exit;
        }
        
        // Verificar si hay suscripciones activas con este plan
        if (Plan::hasUsers($planId)) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar el plan porque hay usuarios que lo están utilizando']);
            exit;
        }
        
        // Eliminar plan
        try {
            Plan::delete($planId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Plan eliminado correctamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar plan: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Estadísticas generales
     */
    public function stats() {
        // Obtener período de tiempo
        $period = $_GET['period'] ?? 'month'; // day, week, month, year
        $startDate = '';
        $endDate = date('Y-m-d');
        
        switch ($period) {
            case 'day':
                $startDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'week':
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'month':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'year':
                $startDate = date('Y-m-d', strtotime('-365 days'));
                break;
            default:
                $startDate = date('Y-m-d', strtotime('-30 days'));
                $period = 'month';
        }
        
        // Estadísticas de usuarios
        $userStats = [
            'total' => User::count(),
            'advertisers' => User::count(['user_type' => 'advertiser']),
            'visitors' => User::count(['user_type' => 'visitor']),
            'new_users' => User::countNewUsers($startDate, $endDate),
            'user_growth' => User::getUserGrowth($period)
        ];
        
        // Estadísticas de perfiles
        $profileStats = [
            'total' => Profile::count(),
            'verified' => Profile::count(['is_verified' => true]),
            'most_viewed' => Profile::getMostViewed(5),
            'most_contacted' => Profile::getMostContacted(5),
            'by_gender' => [
                'female' => Profile::count(['gender' => 'female']),
                'male' => Profile::count(['gender' => 'male']),
                'trans' => Profile::count(['gender' => 'trans'])
            ]
        ];
        
        // Estadísticas de pagos
        $paymentStats = [
            'total_amount' => Payment::getTotalAmount(['start_date' => $startDate, 'end_date' => $endDate]),
            'completed_count' => Payment::count(['payment_status' => 'completed', 'start_date' => $startDate, 'end_date' => $endDate]),
            'revenue_by_day' => Payment::getRevenueByDay($startDate, $endDate),
            'by_method' => [
                'card' => Payment::getTotalAmount(['payment_method' => 'card', 'start_date' => $startDate, 'end_date' => $endDate]),
                'yape' => Payment::getTotalAmount(['payment_method' => 'yape', 'start_date' => $startDate, 'end_date' => $endDate])
            ]
        ];
        
        // Estadísticas de suscripciones
        $subscriptionStats = [
            'active' => Subscription::count(['status' => 'active']),
            'trial' => Subscription::count(['status' => 'trial']),
            'expired' => Subscription::count(['status' => 'expired', 'start_date' => $startDate, 'end_date' => $endDate]),
            'auto_renew' => Subscription::countAutoRenew(),
            'by_plan' => Subscription::countByPlan()
        ];
        
        $pageTitle = 'Estadísticas';
        $pageHeader = 'Estadísticas Generales';
        
        require_once __DIR__ . '/../views/layouts/admin.php';
        require_once __DIR__ . '/../views/admin/stats.php';
    }
}