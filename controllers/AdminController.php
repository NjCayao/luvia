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

class AdminController
{
    /**
     * Constructor - Verifica que sea un administrador
     */
    public function __construct()
    {
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
    public function dashboard()
    {
        // Establecer valores predeterminados para las estadísticas
        $totalUsers = 0;
        $totalAdvertisers = 0;
        $totalVisitors = 0;
        $totalProfiles = 0;

        // Obtener estadísticas
        try {
            // Contar usuarios totales
            $totalUsers = User::count();

            // Contar anunciantes - asegurarse de que el filtro funcione
            try {
                $totalAdvertisers = User::count(['user_type' => 'advertiser']);
            } catch (Exception $e) {
                error_log("Error al contar anunciantes: " . $e->getMessage());
                // Alternativa si el filtro falla
                $conn = getDbConnection();
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'advertiser'");
                $stmt->execute();
                $result = $stmt->fetch();
                $totalAdvertisers = (int)($result['total'] ?? 0);
            }

            // Contar visitantes - asegurarse de que el filtro funcione
            try {
                $totalVisitors = User::count(['user_type' => 'visitor']);
            } catch (Exception $e) {
                error_log("Error al contar visitantes: " . $e->getMessage());
                // Alternativa si el filtro falla
                $conn = getDbConnection();
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'visitor'");
                $stmt->execute();
                $result = $stmt->fetch();
                $totalVisitors = (int)($result['total'] ?? 0);
            }

            // Contar perfiles
            try {
                $totalProfiles = Profile::count();
            } catch (Exception $e) {
                error_log("Error al contar perfiles: " . $e->getMessage());
                // Alternativa si el método falla
                $conn = getDbConnection();
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM profiles");
                $stmt->execute();
                $result = $stmt->fetch();
                $totalProfiles = (int)($result['total'] ?? 0);
            }
        } catch (Exception $e) {
            error_log("Error general al obtener estadísticas: " . $e->getMessage());
            // Usar valores predeterminados ya establecidos
        }

        // Usuarios recientes
        $recentUsers = [];
        try {
            $recentUsers = User::getAll(5, 0);
        } catch (Exception $e) {
            error_log("Error al obtener usuarios recientes: " . $e->getMessage());
        }

        // Pagos recientes
        $recentPayments = [];
        try {
            $recentPayments = Payment::getAll(5, 0);
        } catch (Exception $e) {
            error_log("Error al obtener pagos recientes: " . $e->getMessage());
        }

        // Suscripciones por vencer
        $expiringSubscriptions = [];
        try {
            $expiringSubscriptions = Subscription::getAboutToExpire();
        } catch (Exception $e) {
            error_log("Error al obtener suscripciones por vencer: " . $e->getMessage());
        }

        // Perfiles más vistos
        $topProfiles = [];
        try {
            $topProfiles = Profile::getMostViewed(5);
        } catch (Exception $e) {
            error_log("Error al obtener perfiles más vistos: " . $e->getMessage());
        }

        $pageTitle = 'Panel de Administración';
        $pageHeader = 'Dashboard';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/dashboard.php';

        // Renderiza la vista principal
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Gestión de usuarios
     */
    public function users()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/users.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Ver detalle de usuario
     */
    public function viewUser($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/user_detail.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }


    /**
     * Editar usuario
     */
    public function editUser($params)
    {
        $userId = $params['id'] ?? 0;

        // Para depuración
        error_log("editUser llamado con ID: $userId");

        try {
            // Obtener usuario directamente de la base de datos
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Usuario encontrado: " . ($user ? json_encode($user) : 'No encontrado'));

            if (!$user) {
                setFlashMessage('danger', 'Usuario no encontrado');
                redirect('/admin/usuarios');
                exit;
            }

            $pageTitle = 'Editar Usuario';
            $pageHeader = 'Editar Usuario';

            // Define la ruta al archivo de vista específica
            $viewFile = __DIR__ . '/../views/admin/user_edit.php';

            // Verificar si el archivo existe
            if (!file_exists($viewFile)) {
                error_log("Vista no encontrada: $viewFile");
                setFlashMessage('danger', 'Error interno: Archivo de vista no encontrado');
                redirect('/admin/usuarios');
                exit;
            }

            // Renderiza la vista principal (que incluirá el contenido específico)
            require_once __DIR__ . '/../views/layouts/admin.php';
        } catch (Exception $e) {
            error_log("Error en editUser: " . $e->getMessage());
            setFlashMessage('danger', 'Error al cargar datos del usuario: ' . $e->getMessage());
            redirect('/admin/usuarios');
            exit;
        }
    }

    /**
     * Actualizar usuario
     */
    public function updateUser()
    {
        // Establecer encabezado de tipo de contenido JSON
        header('Content-Type: application/json');

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
        $password = $_POST['password'] ?? '';

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
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato de correo electrónico inválido';
        }

        if (empty($phone)) {
            $errors['phone'] = 'El teléfono es obligatorio';
        }

        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Limpiar teléfono
        $phone = preg_replace('/\D/', '', $phone);

        try {
            // Verificar que el usuario exista
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario no encontrado']);
                exit;
            }

            // Verificar si el email ya existe y no es del mismo usuario
            if ($email !== $user['email']) {
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $userId]);
                if ($stmt->fetch()) {
                    http_response_code(422);
                    echo json_encode(['errors' => ['email' => 'Este correo electrónico ya está registrado']]);
                    exit;
                }
            }

            // Verificar si el teléfono ya existe y no es del mismo usuario
            if ($phone !== $user['phone']) {
                $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
                $stmt->execute([$phone, $userId]);
                if ($stmt->fetch()) {
                    http_response_code(422);
                    echo json_encode(['errors' => ['phone' => 'Este teléfono ya está registrado']]);
                    exit;
                }
            }

            // Preparar datos de usuario
            $userData = [
                'status' => $status,
                'email' => $email,
                'phone' => $phone
            ];

            // Si se proporcionó una nueva contraseña, actualizarla
            if (!empty($password)) {
                $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Actualizar usuario directamente con SQL
            $updateFields = [];
            $updateParams = [];

            foreach ($userData as $key => $value) {
                $updateFields[] = "$key = ?";
                $updateParams[] = $value;
            }

            $updateParams[] = $userId; // Para la condición WHERE

            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($updateParams);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario actualizado correctamente',
                    'redirect' => url('/admin/usuario/' . $userId)
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar usuario']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Cambiar estado de usuario (activo/suspendido)
     */
    public function toggleUserStatus()
    {
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setFlashMessage('danger', 'Método no permitido');
            redirect('/admin/usuarios');
            exit;
        }

        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Token CSRF inválido');
            redirect('/admin/usuarios');
            exit;
        }

        // Obtener datos
        $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        // Validar datos
        if ($userId <= 0) {
            setFlashMessage('danger', 'ID de usuario inválido');
            redirect('/admin/usuarios');
            exit;
        }

        if (!in_array($status, ['active', 'suspended'])) {
            setFlashMessage('danger', 'Estado inválido');
            redirect('/admin/usuarios');
            exit;
        }

        try {
            // Verificar que el usuario exista
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                setFlashMessage('danger', 'Usuario no encontrado');
                redirect('/admin/usuarios');
                exit;
            }

            // No permitir cambiar el estado de un administrador
            if ($user['user_type'] === 'admin') {
                setFlashMessage('danger', 'No se puede cambiar el estado de un administrador');
                redirect('/admin/usuarios');
                exit;
            }

            // Actualizar estado
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $userId]);

            if ($result) {
                $message = $status === 'active' ? 'Usuario activado correctamente' : 'Usuario suspendido correctamente';
                setFlashMessage('success', $message);
            } else {
                setFlashMessage('danger', 'No se pudo actualizar el estado del usuario');
            }
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al cambiar el estado del usuario: ' . $e->getMessage());
        }

        // Redirigir de vuelta
        if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
            redirect($_POST['redirect_url']);
        } else {
            redirect('/admin/usuarios');
        }

        exit;
    }

    /**
     * Gestión de perfiles
     */
    public function profiles()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/profiles.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Ver detalle de perfil
     */
    public function viewProfile($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/user_detail.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Editar perfil
     */
    public function editProfile($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/edit_profile.php';

        // Verificar si el archivo existe
        if (!file_exists($viewFile)) {
            error_log("Vista no encontrada: $viewFile");
            setFlashMessage('danger', 'Error interno: Archivo de vista no encontrado');
            redirect('/admin/perfiles');
            exit;
        }

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Actualizar perfil
     */
    public function updateProfile()
    {
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

        try {
            // Actualizar perfil directamente en la base de datos
            $conn = getDbConnection();
            $stmt = $conn->prepare("UPDATE profiles SET name = ?, gender = ?, description = ?, whatsapp = ?, city = ?, location = ?, schedule = ?, is_verified = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $gender, $description, $whatsapp, $city, $location, $schedule, $isVerified ? 1 : 0, $profileId]);

            if ($stmt->rowCount() >= 0) { // Podría ser 0 si no hay cambios
                echo json_encode([
                    'success' => true,
                    'message' => 'Perfil actualizado correctamente',
                    'redirect' => url('/admin/perfil/' . $profileId)
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar perfil: No se pudo actualizar en la base de datos']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar perfil: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Verificar perfil
     */
    public function verifyProfile()
    {
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            if (isAjax()) {
                echo json_encode(['error' => 'Método no permitido']);
                exit;
            }
            setFlashMessage('danger', 'Método no permitido');
            redirect('/admin/perfiles');
            exit;
        }

        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            if (isAjax()) {
                echo json_encode(['error' => 'Token CSRF inválido']);
                exit;
            }
            setFlashMessage('danger', 'Token CSRF inválido');
            redirect('/admin/perfiles');
            exit;
        }

        // Obtener ID del perfil
        $profileId = $_POST['profile_id'] ?? 0;

        // Verificar que el perfil exista
        $profile = Profile::getById($profileId);

        if (!$profile) {
            http_response_code(404);
            if (isAjax()) {
                echo json_encode(['error' => 'Perfil no encontrado']);
                exit;
            }
            setFlashMessage('danger', 'Perfil no encontrado');
            redirect('/admin/perfiles');
            exit;
        }

        try {
            // Actualizar perfil en la base de datos directamente
            $conn = getDbConnection();
            $stmt = $conn->prepare("UPDATE profiles SET is_verified = 1, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$profileId]);

            if ($stmt->rowCount() > 0) {
                if (isAjax()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Perfil verificado correctamente',
                        'redirect' => url('/admin/perfil/' . $profileId)
                    ]);
                } else {
                    setFlashMessage('success', 'Perfil verificado correctamente');
                    redirect('/admin/perfil/' . $profileId);
                }
            } else {
                if (isAjax()) {
                    http_response_code(500);
                    echo json_encode(['error' => 'No se pudo verificar el perfil']);
                } else {
                    setFlashMessage('danger', 'No se pudo verificar el perfil');
                    redirect('/admin/perfil/' . $profileId);
                }
            }
        } catch (Exception $e) {
            error_log('Error al verificar perfil: ' . $e->getMessage());

            if (isAjax()) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al verificar perfil: ' . $e->getMessage()]);
            } else {
                setFlashMessage('danger', 'Error al verificar perfil: ' . $e->getMessage());
                redirect('/admin/perfil/' . $profileId);
            }
        }
        exit;
    }

    /**
     * Gestión de pagos
     */
    public function payments()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/payments.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Ver detalle de pago
     */
    public function viewPayment($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/payment_detail.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Gestión de suscripciones
     */
    public function subscriptions()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/subscriptions.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Ver detalle de suscripción
     */
    public function viewSubscription($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/subscription_detail.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Gestión de planes
     */
    public function plans()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/plans.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Editar plan
     */
    public function editPlan($params)
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/plan_edit.php';

        // Verificar si el archivo existe
        if (!file_exists($viewFile)) {
            error_log("Vista no encontrada: $viewFile");
            setFlashMessage('danger', 'Error interno: Archivo de vista no encontrado');
            redirect('/admin/planes');
            exit;
        }

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Crear nuevo plan
     */
    public function createPlan()
    {
        $pageTitle = 'Crear Plan';
        $pageHeader = 'Crear Plan';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/plan_create.php';

        // Verificar si el archivo existe
        if (!file_exists($viewFile)) {
            error_log("Vista no encontrada: $viewFile");
            setFlashMessage('danger', 'Error interno: Archivo de vista no encontrado');
            redirect('/admin/planes');
            exit;
        }

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Guarda un plan (nuevo o existente)
     */
    public function savePlan()
    {
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
    public function deletePlan()
    {
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

        try {
            // Eliminar plan
            Plan::delete($planId);

            echo json_encode([
                'success' => true,
                'message' => 'Plan eliminado correctamente',
                'redirect' => url('/admin/planes')
            ]);
        } catch (Exception $e) {
            error_log('Error al eliminar plan: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar plan: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Estadísticas generales
     */
    public function stats()
    {
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

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/stats.php';

        // Renderiza la vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Gestiona las solicitudes de verificación
     */
    public function verifications()
    {
        // Filtros
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';

        // Paginación
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Preparar filtros
        $filters = [];
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        if (!empty($type)) {
            $filters['verification_type'] = $type;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        // Obtener verificaciones
        // Aquí es necesario un modelo VerificationModel
        require_once __DIR__ . '/../models/Verification.php';
        $verifications = Verification::getAll($limit, $offset, $filters);
        $totalVerifications = Verification::count($filters);
        $pendingCount = Verification::count(['status' => 'pending']);

        // Calcular páginas
        $totalPages = ceil($totalVerifications / $limit);

        $pageTitle = 'Solicitudes de Verificación';
        $pageHeader = 'Verificaciones';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/admin/verification.php';

        // Renderiza la vista principal
        require_once __DIR__ . '/../views/layouts/admin.php';
    }

    /**
     * Actualiza el estado de un pago
     */
    public function updatePaymentStatus()
    {
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
        $paymentId = $_POST['payment_id'] ?? 0;
        $status = $_POST['payment_status'] ?? '';
        $errorMessage = $_POST['error_message'] ?? '';

        // Validar estado
        if (!in_array($status, ['pending', 'processing', 'completed', 'failed', 'refunded'])) {
            setFlashMessage('danger', 'Estado inválido');
            redirect('/admin/pagos');
            exit;
        }

        // Actualizar estado
        try {
            $updateData = ['payment_status' => $status];

            if ($status === 'failed' && !empty($errorMessage)) {
                $updateData['error_message'] = $errorMessage;
            }

            Payment::update($paymentId, $updateData);

            // Si se completa, activar suscripción
            if ($status === 'completed') {
                $payment = Payment::getById($paymentId);

                if ($payment) {
                    // Activar suscripción
                    $this->activateSubscription($payment['user_id'], $payment['plan_id'], $paymentId);
                }
            }

            setFlashMessage('success', 'Estado de pago actualizado correctamente');
            redirect('/admin/pago/' . $paymentId);
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al actualizar estado: ' . $e->getMessage());
            redirect('/admin/pago/' . $paymentId);
        }

        exit;
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

    /**
     * Cambiar estado de suscripción
     */
    public function changeSubscriptionStatus()
    {
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
        $subscriptionId = $_POST['subscription_id'] ?? 0;
        $status = $_POST['status'] ?? '';

        // Validar estado
        if (!in_array($status, ['active', 'expired', 'cancelled', 'trial'])) {
            setFlashMessage('danger', 'Estado inválido');
            redirect('/admin/suscripciones');
            exit;
        }

        // Actualizar estado directamente
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare("UPDATE subscriptions SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $subscriptionId]);

            if ($stmt->rowCount() > 0) {
                setFlashMessage('success', 'Estado de suscripción actualizado correctamente');
            } else {
                setFlashMessage('danger', 'No se pudo actualizar el estado de la suscripción');
            }

            redirect('/admin/suscripcion/' . $subscriptionId);
        } catch (Exception $e) {
            error_log('Error al cambiar estado de suscripción: ' . $e->getMessage());
            setFlashMessage('danger', 'Error al actualizar estado: ' . $e->getMessage());
            redirect('/admin/suscripcion/' . $subscriptionId);
        }

        exit;
    }

    /**
     * Cancelar suscripción
     */
    public function cancelSubscription()
    {
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

        // Obtener ID
        $subscriptionId = $_POST['subscription_id'] ?? 0;

        // Verificar que exista
        $subscription = Subscription::getById($subscriptionId);
        if (!$subscription) {
            http_response_code(404);
            echo json_encode(['error' => 'Suscripción no encontrada']);
            exit;
        }

        try {
            // Actualizar suscripción
            Subscription::update($subscriptionId, ['auto_renew' => false]);

            // Determinar tipo de respuesta
            if (isAjax()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'La renovación automática ha sido cancelada',
                    'redirect' => url('/admin/suscripcion/' . $subscriptionId)
                ]);
            } else {
                setFlashMessage('success', 'La renovación automática ha sido cancelada');
                redirect('/admin/suscripcion/' . $subscriptionId);
            }
        } catch (Exception $e) {
            error_log('Error al cancelar suscripción: ' . $e->getMessage());

            if (isAjax()) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al cancelar renovación: ' . $e->getMessage()]);
            } else {
                setFlashMessage('danger', 'Error al cancelar renovación: ' . $e->getMessage());
                redirect('/admin/suscripcion/' . $subscriptionId);
            }
        }

        exit;
    }

    /**
     * Actualizar verificación
     */
    public function updateVerification()
    {
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
        $verificationId = $_POST['verification_id'] ?? 0;
        $userId = $_POST['user_id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? '';

        // Validar estado
        if (!in_array($status, ['approved', 'rejected'])) {
            setFlashMessage('danger', 'Estado inválido');
            redirect('/admin/verificacion');
            exit;
        }

        try {
            // Actualizar verificación
            require_once __DIR__ . '/../models/Verification.php';
            Verification::updateStatus($verificationId, $status, $notes, $_SESSION['user_id']);

            // Si es aprobada y es tipo ID, marcar al usuario como verificado
            $verification = Verification::getById($verificationId);

            if ($status === 'approved' && $verification && $verification['verification_type'] === 'id_card') {
                // Obtener perfil del usuario
                $profile = Profile::getByUserId($userId);

                if ($profile) {
                    // Marcar perfil como verificado
                    Profile::update($profile['id'], ['is_verified' => true]);
                }
            }

            setFlashMessage('success', 'Verificación ' . ($status === 'approved' ? 'aprobada' : 'rechazada') . ' correctamente');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al procesar verificación: ' . $e->getMessage());
        }

        redirect('/admin/verificacion');
        exit;
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser()
    {
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

        // Obtener ID de usuario
        $userId = $_POST['user_id'] ?? 0;

        // Verificar que el usuario exista
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                setFlashMessage('danger', 'Usuario no encontrado');
                redirect('/admin/usuarios');
                exit;
            }

            // No permitir eliminar administradores
            if ($user['user_type'] === 'admin') {
                setFlashMessage('danger', 'No se pueden eliminar administradores');
                redirect('/admin/usuarios');
                exit;
            }

            // Marcar usuario como eliminado (soft delete)
            $stmt = $conn->prepare("UPDATE users SET status = 'deleted' WHERE id = ?");
            $result = $stmt->execute([$userId]);

            if ($result) {
                setFlashMessage('success', 'Usuario eliminado correctamente');
            } else {
                setFlashMessage('danger', 'No se pudo eliminar el usuario');
            }

            redirect('/admin/usuarios');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al eliminar usuario: ' . $e->getMessage());
            redirect('/admin/usuario/' . $userId);
        }

        exit;
    }
}
