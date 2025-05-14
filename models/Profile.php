<?php
// models/Profile.php

require_once __DIR__ . '/../config/database.php';

class Profile
{
    /**
     * Obtiene un perfil por ID
     */
    public static function getById($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM profiles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene un perfil por ID de usuario
     */
    public static function getByUserId($userId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo perfil
     */
    public static function create($profileData)
    {
        $conn = getDbConnection();

        $sql = "INSERT INTO profiles (user_id, name, gender, description, whatsapp, city, location, schedule, is_verified) 
                VALUES (:user_id, :name, :gender, :description, :whatsapp, :city, :location, :schedule, :is_verified)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $profileData['user_id']);
        $stmt->bindParam(':name', $profileData['name']);
        $stmt->bindParam(':gender', $profileData['gender']);
        $stmt->bindParam(':description', $profileData['description']);
        $stmt->bindParam(':whatsapp', $profileData['whatsapp']);
        $stmt->bindParam(':city', $profileData['city']);
        $stmt->bindParam(':location', $profileData['location']);
        $stmt->bindParam(':schedule', $profileData['schedule']);
        $stmt->bindParam(':is_verified', $profileData['is_verified']);

        $stmt->execute();

        return $conn->lastInsertId();
    }

    /**
     * Actualiza un perfil
     */
    public static function update($id, $data)
    {
        $conn = getDbConnection();

        $setFields = [];
        $params = [];

        foreach ($data as $field => $value) {
            $setFields[] = "$field = ?";
            $params[] = $value;
        }

        $params[] = $id;

        $sql = "UPDATE profiles SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Incrementa el contador de vistas
     */
    public static function incrementViews($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE profiles SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Incrementa el contador de clics en WhatsApp
     */
    public static function incrementWhatsappClicks($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE profiles SET whatsapp_clicks = whatsapp_clicks + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verifica un perfil
     */
    public static function verify($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE profiles SET is_verified = TRUE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Busca perfiles por género
     */
    public static function searchByGender($gender, $limit = 20, $offset = 0)
    {
        $conn = getDbConnection();

        // Joins con otras tablas para verificar suscripciones activas
        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                m.filename as main_photo 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN (
                    SELECT profile_id, filename 
                    FROM media 
                    WHERE media_type = 'photo' AND is_primary = TRUE
                    LIMIT 1
                ) m ON m.profile_id = p.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.gender = ? AND (u.status = 'active' OR s.id IS NOT NULL)
                ORDER BY is_verified DESC, s.id IS NOT NULL DESC, p.id DESC
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$gender, $limit, $offset]);

        return $stmt->fetchAll();
    }

    /**
     * Busca perfiles por ciudad
     */
    public static function searchByCity($city, $gender = null, $limit = 20, $offset = 0)
    {
        $conn = getDbConnection();

        $params = [$city];
        $genderCondition = '';

        if ($gender !== null) {
            $genderCondition = ' AND p.gender = ?';
            $params[] = $gender;
        }

        $params[] = $limit;
        $params[] = $offset;

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                m.filename as main_photo 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN (
                    SELECT profile_id, filename 
                    FROM media 
                    WHERE media_type = 'photo' AND is_primary = TRUE
                    LIMIT 1
                ) m ON m.profile_id = p.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.city = ?$genderCondition AND (u.status = 'active' OR s.id IS NOT NULL)
                ORDER BY is_verified DESC, s.id IS NOT NULL DESC, p.id DESC
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Busca perfiles nuevos (últimos 7 días)
     */
    public static function getNewProfiles($gender = null, $limit = 10, $offset = 0)
    {
        $conn = getDbConnection();

        $params = [];
        $genderCondition = '';

        if ($gender !== null) {
            $genderCondition = ' AND p.gender = ?';
            $params[] = $gender;
        }

        $params[] = $limit;
        $params[] = $offset;

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                m.filename as main_photo 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN (
                    SELECT profile_id, filename 
                    FROM media 
                    WHERE media_type = 'photo' AND is_primary = TRUE
                    LIMIT 1
                ) m ON m.profile_id = p.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)$genderCondition 
                AND (u.status = 'active' OR s.id IS NOT NULL)
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Obtiene perfiles destacados (verificados y con suscripción activa)
     */
    public static function getFeaturedProfiles($gender = null, $limit = 10)
    {
        $conn = getDbConnection();

        $params = [];
        $genderCondition = '';

        if ($gender !== null) {
            $genderCondition = ' AND p.gender = ?';
            $params[] = $gender;
        }

        $params[] = $limit;

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                m.filename as main_photo 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN (
                    SELECT profile_id, filename 
                    FROM media 
                    WHERE media_type = 'photo' AND is_primary = TRUE
                    LIMIT 1
                ) m ON m.profile_id = p.id
                JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.is_verified = TRUE$genderCondition
                ORDER BY RAND()
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Obtiene un perfil completo con medios y tarifas
     */
    public static function getProfileWithDetails($id)
    {
        $conn = getDbConnection();

        // Obtener datos del perfil
        $profile = self::getById($id);

        if (!$profile) {
            return null;
        }

        // Obtener medios
        $stmtMedia = $conn->prepare("
            SELECT * FROM media 
            WHERE profile_id = ? 
            ORDER BY media_type ASC, is_primary DESC, order_num ASC
        ");
        $stmtMedia->execute([$id]);
        $media = $stmtMedia->fetchAll();

        // Obtener tarifas
        $stmtRates = $conn->prepare("
            SELECT * FROM rates 
            WHERE profile_id = ?
            ORDER BY rate_type ASC
        ");
        $stmtRates->execute([$id]);
        $rates = $stmtRates->fetchAll();

        // Combinar todo
        $profile['media'] = $media;
        $profile['rates'] = $rates;

        return $profile;
    }

    /**
     * Verifica si un usuario tiene acceso a ver perfiles completos
     */
    public static function userHasAccessToViewProfiles($userId)
    {
        $conn = getDbConnection();

        // Verificar si el usuario es un anunciante
        $stmtUser = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $user = $stmtUser->fetch();

        if ($user && $user['user_type'] === 'advertiser') {
            return true;
        }

        // Verificar si el usuario tiene una suscripción activa
        $stmtSub = $conn->prepare("
            SELECT id FROM subscriptions 
            WHERE user_id = ? AND status = 'active' AND end_date > NOW()
        ");
        $stmtSub->execute([$userId]);

        return $stmtSub->fetch() ? true : false;
    }

    /**
     * Obtiene las ciudades disponibles
     */
    public static function getAvailableCities()
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT DISTINCT city FROM profiles ORDER BY city");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Cuenta perfiles por género
     */
    public static function countByGender($gender)
    {
        $conn = getDbConnection();

        $sql = "SELECT COUNT(*) as total FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.gender = ? 
                AND (u.status = 'active' OR s.id IS NOT NULL)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$gender]);
        $result = $stmt->fetch();

        return (int)$result['total'];
    }

    /**
     * Cuenta perfiles por ciudad
     */
    public static function countByCity($city, $gender = null)
    {
        $conn = getDbConnection();

        $params = [$city];
        $genderCondition = '';

        if ($gender !== null) {
            $genderCondition = 'AND p.gender = ?';
            $params[] = $gender;
        }

        $sql = "SELECT COUNT(*) as total FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE p.city = ? $genderCondition
                AND (u.status = 'active' OR s.id IS NOT NULL)";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return (int)$result['total'];
    }

    /**
     * Busca perfiles por términos
     */
    public static function search($query, $city = '', $gender = '', $limit = 20, $offset = 0)
    {
        $conn = getDbConnection();

        $params = [];
        $conditions = [];

        // Condición para búsqueda por texto
        if (!empty($query)) {
            $conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }

        // Condición para ciudad
        if (!empty($city)) {
            $conditions[] = "p.city = ?";
            $params[] = $city;
        }

        // Condición para género
        if (!empty($gender)) {
            $conditions[] = "p.gender = ?";
            $params[] = $gender;
        }

        // Construir WHERE
        $where = '';
        if (!empty($conditions)) {
            $where = 'AND ' . implode(' AND ', $conditions);
        }

        // Añadir límite y offset
        $params[] = $limit;
        $params[] = $offset;

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                m.filename as main_photo 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN (
                    SELECT profile_id, filename 
                    FROM media 
                    WHERE media_type = 'photo' AND is_primary = TRUE
                    LIMIT 1
                ) m ON m.profile_id = p.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE (u.status = 'active' OR s.id IS NOT NULL) $where
                ORDER BY p.is_verified DESC, s.id IS NOT NULL DESC, p.id DESC
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Cuenta resultados de búsqueda
     */
    public static function countSearch($query, $city = '', $gender = '')
    {
        $conn = getDbConnection();

        $params = [];
        $conditions = [];

        // Condición para búsqueda por texto
        if (!empty($query)) {
            $conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }

        // Condición para ciudad
        if (!empty($city)) {
            $conditions[] = "p.city = ?";
            $params[] = $city;
        }

        // Condición para género
        if (!empty($gender)) {
            $conditions[] = "p.gender = ?";
            $params[] = $gender;
        }

        // Construir WHERE
        $where = '';
        if (!empty($conditions)) {
            $where = 'AND ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) as total 
                FROM profiles p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
                WHERE (u.status = 'active' OR s.id IS NOT NULL) $where";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return (int)$result['total'];
    }

    /**
     * Cuenta el número de perfiles según filtros
     */
    public static function count($filters = [])
    {
        $conn = getDbConnection();

        $sql = "SELECT COUNT(*) as total FROM profiles";
        $params = [];
        $whereConditions = [];

        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['gender'])) {
                $whereConditions[] = "gender = ?";
                $params[] = $filters['gender'];
            }

            if (isset($filters['city'])) {
                $whereConditions[] = "city = ?";
                $params[] = $filters['city'];
            }

            if (isset($filters['is_verified'])) {
                $whereConditions[] = "is_verified = ?";
                $params[] = $filters['is_verified'] ? 1 : 0;
            }

            if (isset($filters['search'])) {
                $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
        }

        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return (int)$result['total'];
    }

    /**
     * Obtiene los perfiles más vistos
     */
    public static function getMostViewed($limit = 5)
    {
        $conn = getDbConnection();

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
            m.filename as main_photo 
            FROM profiles p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN (
                SELECT profile_id, filename 
                FROM media 
                WHERE media_type = 'photo' AND is_primary = TRUE
                LIMIT 1
            ) m ON m.profile_id = p.id
            WHERE u.status = 'active'
            ORDER BY p.views DESC
            LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$limit]);

        return $stmt->fetchAll();
    }

    /**
     * Obtiene los perfiles más contactados (clicks en WhatsApp)
     */
    public static function getMostContacted($limit = 5)
    {
        $conn = getDbConnection();

        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
            m.filename as main_photo 
            FROM profiles p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN (
                SELECT profile_id, filename 
                FROM media 
                WHERE media_type = 'photo' AND is_primary = TRUE
                LIMIT 1
            ) m ON m.profile_id = p.id
            WHERE u.status = 'active'
            ORDER BY p.whatsapp_clicks DESC
            LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$limit]);

        return $stmt->fetchAll();
    }
}
