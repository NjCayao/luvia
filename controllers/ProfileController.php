<?php
// controllers/ProfileController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Media.php';
require_once __DIR__ . '/../models/Rate.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/validation.php';

class ProfileController
{
    /**
     * Muestra el panel de control del usuario
     */
    public function dashboard()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = User::getById($userId);

        // Obtener perfil si es anunciante
        $profile = null;
        if ($user['user_type'] === 'advertiser') {
            $profile = Profile::getByUserId($userId);
        }

        // Obtener suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        // Verificar si tiene período de prueba activo (anunciantes)
        $hasTrial = false;

        if ($user['user_type'] === 'advertiser') {
            // Calcular días desde registro
            $registrationDate = new DateTime($user['created_at']);
            $now = new DateTime();
            $daysSinceRegistration = $now->diff($registrationDate)->days;

            // Período de prueba activo si no han pasado más de FREE_TRIAL_DAYS
            $hasTrial = $daysSinceRegistration < FREE_TRIAL_DAYS;
            $trialDaysLeft = $hasTrial ? FREE_TRIAL_DAYS - $daysSinceRegistration : 0;
        }

        $pageTitle = 'Mi Cuenta';
        $pageHeader = 'Panel de Control';

        // Renderizar vista
        $viewFile = __DIR__ . '/../views/profile/dashboard.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra el formulario para editar perfil
     */
    public function showEdit()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            setFlashMessage('warning', 'Solo los anunciantes pueden crear perfiles');
            redirect('/usuario/dashboard');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = User::getById($userId);

        // Obtener perfil existente o crear uno nuevo
        $profile = Profile::getByUserId($userId);

        // Obtener todas las provincias
        $provinces = Profile::getAvailableProvinces();

        // Si hay perfil con provincia seleccionada, obtener distritos
        $districts = [];
        if ($profile && !empty($profile['province_id'])) {
            $districts = Profile::getDistrictsByProvinceId($profile['province_id']);
        }

        // Adquirir datos adicionales del perfil si existe
        if ($profile) {
            // Si tienen provincias y distritos asignados, obtener sus nombres para mostrar
            if (!empty($profile['province_id'])) {
                $stmt = getDbConnection()->prepare("SELECT name FROM provinces WHERE id = ?");
                $stmt->execute([$profile['province_id']]);
                $province = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($province) {
                    $profile['province_name'] = $province['name'];
                }
            }

            if (!empty($profile['district_id'])) {
                $stmt = getDbConnection()->prepare("SELECT name FROM districts WHERE id = ?");
                $stmt->execute([$profile['district_id']]);
                $district = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($district) {
                    $profile['district_name'] = $district['name'];
                }
            }
        }

        $pageTitle = $profile ? 'Editar Perfil' : 'Crear Perfil';
        $pageHeader = $pageTitle;

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/profile/edit.php';

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Procesa la creación o actualización de perfil
     */
    public function processEdit()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
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

        $userId = $_SESSION['user_id'];

        // Obtener datos del formulario
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $whatsapp = $_POST['whatsapp'] ?? '';
        $countryCode = $_POST['country_code'] ?? '+51'; // Asegúrate de capturar el código de país
        $provinceId = !empty($_POST['province_id']) ? intval($_POST['province_id']) : null;
        $districtId = !empty($_POST['district_id']) ? intval($_POST['district_id']) : null;
        $location = $_POST['location'] ?? '';
        $schedule = $_POST['schedule'] ?? '';

        // Validar datos
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'El nombre es obligatorio';
        } else if (strlen($name) > 100) {
            $errors['name'] = 'El nombre no puede exceder los 100 caracteres';
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

        if (empty($provinceId)) {
            $errors['province_id'] = 'La provincia es obligatoria';
        }

        // Solo validar district_id si existe una provincia seleccionada
        if (!empty($provinceId) && empty($districtId)) {
            $errors['district_id'] = 'El distrito es obligatorio';
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

        // Limpiar número de WhatsApp y asegurar que tenga el código de país
        $whatsapp = preg_replace('/\D/', '', $whatsapp);
        // Eliminar el símbolo '+' si existe en el código de país
        $countryCode = ltrim($countryCode, '+');
        // Si el número no comienza con el código de país, agregarlo
        if (!preg_match('/^' . $countryCode . '/', $whatsapp)) {
            $whatsapp = $countryCode . $whatsapp;
        }

        // Verificar si ya existe un perfil para este usuario
        $existingProfile = Profile::getByUserId($userId);

        try {
            if ($existingProfile) {
                // Actualizar perfil existente
                $updateData = [
                    'name' => $name,
                    'description' => $description,
                    'whatsapp' => $whatsapp,
                    'schedule' => $schedule
                ];

                // Solo incluir estos campos si están presentes para evitar errores SQL
                if (!empty($provinceId)) {
                    $updateData['province_id'] = $provinceId;
                }

                if (!empty($districtId)) {
                    $updateData['district_id'] = $districtId;
                }

                if (!empty($location)) {
                    $updateData['location'] = $location;
                }

                Profile::update($existingProfile['id'], $updateData);

                $profileId = $existingProfile['id'];
                $message = 'Perfil actualizado correctamente';
            } else {
                // Obtener el género del usuario
                $user = User::getById($userId);
                $gender = $user['gender'] ?? 'female';

                // Crear nuevo perfil
                $profileData = [
                    'user_id' => $userId,
                    'name' => $name,
                    'gender' => $gender,
                    'description' => $description,
                    'whatsapp' => $whatsapp,
                    'schedule' => $schedule,
                    'is_verified' => false
                ];

                // Solo incluir estos campos si están presentes para evitar errores SQL
                if (!empty($provinceId)) {
                    $profileData['province_id'] = $provinceId;
                }

                if (!empty($districtId)) {
                    $profileData['district_id'] = $districtId;
                }

                if (!empty($location)) {
                    $profileData['location'] = $location;
                }

                $profileId = Profile::create($profileData);

                $message = 'Perfil creado correctamente';
            }

            // Procesar tarifas si se enviaron
            if (isset($_POST['rates'])) {
                $rates = json_decode($_POST['rates'], true);

                if (is_array($rates)) {
                    // Primero eliminar tarifas existentes si las hay
                    if ($existingProfile) {
                        Rate::deleteByProfileId($profileId);
                    }

                    // Luego crear las nuevas tarifas
                    foreach ($rates as $rate) {
                        if (isset($rate['rate_type'], $rate['price'])) {
                            Rate::create([
                                'profile_id' => $profileId,
                                'rate_type' => $rate['rate_type'],
                                'description' => $rate['description'] ?? '',
                                'price' => $rate['price']
                            ]);
                        }
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => $message,
                'redirect' => url('/usuario/medios')
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar el perfil: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Muestra la página de gestión de medios (fotos y videos)
     */
    public function showMedia()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            setFlashMessage('warning', 'Solo los anunciantes pueden gestionar medios');
            redirect('/usuario/dashboard');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Verificar que tenga un perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            setFlashMessage('warning', 'Primero debe crear su perfil');
            redirect('/usuario/editar');
            exit;
        }

        // Obtener fotos y videos
        $photos = Media::getPhotosByProfileId($profile['id']);
        $videos = Media::getVideosByProfileId($profile['id']);

        // Establecer límites según período de prueba o plan
        $maxPhotos = 1; // Límite por defecto para período de prueba
        $maxVideos = 0; // Sin videos en período de prueba

        // Verificar si está en período de prueba
        $user = User::getById($userId);
        $registrationDate = new DateTime($user['created_at']);
        $now = new DateTime();
        $daysSinceRegistration = $now->diff($registrationDate)->days;
        $hasTrial = $daysSinceRegistration < 15; // 15 días de período de prueba

        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                // Asignar límites según el plan
                if ($plan['name'] === 'Plan Básico') {
                    $maxPhotos = 2;
                    $maxVideos = 1;
                } elseif ($plan['name'] === 'Plan Premium') {
                    $maxPhotos = 5;
                    $maxVideos = 2;
                } else {
                    // Para otros planes, usar valores del plan
                    $maxPhotos = $plan['max_photos'] ?? $maxPhotos;
                    $maxVideos = $plan['max_videos'] ?? $maxVideos;
                }
            }
        }

        $pageTitle = 'Gestionar Fotos y Videos';
        $pageHeader = 'Mis Fotos y Videos';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/profile/media.php';

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Sube una nueva foto
     */
    public function uploadPhoto()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
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

        $userId = $_SESSION['user_id'];

        // Verificar que tenga un perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            http_response_code(400);
            echo json_encode(['error' => 'Primero debe crear su perfil']);
            exit;
        }

        // Verificar si no ha excedido el límite de fotos
        $photoCount = Media::countPhotos($profile['id']);

        // Obtener límite según suscripción o periodo de prueba
        $maxPhotos = 1; // Límite por defecto para período de prueba
        $planName = "Período de prueba";

        // Verificar si está en período de prueba
        $user = User::getById($userId);
        $registrationDate = new DateTime($user['created_at']);
        $now = new DateTime();
        $daysSinceRegistration = $now->diff($registrationDate)->days;
        $hasTrial = $daysSinceRegistration < 15; // 15 días de período de prueba

        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                // Asignar límites según el plan
                if ($plan['name'] === 'Plan Básico') {
                    $maxPhotos = 2;
                    $planName = "Plan Básico";
                } elseif ($plan['name'] === 'Plan Premium') {
                    $maxPhotos = 5;
                    $planName = "Plan Premium";
                } else {
                    // Para otros planes, usar el valor del plan
                    $maxPhotos = $plan['max_photos'] ?? $maxPhotos;
                    $planName = $plan['name'];
                }
            }
        } elseif (!$hasTrial) {
            // Si no tiene suscripción activa y ya no está en período de prueba
            http_response_code(400);
            echo json_encode(['error' => 'No tiene una suscripción activa. Adquiera un plan para continuar.']);
            exit;
        }

        if ($photoCount >= $maxPhotos) {
            http_response_code(400);
            echo json_encode(['error' => "Ha alcanzado el límite de $maxPhotos fotos de su $planName. Actualice su plan para subir más."]);
            exit;
        }

        // Verificar que se haya subido un archivo
        if (!isset($_FILES['photo']) || empty($_FILES['photo']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No se ha seleccionado ninguna foto']);
            exit;
        }

        // Validar el archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($_FILES['photo']['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['error' => 'La foto no debe exceder los 5MB']);
            exit;
        }

        $fileType = mime_content_type($_FILES['photo']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            http_response_code(422);
            echo json_encode(['error' => 'Tipo de archivo no permitido. Use JPG, PNG o WEBP']);
            exit;
        }

        try {
            // Generar nombre único para el archivo
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('photo_') . '.' . $extension;

            // Directorio para fotos
            $uploadPath = __DIR__ . '/../public/uploads/photos/';

            // Crear directorio si no existe
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Subir archivo
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath . $filename)) {
                // Determinar si es la primera foto (será la principal)
                $isPrimary = $photoCount === 0;

                // Guardar en base de datos
                $mediaId = Media::create([
                    'profile_id' => $profile['id'],
                    'media_type' => 'photo',
                    'filename' => $filename,
                    'order_num' => $photoCount,
                    'is_primary' => $isPrimary
                ]);

                // Obtener información completa del nuevo medio
                $media = Media::getById($mediaId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Foto subida correctamente',
                    'media' => $media
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al subir la foto']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

        exit;
    }

    /**
     * Sube un nuevo video
     */
    public function uploadVideo()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
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

        $userId = $_SESSION['user_id'];

        // Verificar que tenga un perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            http_response_code(400);
            echo json_encode(['error' => 'Primero debe crear su perfil']);
            exit;
        }

        // Verificar si no ha excedido el límite de videos
        $videoCount = Media::countVideos($profile['id']);

        // Obtener límite según suscripción o periodo de prueba
        $maxVideos = 0; // Por defecto, sin videos en período de prueba
        $planName = "Período de prueba";

        // Verificar si está en período de prueba
        $user = User::getById($userId);
        $registrationDate = new DateTime($user['created_at']);
        $now = new DateTime();
        $daysSinceRegistration = $now->diff($registrationDate)->days;
        $hasTrial = $daysSinceRegistration < 15; // 15 días de período de prueba

        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                // Asignar límites según el plan
                if ($plan['name'] === 'Plan Básico') {
                    $maxVideos = 1;
                    $planName = "Plan Básico";
                } elseif ($plan['name'] === 'Plan Premium') {
                    $maxVideos = 2;
                    $planName = "Plan Premium";
                } else {
                    // Para otros planes, usar el valor del plan
                    $maxVideos = $plan['max_videos'] ?? $maxVideos;
                    $planName = $plan['name'];
                }
            }
        } elseif (!$hasTrial) {
            // Si no tiene suscripción activa y ya no está en período de prueba
            http_response_code(400);
            echo json_encode(['error' => 'No tiene una suscripción activa. Adquiera un plan para continuar.']);
            exit;
        } else {
            // En periodo de prueba, no permite videos
            http_response_code(400);
            echo json_encode(['error' => 'El período de prueba no incluye videos. Adquiera un plan para subir videos.']);
            exit;
        }

        if ($videoCount >= $maxVideos) {
            http_response_code(400);
            echo json_encode(['error' => "Ha alcanzado el límite de $maxVideos videos de su $planName. Actualice su plan para subir más."]);
            exit;
        }

        // Verificar que se haya subido un archivo
        if (!isset($_FILES['video']) || empty($_FILES['video']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No se ha seleccionado ningún video']);
            exit;
        }

        // Validar el archivo
        $allowedTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        $maxSize = 100 * 1024 * 1024; // 100MB

        if ($_FILES['video']['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['error' => 'El video no debe exceder los 100MB']);
            exit;
        }

        $fileType = mime_content_type($_FILES['video']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            http_response_code(422);
            echo json_encode(['error' => 'Tipo de archivo no permitido. Use MP4, WEBM o MOV']);
            exit;
        }

        try {
            // Generar nombre único para el archivo
            $extension = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('video_') . '.' . $extension;

            // Directorio para videos
            $uploadPath = __DIR__ . '/../public/uploads/videos/';

            // Crear directorio si no existe
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Subir archivo
            if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath . $filename)) {
                // Guardar en base de datos
                $mediaId = Media::create([
                    'profile_id' => $profile['id'],
                    'media_type' => 'video',
                    'filename' => $filename,
                    'order_num' => $videoCount,
                    'is_primary' => false
                ]);

                // Obtener información completa del nuevo medio
                $media = Media::getById($mediaId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Video subido correctamente',
                    'media' => $media
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al subir el video']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

        exit;
    }


    /**
     * Obtiene la lista actual de fotos (para actualizar UI mediante AJAX)
     */
    public function getPhotos()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['error' => 'Perfil no encontrado']);
            exit;
        }

        // Obtener fotos
        $photos = Media::getPhotosByProfileId($profile['id']);

        // Establecer límites según período de prueba o plan
        $maxPhotos = 1; // Límite por defecto para período de prueba

        // Verificar si está en período de prueba
        $user = User::getById($userId);
        $registrationDate = new DateTime($user['created_at']);
        $now = new DateTime();
        $daysSinceRegistration = $now->diff($registrationDate)->days;
        $hasTrial = $daysSinceRegistration < 15; // 15 días de período de prueba

        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                // Asignar límites según el plan
                if ($plan['name'] === 'Plan Básico') {
                    $maxPhotos = 2;
                } elseif ($plan['name'] === 'Plan Premium') {
                    $maxPhotos = 5;
                } else {
                    // Para otros planes, usar valores del plan
                    $maxPhotos = $plan['max_photos'] ?? $maxPhotos;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'photos' => $photos,
            'max_photos' => $maxPhotos
        ]);
        exit;
    }

    /**
     * Obtiene la lista actual de videos (para actualizar UI mediante AJAX)
     */
    public function getVideos()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['error' => 'Perfil no encontrado']);
            exit;
        }

        // Obtener videos
        $videos = Media::getVideosByProfileId($profile['id']);

        // Establecer límites según período de prueba o plan
        $maxVideos = 0; // Sin videos en período de prueba

        // Verificar si está en período de prueba
        $user = User::getById($userId);
        $registrationDate = new DateTime($user['created_at']);
        $now = new DateTime();
        $daysSinceRegistration = $now->diff($registrationDate)->days;
        $hasTrial = $daysSinceRegistration < 15; // 15 días de período de prueba

        // Verificar si tiene suscripción activa
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                // Asignar límites según el plan
                if ($plan['name'] === 'Plan Básico') {
                    $maxVideos = 1;
                } elseif ($plan['name'] === 'Plan Premium') {
                    $maxVideos = 2;
                } else {
                    // Para otros planes, usar valores del plan
                    $maxVideos = $plan['max_videos'] ?? $maxVideos;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'videos' => $videos,
            'max_videos' => $maxVideos
        ]);
        exit;
    }

    /**
     * Establece una foto como principal
     */
    public function setPrimaryPhoto()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        $mediaId = $_POST['media_id'] ?? 0;

        // Obtener el medio
        $media = Media::getById($mediaId);

        if (!$media) {
            http_response_code(404);
            echo json_encode(['error' => 'Medio no encontrado']);
            exit;
        }

        // Verificar que sea una foto
        if ($media['media_type'] !== 'photo') {
            http_response_code(400);
            echo json_encode(['error' => 'El medio seleccionado no es una foto']);
            exit;
        }

        // Verificar que el perfil pertenezca al usuario
        $profile = Profile::getById($media['profile_id']);

        if (!$profile || $profile['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'No tiene permiso para modificar este perfil']);
            exit;
        }

        // Establecer como principal
        if (Media::setAsPrimary($mediaId)) {
            echo json_encode([
                'success' => true,
                'message' => 'Foto establecida como principal'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al establecer la foto como principal']);
        }

        exit;
    }

    /**
     * Elimina un medio (foto o video)
     */
    public function deleteMedia()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        $mediaId = $_POST['media_id'] ?? 0;

        // Obtener el medio
        $media = Media::getById($mediaId);

        if (!$media) {
            http_response_code(404);
            echo json_encode(['error' => 'Medio no encontrado']);
            exit;
        }

        // Verificar que el perfil pertenezca al usuario
        $profile = Profile::getById($media['profile_id']);

        if (!$profile || $profile['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'No tiene permiso para modificar este perfil']);
            exit;
        }

        // Eliminar de la base de datos y obtener nombre de archivo
        $filename = Media::delete($mediaId);

        if ($filename) {
            // Determinar la carpeta según el tipo de medio
            $folder = $media['media_type'] === 'photo' ? 'photos' : 'videos';
            $filePath = UPLOAD_DIR . $folder . '/' . $filename;

            // Eliminar archivo físico
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Medio eliminado correctamente',
                'media_type' => $media['media_type']
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar el medio']);
        }

        exit;
    }

    /**
     * Reordena los medios
     */
    public function reorderMedia()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        // Obtener datos
        $profileId = $_POST['profile_id'] ?? 0;
        $mediaType = $_POST['media_type'] ?? '';
        $idOrder = isset($_POST['id_order']) ? json_decode($_POST['id_order'], true) : [];

        // Validar datos
        if (empty($profileId) || empty($mediaType) || empty($idOrder)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        // Verificar que el perfil pertenezca al usuario
        $profile = Profile::getById($profileId);

        if (!$profile || $profile['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'No tiene permiso para modificar este perfil']);
            exit;
        }

        // Reordenar
        if (Media::reorder($profileId, $mediaType, $idOrder)) {
            echo json_encode([
                'success' => true,
                'message' => 'Orden actualizado correctamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el orden']);
        }

        exit;
    }

    /**
     * Muestra la página de gestión de tarifas
     */
    public function showRates()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            setFlashMessage('warning', 'Solo los anunciantes pueden gestionar tarifas');
            redirect('/usuario/dashboard');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Verificar que tenga un perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            setFlashMessage('warning', 'Primero debe crear su perfil');
            redirect('/usuario/editar');
            exit;
        }

        // Obtener tarifas existentes
        $rates = Rate::getByProfileId($profile['id']);

        $pageTitle = 'Gestionar Tarifas';
        $pageHeader = 'Mis Tarifas';

        // Renderizar vista
        $viewFile = __DIR__ . '/../views/profile/rates.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Guarda las tarifas
     */
    public function saveRates()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
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

        $userId = $_SESSION['user_id'];

        // Verificar que tenga un perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            http_response_code(400);
            echo json_encode(['error' => 'Primero debe crear su perfil']);
            exit;
        }

        // Obtener datos
        $rates = isset($_POST['rates']) ? json_decode($_POST['rates'], true) : [];

        // Validar datos
        if (empty($rates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No se han enviado tarifas']);
            exit;
        }

        // Validar cada tarifa
        $errors = [];

        foreach ($rates as $key => $rate) {
            if (!isset($rate['rate_type']) || empty($rate['rate_type'])) {
                $errors["rates[$key][rate_type]"] = 'El tipo de tarifa es obligatorio';
            }

            if (!isset($rate['price']) || !is_numeric($rate['price']) || $rate['price'] <= 0) {
                $errors["rates[$key][price]"] = 'El precio debe ser un número positivo';
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Guardar tarifas
        if (Rate::saveMultiple($profile['id'], $rates)) {
            echo json_encode([
                'success' => true,
                'message' => 'Tarifas guardadas correctamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar las tarifas']);
        }

        exit;
    }



    /**
     * Muestra las estadísticas del perfil del usuario
     */
    public function showStats()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        // Verificar que sea un anunciante
        if ($_SESSION['user_type'] !== 'advertiser') {
            setFlashMessage('warning', 'Esta sección es solo para anunciantes');
            redirect('/usuario/dashboard');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = User::getById($userId);

        // Obtener perfil
        $profile = Profile::getByUserId($userId);

        if (!$profile) {
            setFlashMessage('warning', 'Primero debe crear su perfil');
            redirect('/usuario/editar');
            exit;
        }

        // Obtener datos para estadísticas
        $stats = [
            'profile' => $profile,
            'period' => $_GET['period'] ?? 'month', // Período por defecto: month (también: week, year)
            'total_views' => $profile['views'],
            'total_clicks' => $profile['whatsapp_clicks'],
            'total_conversion_rate' => $profile['views'] > 0 ?
                round(($profile['whatsapp_clicks'] / $profile['views']) * 100, 2) : 0,
            'avg_daily_views' => $this->calculateAvgDailyViews($profile['id'], $_GET['period'] ?? 'month'),
            'avg_daily_clicks' => $this->calculateAvgDailyClicks($profile['id'], $_GET['period'] ?? 'month'),
            'avg_category_views' => $this->getAvgCategoryViews($profile['gender']),
            'avg_category_clicks' => $this->getAvgCategoryClicks($profile['gender']),
            'ranking_position' => $this->getProfileRanking($profile['id'], $profile['gender']),
            'total_in_category' => $this->getTotalProfilesInCategory($profile['gender']),
            'views_by_day' => $this->getViewsByDay($profile['id'], $_GET['period'] ?? 'month'),
            'clicks_by_day' => $this->getClicksByDay($profile['id'], $_GET['period'] ?? 'month'),
            'conversion_rate' => $this->getConversionRateByDay($profile['id'], $_GET['period'] ?? 'month'),
            'period_days' => $this->getPeriodDays($_GET['period'] ?? 'month')
        ];

        // Calcular percentil (porcentaje en ranking)
        $stats['percentile'] = $stats['total_in_category'] > 0 ?
            round((1 - ($stats['ranking_position'] / $stats['total_in_category'])) * 100, 2) : 0;

        $pageTitle = 'Estadísticas de mi Perfil';
        $pageHeader = 'Estadísticas Detalladas';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/profile/stats.php';

        // Renderiza la vista
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Calcula el promedio diario de vistas en un período
     */
    private function calculateAvgDailyViews($profileId, $period = 'month')
    {
        // Implementar lógica para calcular vistas promedio diarias
        // Usando datos de la tabla analytics_views o similar

        // Para una implementación básica, podemos dividir el total de vistas por el número de días
        $profile = Profile::getById($profileId);
        $days = $this->getPeriodDays($period);

        // Si el perfil es nuevo, ajustar los días desde la creación
        $profileCreationDate = new DateTime($profile['created_at']);
        $now = new DateTime();
        $daysSinceCreation = $now->diff($profileCreationDate)->days;
        $daysSinceCreation = max(1, $daysSinceCreation); // Mínimo 1 día

        $days = min($days, $daysSinceCreation);

        return round($profile['views'] / $days, 2);
    }

    /**
     * Calcula el promedio diario de clics en WhatsApp en un período
     */
    private function calculateAvgDailyClicks($profileId, $period = 'month')
    {
        // Similar a calculateAvgDailyViews pero para clics
        $profile = Profile::getById($profileId);
        $days = $this->getPeriodDays($period);

        // Si el perfil es nuevo, ajustar los días desde la creación
        $profileCreationDate = new DateTime($profile['created_at']);
        $now = new DateTime();
        $daysSinceCreation = $now->diff($profileCreationDate)->days;
        $daysSinceCreation = max(1, $daysSinceCreation); // Mínimo 1 día

        $days = min($days, $daysSinceCreation);

        return round($profile['whatsapp_clicks'] / $days, 2);
    }

    /**
     * Obtiene el promedio de vistas para perfiles de la misma categoría (género)
     */
    private function getAvgCategoryViews($gender)
    {
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT AVG(views) as avg_views FROM profiles WHERE gender = ?");
            $stmt->execute([$gender]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['avg_views'], 2);
        } catch (Exception $e) {
            error_log("Error obteniendo promedio de vistas por categoría: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene el promedio de clics para perfiles de la misma categoría (género)
     */
    private function getAvgCategoryClicks($gender)
    {
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT AVG(whatsapp_clicks) as avg_clicks FROM profiles WHERE gender = ?");
            $stmt->execute([$gender]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return round($result['avg_clicks'], 2);
        } catch (Exception $e) {
            error_log("Error obteniendo promedio de clics por categoría: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene la posición del perfil en el ranking por vistas
     */
    private function getProfileRanking($profileId, $gender)
    {
        try {
            $conn = getDbConnection();

            // Primero obtenemos las vistas del perfil
            $stmt = $conn->prepare("SELECT views FROM profiles WHERE id = ?");
            $stmt->execute([$profileId]);
            $profileViews = $stmt->fetch(PDO::FETCH_ASSOC)['views'];

            // Luego contamos cuántos perfiles tienen más vistas
            $stmt = $conn->prepare("SELECT COUNT(*) as position FROM profiles WHERE gender = ? AND views > ?");
            $stmt->execute([$gender, $profileViews]);
            $position = $stmt->fetch(PDO::FETCH_ASSOC)['position'];

            // La posición es el número de perfiles con más vistas + 1
            return $position + 1;
        } catch (Exception $e) {
            error_log("Error obteniendo ranking: " . $e->getMessage());
            return 1; // Por defecto, primero
        }
    }

    /**
     * Obtiene el total de perfiles en la misma categoría
     */
    private function getTotalProfilesInCategory($gender)
    {
        try {
            $conn = getDbConnection();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM profiles WHERE gender = ?");
            $stmt->execute([$gender]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            error_log("Error contando perfiles por categoría: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene las vistas por día en un período
     */
    private function getViewsByDay($profileId, $period = 'month')
    {
        // Idealmente, esta información vendría de una tabla de analíticas
        // Como implementación básica, generamos datos de ejemplo
        $days = $this->getPeriodDays($period);

        $results = [];
        $startDate = new DateTime();
        $startDate->modify("-{$days} days");

        for ($i = 0; $i < $days; $i++) {
            $date = clone $startDate;
            $date->modify("+{$i} days");
            $dateStr = $date->format('Y-m-d');

            // Para esta demo, generamos valores aleatorios en un rango realista
            // En producción, estos datos vendrían de una tabla de analytics
            $results[$dateStr] = rand(0, 10);
        }

        return $results;
    }

    /**
     * Obtiene los clics por día en un período
     */
    private function getClicksByDay($profileId, $period = 'month')
    {
        // Similar a getViewsByDay pero para clics
        $days = $this->getPeriodDays($period);

        $results = [];
        $startDate = new DateTime();
        $startDate->modify("-{$days} days");

        for ($i = 0; $i < $days; $i++) {
            $date = clone $startDate;
            $date->modify("+{$i} days");
            $dateStr = $date->format('Y-m-d');

            // Valores aleatorios, pero menores que las vistas
            $results[$dateStr] = rand(0, 5);
        }

        return $results;
    }

    /**
     * Calcula la tasa de conversión diaria (clics/vistas)
     */
    private function getConversionRateByDay($profileId, $period = 'month')
    {
        $viewsByDay = $this->getViewsByDay($profileId, $period);
        $clicksByDay = $this->getClicksByDay($profileId, $period);

        $conversionRate = [];

        foreach ($viewsByDay as $date => $views) {
            $clicks = $clicksByDay[$date] ?? 0;
            $conversionRate[$date] = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
        }

        return $conversionRate;
    }

    /**
     * Obtiene el número de días según el período
     */
    private function getPeriodDays($period)
    {
        switch ($period) {
            case 'week':
                return 7;
            case 'month':
                return 30;
            case 'year':
                return 365;
            default:
                return 30; // Por defecto, un mes
        }
    }

    /**
     * Muestra el perfil del usuario
     */
    public function showProfile()
    {
        // Verificar que el usuario esté logueado
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder a esta página');
            redirect('/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = User::getById($userId);

        // Obtener perfil si es anunciante
        $profile = null;
        if ($user['user_type'] === 'advertiser') {
            $profile = Profile::getByUserId($userId);
        }

        // Si no tiene perfil, redirigir a creación
        if ($user['user_type'] === 'advertiser' && !$profile) {
            setFlashMessage('warning', 'Primero debe crear su perfil');
            redirect('/usuario/editar');
            exit;
        }

        // Obtener medios (fotos y videos)
        $photos = [];
        $videos = [];
        if ($profile) {
            $photos = Media::getPhotosByProfileId($profile['id']);
            $videos = Media::getVideosByProfileId($profile['id']);
        }

        // Obtener tarifas
        $rates = [];
        if ($profile) {
            $rates = Rate::getByProfileId($profile['id']);
        }

        $pageTitle = 'Mi Perfil';
        $pageHeader = 'Información de Perfil';

        // Renderizar vista
        $viewFile = __DIR__ . '/../views/profile/view.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
