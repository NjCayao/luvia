<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/SmsService.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../includes/functions.php';

class AuthController
{
    private $smsService;

    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Muestra la página de inicio de sesión
     */
    public function login()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/usuario/dashboard');
            exit;
        }

        $pageTitle = 'Iniciar Sesión';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/auth/login.php';

        // Renderiza la vista principal
        require_once __DIR__ . '/../views/layouts/auth.php';
    }

    /**
     * Procesa el inicio de sesión
     */
    public function processLogin()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/usuario/dashboard');
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

        // Obtener datos del formulario
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validar datos
        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Ingrese su teléfono o correo electrónico';
        }

        if (empty($password)) {
            $errors['password'] = 'Ingrese su contraseña';
        }

        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Buscar usuario
        $user = null;

        // Verificar si el username es un correo electrónico o teléfono
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = User::getByEmail($username);
        } else {
            // Limpiar formato del teléfono
            $username = preg_replace('/\D/', '', $username);
            $user = User::getByPhone($username);
        }

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuario o contraseña incorrectos']);
            exit;
        }

        // Verificar si la cuenta está activa
        if ($user['status'] !== 'active') {
            // Si no está verificado, redirigir a verificación
            if ($user['status'] === 'pending') {
                // Generar nuevo código de verificación
                $verificationCode = rand(100000, 999999);
                $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours'));

                User::update($user['id'], [
                    'verification_code' => $verificationCode,
                    'verification_expires' => $expiryTime
                ]);

                // Enviar código por SMS o correo
                if (SMS_VERIFICATION_ENABLED && !$user['phone_verified']) {
                    $this->smsService->sendVerificationCode($user['phone'], $verificationCode);
                }

                // Guardar ID en sesión para la verificación
                $_SESSION['pending_verification_id'] = $user['id'];

                echo json_encode([
                    'redirect' => url('/verificar'),
                    'message' => 'Su cuenta requiere verificación.'
                ]);
                exit;
            }

            // Si está suspendido u otro estado
            http_response_code(403);
            echo json_encode(['error' => 'Su cuenta está ' . $user['status'] . '. Contacte a soporte.']);
            exit;
        }

        // Iniciar sesión
        loginUser($user);

        // Redirigir según el tipo de usuario
        $redirectUrl = '/usuario/dashboard';

        if (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        } else if ($user['user_type'] === 'admin') {
            $redirectUrl = '/admin';
        } else if ($user['user_type'] === 'visitor') {
            // Verificar si tiene suscripción activa
            require_once __DIR__ . '/../models/Subscription.php';
            $subscription = Subscription::getActiveByUserId($user['id']);

            if (!$subscription) {
                // Si no tiene suscripción, redirigir a planes
                $redirectUrl = '/pago/planes';
            } else {
                // Si tiene suscripción, redirigir a la página principal con filtro de mujeres
                $redirectUrl = '/categoria/female';
            }
        } else if ($user['user_type'] === 'advertiser') {
            // Verificar si ya tiene perfil creado
            require_once __DIR__ . '/../models/Profile.php';
            $profile = Profile::getByUserId($user['id']);

            if (!$profile) {
                // Si no tiene perfil, redirigir a crear perfil
                $redirectUrl = '/usuario/editar';
            }
        }

        echo json_encode([
            'success' => true,
            'redirect' => url($redirectUrl)
        ]);
        exit;
    }

    /**
     * Muestra la página de registro
     */
    public function register()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/usuario/dashboard');
            exit;
        }

        $pageTitle = 'Registro';

        // Obtener tipo de usuario de la URL (opcional)
        $userType = isset($_GET['tipo']) && in_array($_GET['tipo'], ['advertiser', 'visitor'])
            ? $_GET['tipo']
            : 'visitor';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/auth/register.php';

        // Renderiza la vista principal
        require_once __DIR__ . '/../views/layouts/auth.php';
    }

    /**
     * Procesa el registro de usuario
     */
    public function processRegister()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/usuario/dashboard');
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

        // Obtener datos del formulario
        $countryCode = $_POST['country_code'] ?? '+51'; // Por defecto Perú si no se envía
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $userType = $_POST['user_type'] ?? 'visitor';
        $gender = $_POST['gender'] ?? '';
        $terms = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;


        // Limpiar teléfono (eliminar espacios y caracteres no numéricos)
        $phone = preg_replace('/\D/', '', $phone);

        // Asegurarnos que el código de país no tenga el símbolo + para guardar en BD
        $countryCode = ltrim($countryCode, '+');
        $fullPhoneNumber = $countryCode . $phone;

        // Validar tipo de usuario
        if (!in_array($userType, ['advertiser', 'visitor'])) {
            $userType = 'visitor';
        }

        // Validar datos
        $errors = [];

        if (empty($phone)) {
            $errors['phone'] = 'El número de teléfono es obligatorio';
        } else {
            $phoneError = validatePhone($phone);
            if ($phoneError) {
                $errors['phone'] = $phoneError;
            } else {

                if ($countryCode === '51' && strlen($phone) !== 9) {
                    $errors['phone'] = 'El número de teléfono peruano debe tener 9 dígitos';
                } else if (strlen($phone) < 6) { // Validación básica para otros países
                    $errors['phone'] = 'El número de teléfono no parece válido';
                }

                // Verificar si el teléfono ya está registrado
                $existingUser = User::getByPhone($fullPhoneNumber);
                if ($existingUser) {
                    $errors['phone'] = 'Este número de teléfono ya está registrado';
                }
            }
        }

        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es obligatorio';
        } else {
            $emailError = validateEmail($email);
            if ($emailError) {
                $errors['email'] = $emailError;
            } else {
                // Verificar si el email ya está registrado
                $existingUser = User::getByEmail($email);
                if ($existingUser) {
                    $errors['email'] = 'Este correo electrónico ya está registrado';
                }
            }
        }

        if (empty($password)) {
            $errors['password'] = 'La contraseña es obligatoria';
        } else {
            $passwordError = validateMinLength($password, 8, 'La contraseña');
            if ($passwordError) {
                $errors['password'] = $passwordError;
            }
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden';
        }

        if ($userType === 'advertiser' && empty($gender)) {
            $errors['gender'] = 'Debe seleccionar su género';
        }

        if (!$terms) {
            $errors['terms'] = 'Debe aceptar los términos y condiciones';
        }

        // Si hay errores, devolver respuesta JSON crear el usuario
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        try {
            // Crear usuario
            $userData = [
                'phone' => $fullPhoneNumber,
                'email' => $email,
                'password' => $password,
                'user_type' => $userType,
                'gender' => $gender
            ];

            $result = User::create($userData);
            $userId = $result['id'];
            $verificationCode = $result['verification_code'];

            // Enviar código por SMS
            if (SMS_VERIFICATION_ENABLED) {
                try {
                    // Construir número internacional para el SMS
                    $internationalPhone = '+' . $fullPhoneNumber;
                    $this->smsService->sendVerificationCode($internationalPhone, $verificationCode);
                } catch (Exception $e) {
                    // Registrar el error pero continuar
                    error_log("Error al enviar SMS: " . $e->getMessage());
                    // Para desarrollo, mostrar el código en el error log para facilitar las pruebas
                    error_log("CÓDIGO DE VERIFICACIÓN (para desarrollo): $verificationCode");
                }
            }

            // Guardar ID en sesión para la verificación
            $_SESSION['pending_verification_id'] = $userId;

            // Crear suscripción de prueba para anunciantes
            if ($userType === 'advertiser') {
                require_once __DIR__ . '/../models/Subscription.php';
                Subscription::createTrial($userId);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Registro exitoso. Por favor verifique su cuenta.',
                'redirect' => url('/verificar')
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al registrar: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Muestra la página de verificación
     */
    public function verify()
    {
        // Si no hay ID de verificación pendiente, redirigir a login
        if (!isset($_SESSION['pending_verification_id'])) {
            redirect('/login');
            exit;
        }

        $userId = $_SESSION['pending_verification_id'];
        $user = User::getById($userId);

        if (!$user) {
            unset($_SESSION['pending_verification_id']);
            redirect('/login');
            exit;
        }

        $pageTitle = 'Verificar Cuenta';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/auth/verify.php';

        // Renderiza la vista principal
        require_once __DIR__ . '/../views/layouts/auth.php';
    }

    /**
     * Procesa la verificación de cuenta
     */
    public function processVerify()
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

        // Verificar si hay ID de verificación pendiente
        if (!isset($_SESSION['pending_verification_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No hay verificación pendiente']);
            exit;
        }

        $userId = $_SESSION['pending_verification_id'];
        $code = $_POST['code'] ?? '';

        // Validar código
        if (empty($code)) {
            http_response_code(422);
            echo json_encode(['error' => 'Ingrese el código de verificación']);
            exit;
        }

        // Verificar código
        if (!User::verifyCode($userId, $code)) {
            http_response_code(422);
            echo json_encode(['error' => 'Código inválido o expirado']);
            exit;
        }

        // Verificar teléfono del usuario
        User::verifyPhone($userId);

        // Obtener usuario
        $user = User::getById($userId);

        // Activar la cuenta directamente
        User::update($userId, ['status' => 'active']);

        // Limpiar sesión de verificación
        unset($_SESSION['pending_verification_id']);

        // Iniciar sesión automáticamente
        loginUser($user);

        // Preparar redirección según tipo de usuario
        $redirectUrl = '/usuario/dashboard';

        // Para visitantes, redirigir a planes
        if ($user['user_type'] === 'visitor') {
            $redirectUrl = '/pago/planes';
        } else if ($user['user_type'] === 'advertiser') {
            $redirectUrl = '/usuario/editar'; // Para que creen su perfil
        }

        echo json_encode([
            'success' => true,
            'message' => 'Verificación exitosa',
            'redirect' => url($redirectUrl)
        ]);

        exit;
    }

    /**
     * Reenvía el código de verificación
     */
    public function resendCode()
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

        // Verificar si hay ID de verificación pendiente
        if (!isset($_SESSION['pending_verification_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No hay verificación pendiente']);
            exit;
        }

        $userId = $_SESSION['pending_verification_id'];
        $user = User::getById($userId);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        // Generar nuevo código
        $verificationCode = rand(100000, 999999);
        $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours'));

        User::update($userId, [
            'verification_code' => $verificationCode,
            'verification_expires' => $expiryTime
        ]);

        // Enviar código por SMS
        if (SMS_VERIFICATION_ENABLED) {
            $this->smsService->sendVerificationCode($user['phone'], $verificationCode);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Código de verificación reenviado'
        ]);

        exit;
    }

    /**
     * Muestra la página de recuperación de contraseña
     */
    public function forgotPassword()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect('/usuario/dashboard');
            exit;
        }

        $pageTitle = 'Recuperar Contraseña';

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/auth.php';
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function processForgotPassword()
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

        $username = $_POST['username'] ?? '';

        // Validar username
        if (empty($username)) {
            http_response_code(422);
            echo json_encode(['error' => 'Ingrese su teléfono o correo electrónico']);
            exit;
        }

        // Buscar usuario
        $user = null;

        // Verificar si el username es un correo electrónico o teléfono
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = User::getByEmail($username);
        } else {
            // Limpiar formato del teléfono
            $username = preg_replace('/\D/', '', $username);
            $user = User::getByPhone($username);
        }

        // Si el usuario no existe, simular éxito por seguridad
        if (!$user) {
            echo json_encode([
                'success' => true,
                'message' => 'Si su cuenta existe, recibirá instrucciones para recuperar su contraseña.'
            ]);
            exit;
        }

        // Generar código de recuperación
        $resetCode = rand(100000, 999999);
        $expiryTime = date('Y-m-d H:i:s', strtotime('+1 hour'));

        User::update($user['id'], [
            'verification_code' => $resetCode,
            'verification_expires' => $expiryTime
        ]);

        // Enviar código por SMS o email
        if ($user['phone_verified'] && SMS_VERIFICATION_ENABLED) {
            $this->smsService->sendSms($user['phone'], "Su código para recuperar contraseña es: $resetCode. Válido por 1 hora.");
        } else if ($user['email_verified']) {
            // Implementar envío de email
            // ...
        }

        // Guardar ID en sesión para el reseteo
        $_SESSION['reset_password_id'] = $user['id'];

        echo json_encode([
            'success' => true,
            'message' => 'Se han enviado instrucciones para recuperar su contraseña.',
            'redirect' => url('/reset-password')
        ]);

        exit;
    }

    /**
     * Muestra la página de reseteo de contraseña
     */
    public function resetPassword()
    {
        // Si no hay ID de reseteo pendiente, redirigir a forgot-password
        if (!isset($_SESSION['reset_password_id'])) {
            redirect('/forgot-password');
            exit;
        }

        $userId = $_SESSION['reset_password_id'];
        $user = User::getById($userId);

        if (!$user) {
            unset($_SESSION['reset_password_id']);
            redirect('/forgot-password');
            exit;
        }

        $pageTitle = 'Restablecer Contraseña';

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/auth.php';
        require_once __DIR__ . '/../views/auth/reset_password.php';
    }

    /**
     * Procesa el reseteo de contraseña
     */
    public function processResetPassword()
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

        // Verificar si hay ID de reseteo pendiente
        if (!isset($_SESSION['reset_password_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No hay solicitud de reseteo pendiente']);
            exit;
        }

        $userId = $_SESSION['reset_password_id'];
        $code = $_POST['code'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validar datos
        $errors = [];

        if (empty($code)) {
            $errors['code'] = 'Ingrese el código de verificación';
        }

        if (empty($password)) {
            $errors['password'] = 'Ingrese la nueva contraseña';
        } else {
            $passwordError = validateMinLength($password, 8, 'La contraseña');
            if ($passwordError) {
                $errors['password'] = $passwordError;
            }
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden';
        }

        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Verificar código
        if (!User::verifyCode($userId, $code)) {
            http_response_code(422);
            echo json_encode(['error' => 'Código inválido o expirado']);
            exit;
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        User::update($userId, [
            'password' => $hashedPassword,
            'verification_code' => null,
            'verification_expires' => null
        ]);

        // Limpiar sesión de reseteo
        unset($_SESSION['reset_password_id']);

        echo json_encode([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente',
            'redirect' => url('/login')
        ]);

        exit;
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Verificar si está logueado
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }

        // Cerrar sesión
        logoutUser();

        // Redirigir a login
        redirect('/login');
        exit;
    }
}