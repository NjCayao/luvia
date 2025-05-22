<?php
// controllers/HomeController.php

require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Media.php';
require_once __DIR__ . '/../models/Rate.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

class HomeController
{
    /**
     * Página de inicio
     */
    public function index()
    {
        // Verificar si el usuario es visitante y tiene suscripción activa
        $hasAccess = false;

        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];

            if ($_SESSION['user_type'] === 'advertiser') {
                $hasAccess = true;
            } else {
                $subscription = Subscription::getActiveByUserId($userId);
                $hasAccess = ($subscription !== false);
            }
        }

        // Obtener perfiles destacados (solo mujeres para la página principal)
        $featuredProfiles = Profile::getFeaturedProfiles('female', 6);

        // Obtener perfiles nuevos
        $newProfiles = Profile::getNewProfiles('female', 8);

        // Obtener ciudades disponibles para el filtro
        $provinces = Profile::getAvailableProvinces();

        $pageTitle = 'Inicio';

        // Definir la ruta al archivo de vista específico
        $viewFile = __DIR__ . '/../views/home/index.php';

        // Renderizar vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra perfiles por categoría (género)
     */
    public function category($params)
    {
        $gender = $params['gender'] ?? 'female';

        // Validar género
        if (!in_array($gender, ['female', 'male', 'trans'])) {
            $gender = 'female';
        }

        // Verificar si el usuario es visitante y tiene suscripción activa
        $hasAccess = false;

        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];

            if ($_SESSION['user_type'] === 'advertiser') {
                $hasAccess = true;
            } else {
                $subscription = Subscription::getActiveByUserId($userId);
                $hasAccess = ($subscription !== false);
            }
        }

        // Obtener parámetros de filtrado y paginación
        $city = $_GET['city'] ?? '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 12; // Perfiles por página
        $offset = ($page - 1) * $limit;

        // Obtener perfiles según filtros
        if (!empty($city)) {
            $profiles = Profile::searchByCity($city, $gender, $limit, $offset);
            $totalProfiles = Profile::countByCity($city, $gender);
        } else {
            $profiles = Profile::searchByGender($gender, $limit, $offset);
            $totalProfiles = Profile::countByGender($gender);
        }

        // Calcular total de páginas
        $totalPages = ceil($totalProfiles / $limit);

        // Obtener ciudades disponibles para el filtro
        $provinces = Profile::getAvailableProvinces();

        // Título y encabezado según género
        $genderNames = [
            'female' => 'Erophia',
            'male' => 'Erophian',
            'trans' => 'Eromix'
        ];

        $pageTitle = $genderNames[$gender];
        $pageHeader = $genderNames[$gender];

        // Renderizar vista
        // Definir la ruta al archivo de vista específico
        $viewFile = __DIR__ . '/../views/home/category.php';
        // Renderizar vista principal (que incluirá el contenido específico)    
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra un perfil individual
     */
    public function viewProfile($params)
    {
        $profileId = $params['id'] ?? 0;

        // Obtener perfil completo
        $profile = Profile::getProfileWithDetails($profileId);

        if (!$profile) {
            setFlashMessage('warning', 'Perfil no encontrado');
            redirect('/');
            exit;
        }

        // Verificar si el usuario tiene acceso a los detalles completos
        $hasAccess = false;
        $isOwner = false;

        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];
            $isOwner = ($profile['user_id'] == $userId);

            if ($isOwner || $_SESSION['user_type'] === 'admin') {
                $hasAccess = true;
            } else if ($_SESSION['user_type'] === 'advertiser') {
                $hasAccess = true; // Los anunciantes pueden ver perfiles completos
            } else {
                // Verificar si tiene suscripción activa
                $subscription = Subscription::getActiveByUserId($userId);
                $hasAccess = ($subscription !== false);
            }
        }

        // Incrementar contador de vistas si no es el propietario
        if (!$isOwner) {
            Profile::incrementViews($profileId);
        }

        // Preparar datos para la vista
        $photos = [];
        $videos = [];
        $mainPhoto = null;

        foreach ($profile['media'] as $media) {
            if ($media['media_type'] === 'photo') {
                $photos[] = $media;
                if ($media['is_primary']) {
                    $mainPhoto = $media;
                }
            } else if ($media['media_type'] === 'video') {
                $videos[] = $media;
            }
        }

        // Si no hay foto principal, usar la primera
        if (!$mainPhoto && !empty($photos)) {
            $mainPhoto = $photos[0];
        }

        // Obtener usuario
        $user = User::getById($profile['user_id']);

        // Título de la página
        $pageTitle = htmlspecialchars($profile['name']);

        // Generar breadcrumb
        $breadcrumb = '
        <li class="breadcrumb-item"><a href="' . url('/') . '">Inicio</a></li>
        <li class="breadcrumb-item"><a href="' . url('/categoria/' . $profile['gender']) . '">' .
            ucfirst($profile['gender'] === 'female' ? 'Mujeres' : ($profile['gender'] === 'male' ? 'Hombres' : 'Trans')) .
            '</a></li>
        <li class="breadcrumb-item active">' . htmlspecialchars($profile['name']) . '</li>
        ';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/home/view_profile.php';

        // Renderizar vista - CAMBIAR ESTA PARTE
        require_once __DIR__ . '/../views/layouts/main.php';
        // NO INCLUIR LA VISTA ACÁ - ya lo hace main.php
    }

    /**
     * Registra un clic en el botón de WhatsApp
     */
    public function trackWhatsappClick()
    {
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        // Obtener ID del perfil
        $profileId = $_POST['profile_id'] ?? 0;

        if (empty($profileId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de perfil no proporcionado']);
            exit;
        }

        // Incrementar contador
        Profile::incrementWhatsappClicks($profileId);

        // Responder éxito
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Muestra la página de búsqueda
     */
    public function search()
    {
        // Obtener parámetros de búsqueda
        $query = $_GET['q'] ?? '';
        $provinceId = $_GET['province'] ?? '';
        $districtId = $_GET['district'] ?? '';
        $gender = $_GET['gender'] ?? '';

        // Para compatibilidad con la vista actual
        $city = $_GET['city'] ?? '';

        // Validar género
        if (!empty($gender) && !in_array($gender, ['female', 'male', 'trans'])) {
            $gender = '';
        }

        // Verificar si hay términos de búsqueda
        if (empty($query) && empty($provinceId) && empty($city) && empty($gender)) {
            redirect('/');
            exit;
        }

        // Paginación
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Realizar búsqueda
        $searchResults = Profile::search($query, $provinceId, $districtId, $gender, $limit, $offset);
        $totalResults = Profile::countSearch($query, $provinceId, $districtId, $gender);

        // Calcular total de páginas
        $totalPages = ceil($totalResults / $limit);

        // Obtener provincias para filtro
        $provinces = Profile::getAvailableProvinces();

        // Debug - agregar esto temporalmente para ver qué datos tienes
        error_log("Search Results Count: " . count($searchResults));
        error_log("Total Results: " . $totalResults);
        error_log("Query: " . $query . ", Gender: " . $gender);

        // Título de la página
        $pageTitle = 'Resultados de búsqueda';
        $pageHeader = 'Resultados de búsqueda';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/home/search.php';

        // Renderizar vista principal (que incluirá el contenido específico)
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra la página "Acerca de"
     */
    public function about()
    {
        $pageTitle = 'Acerca de Nosotros';
        $pageHeader = 'Acerca de Nosotros';

        $viewFile = __DIR__ . '/../views/layouts/main.php';
        require_once __DIR__ . '/../views/home/about.php';
    }

    /**
     * Muestra los términos y condiciones
     */
    public function terms()
    {
        $pageTitle = 'Términos y Condiciones';
        $pageHeader = 'Términos y Condiciones';

        // Define la ruta al archivo de vista específica
        $viewFile = __DIR__ . '/../views/home/terms.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra la política de privacidad
     */
    public function privacy()
    {
        $pageTitle = 'Política de Privacidad';
        $pageHeader = 'Política de Privacidad';

        $viewFile = __DIR__ . '/../views/home/privacy.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Muestra la página de contacto
     */
    public function contact()
    {
        $pageTitle = 'Contacto';
        $pageHeader = 'Contacto';

        $viewFile = __DIR__ . '/../views/home/contact.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Procesa el formulario de contacto
     */
    public function processContact()
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

        // Obtener datos del formulario
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validar datos
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'El nombre es obligatorio';
        }

        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es obligatorio';
        } else {
            $emailError = validateEmail($email);
            if ($emailError) {
                $errors['email'] = $emailError;
            }
        }

        if (empty($subject)) {
            $errors['subject'] = 'El asunto es obligatorio';
        }

        if (empty($message)) {
            $errors['message'] = 'El mensaje es obligatorio';
        }

        // Si hay errores, devolver respuesta JSON
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Enviar correo (implementación básica)
        $to = 'admin@example.com'; // Cambiar por el correo real
        $headers = "From: {$name} <{$email}>\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $emailBody = "
        <html>
        <head>
            <title>Nuevo mensaje de contacto</title>
        </head>
        <body>
            <h2>Nuevo mensaje de contacto</h2>
            <p><strong>Nombre:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Asunto:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Mensaje:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        </body>
        </html>
        ";

        $mailSent = mail($to, "Contacto: " . $subject, $emailBody, $headers);

        if ($mailSent) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado correctamente. Nos pondremos en contacto pronto.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar el mensaje. Por favor, inténtelo más tarde.']);
        }

        exit;
    }
}
