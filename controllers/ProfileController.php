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
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/profile/dashboard.php';
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

        // Obtener la lista de ciudades disponibles
        $cities = Profile::getAvailableCities();

        $pageTitle = $profile ? 'Editar Perfil' : 'Crear Perfil';
        $pageHeader = $pageTitle;

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/profile/edit.php';
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
        $city = $_POST['city'] ?? '';
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

        // Verificar si ya existe un perfil para este usuario
        $existingProfile = Profile::getByUserId($userId);

        try {
            if ($existingProfile) {
                // Actualizar perfil existente
                Profile::update($existingProfile['id'], [
                    'name' => $name,
                    'description' => $description,
                    'whatsapp' => $whatsapp,
                    'city' => $city,
                    'location' => $location,
                    'schedule' => $schedule
                ]);

                $profileId = $existingProfile['id'];
                $message = 'Perfil actualizado correctamente';
            } else {
                // Obtener el género del usuario
                $user = User::getById($userId);
                $gender = $user['gender'] ?? 'female';

                // Crear nuevo perfil
                $profileId = Profile::create([
                    'user_id' => $userId,
                    'name' => $name,
                    'gender' => $gender,
                    'description' => $description,
                    'whatsapp' => $whatsapp,
                    'city' => $city,
                    'location' => $location,
                    'schedule' => $schedule,
                    'is_verified' => false
                ]);

                $message = 'Perfil creado correctamente';
            }

            // Procesar tarifas si se enviaron
            if (isset($_POST['rates'])) {
                $rates = json_decode($_POST['rates'], true);

                if (is_array($rates)) {
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

        // Obtener límites según suscripción
        $maxPhotos = 2; // Límite por defecto
        $maxVideos = 2; // Límite por defecto

        // Verificar si tiene suscripción activa y ajustar límites
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);
            $plan = $planModel->getById($subscription['plan_id']);

            if ($plan) {
                $maxPhotos = $plan['max_photos'] ?? $maxPhotos;
                $maxVideos = $plan['max_videos'] ?? $maxVideos;
            }
        }

        $pageTitle = 'Gestionar Fotos y Videos';
        $pageHeader = 'Mis Fotos y Videos';

        // Renderizar vista
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/profile/media.php';
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

        // Obtener límite según suscripción
        $maxPhotos = 2; // Límite por defecto

        // Verificar si tiene suscripción activa y ajustar límites
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);
            $plan = $planModel->getById($subscription['plan_id']);

            if ($plan) {
                $maxPhotos = $plan['max_photos'] ?? $maxPhotos;
            }
        }

        if ($photoCount >= $maxPhotos) {
            http_response_code(400);
            echo json_encode(['error' => "Ha alcanzado el límite de $maxPhotos fotos. Actualice su plan para subir más."]);
            exit;
        }

        // Verificar que se haya subido un archivo
        if (!isset($_FILES['photo']) || empty($_FILES['photo']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No se ha seleccionado ninguna foto']);
            exit;
        }

        // Validar el archivo
        $photoError = validateFile($_FILES['photo'], MAX_PHOTO_SIZE, ALLOWED_PHOTO_TYPES, 'La foto');

        if ($photoError) {
            http_response_code(422);
            echo json_encode(['error' => $photoError]);
            exit;
        }

        try {
            // Generar nombre único para el archivo
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('photo_') . '.' . $extension;

            // Directorio para fotos
            $uploadPath = UPLOAD_DIR . 'photos/';

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

        // Obtener límite según suscripción
        $maxVideos = 2; // Límite por defecto

        // Verificar si tiene suscripción activa y ajustar límites
        $subscription = Subscription::getActiveByUserId($userId);

        if ($subscription) {
            // Obtener plan
            $plan = Plan::getById($subscription['plan_id']);

            if ($plan) {
                $maxVideos = $plan['max_videos'] ?? $maxVideos;
            }
        }

        if ($videoCount >= $maxVideos) {
            http_response_code(400);
            echo json_encode(['error' => "Ha alcanzado el límite de $maxVideos videos. Actualice su plan para subir más."]);
            exit;
        }

        // Verificar que se haya subido un archivo
        if (!isset($_FILES['video']) || empty($_FILES['video']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No se ha seleccionado ningún video']);
            exit;
        }

        // Validar el archivo
        $videoError = validateFile($_FILES['video'], MAX_VIDEO_SIZE, ALLOWED_VIDEO_TYPES, 'El video');

        if ($videoError) {
            http_response_code(422);
            echo json_encode(['error' => $videoError]);
            exit;
        }

        try {
            // Generar nombre único para el archivo
            $extension = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('video_') . '.' . $extension;

            // Directorio para videos
            $uploadPath = UPLOAD_DIR . 'videos/';

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
        require_once __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/profile/rates.php';
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
}
