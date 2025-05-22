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

        // Asegurarse de que solo se usen campos que existen en la tabla
        $allowedFields = [
            'user_id',
            'name',
            'gender',
            'description',
            'whatsapp',
            'province_id',
            'district_id',
            'location',
            'schedule',
            'is_verified'
        ];

        $insertData = [];
        $placeholders = [];
        $values = [];

        foreach ($profileData as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $insertData[] = $field;
                $placeholders[] = "?";
                $values[] = $value;
            }
        }

        if (empty($insertData)) {
            throw new Exception("No hay datos válidos para insertar");
        }

        $sql = "INSERT INTO profiles (" . implode(', ', $insertData) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $conn->prepare($sql);
        $stmt->execute($values);

        return $conn->lastInsertId();
    }


    /**
     * Actualiza un perfil
     */
    public static function update($id, $data)
    {
        $conn = getDbConnection();

        // Construir consulta SQL
        $setParts = [];
        $values = [];

        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }

        $setParts[] = "updated_at = NOW()";
        $values[] = $id; // Para la condición WHERE

        $query = "UPDATE profiles SET " . implode(', ', $setParts) . " WHERE id = ?";

        try {
            $stmt = $conn->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log('Error al actualizar perfil: ' . $e->getMessage());
            return false;
        }
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
     * Obtiene las provincias disponibles
     */
    public static function getAvailableProvinces()
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM provinces ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los distritos disponibles para una provincia
     */
    public static function getDistrictsByProvinceId($provinceId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM districts WHERE province_id = ? ORDER BY name");
        $stmt->execute([$provinceId]);
        return $stmt->fetchAll();
    }
    /**
     * Obtiene el nombre de una provincia por su ID
     */
    public static function getProvinceNameById($provinceId)
    {
        if (empty($provinceId)) {
            return '';
        }

        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT name FROM provinces WHERE id = ?");
        $stmt->execute([$provinceId]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : '';
    }

    /**
     * Obtiene el nombre de un distrito por su ID
     */
    public static function getDistrictNameById($districtId)
    {
        if (empty($districtId)) {
            return '';
        }

        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT name FROM districts WHERE id = ?");
        $stmt->execute([$districtId]);
        $result = $stmt->fetch();
        return $result ? $result['name'] : '';
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
    public static function search($query = '', $provinceId = null, $districtId = null, $gender = null, $limit = 20, $offset = 0)
    {
        $conn = getDbConnection();

        // Construir la consulta paso a paso
        $sql = "SELECT p.*, u.id as user_id, u.status as user_status, 
                   m.filename as main_photo,
                   prov.name as province_name,
                   dist.name as district_name
            FROM profiles p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN provinces prov ON p.province_id = prov.id
            LEFT JOIN districts dist ON p.district_id = dist.id
            LEFT JOIN media m ON m.profile_id = p.id AND m.media_type = 'photo' AND m.is_primary = TRUE
            LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
            WHERE (u.status = 'active' OR s.id IS NOT NULL)";

        $params = [];

        // Añadir condiciones una por una
        if (!empty($query)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }

        if (!empty($provinceId) && is_numeric($provinceId)) {
            $sql .= " AND p.province_id = ?";
            $params[] = (int)$provinceId;
        }

        if (!empty($districtId) && is_numeric($districtId)) {
            $sql .= " AND p.district_id = ?";
            $params[] = (int)$districtId;
        }

        if (!empty($gender) && in_array($gender, ['female', 'male', 'trans'])) {
            $sql .= " AND p.gender = ?";
            $params[] = $gender;
        }

        // Orden y límites
        $sql .= " ORDER BY p.is_verified DESC, p.id DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        // Debug completo
        error_log("=== DEBUG SEARCH ===");
        error_log("Query: " . $query);
        error_log("ProvinceId: " . $provinceId);
        error_log("DistrictId: " . $districtId);
        error_log("Gender: " . $gender);
        error_log("SQL: " . $sql);
        error_log("Params count: " . count($params));
        error_log("Params: " . json_encode($params));

        try {
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);

            if (!$result) {
                error_log("Execute failed: " . json_encode($stmt->errorInfo()));
                return [];
            }

            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Exception in search: " . $e->getMessage());
            error_log("SQL was: " . $sql);
            error_log("Params were: " . json_encode($params));
            return [];
        }
    }

    /**
     * Busca perfiles por provincia y distrito
     */
    public static function searchByLocation($provinceId, $districtId = null, $gender = null, $limit = 20, $offset = 0)
    {
        $conn = getDbConnection();

        $params = [$provinceId]; // El primer parámetro siempre es province_id
        $whereConditions = ['p.province_id = ?']; // Cambiado "p.province" a "p.province_id"

        // Si se proporciona un distrito, añadirlo a la condición
        if ($districtId !== null && !empty($districtId)) {
            $whereConditions[] = 'p.district_id = ?'; // Cambiado "p.district" a "p.district_id"
            $params[] = $districtId;
        }

        // Si se proporciona un género, añadirlo a la condición
        if ($gender !== null) {
            $whereConditions[] = 'p.gender = ?';
            $params[] = $gender;
        }

        // Añadir límite y offset a los parámetros
        $params[] = $limit;
        $params[] = $offset;

        // Construir la cláusula WHERE
        $whereClause = implode(' AND ', $whereConditions);

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
            WHERE $whereClause AND (u.status = 'active' OR s.id IS NOT NULL)
            ORDER BY p.is_verified DESC, s.id IS NOT NULL DESC, p.id DESC
            LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Cuenta resultados de búsqueda
     */
    public static function countSearch($query = '', $provinceId = null, $districtId = null, $gender = null)
    {
        $conn = getDbConnection();

        $sql = "SELECT COUNT(*) as total 
            FROM profiles p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
            WHERE (u.status = 'active' OR s.id IS NOT NULL)";

        $params = [];

        if (!empty($query)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }

        if (!empty($provinceId) && is_numeric($provinceId)) {
            $sql .= " AND p.province_id = ?";
            $params[] = (int)$provinceId;
        }

        if (!empty($districtId) && is_numeric($districtId)) {
            $sql .= " AND p.district_id = ?";
            $params[] = (int)$districtId;
        }

        if (!empty($gender) && in_array($gender, ['female', 'male', 'trans'])) {
            $sql .= " AND p.gender = ?";
            $params[] = $gender;
        }

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Exception in countSearch: " . $e->getMessage());
            return 0;
        }
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

    /**
     * Obtiene todos los perfiles con filtros opcionales
     */
    public static function getAll($limit = 100, $offset = 0, $filters = [])
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
            ) m ON m.profile_id = p.id";

        $params = [];
        $whereConditions = [];

        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['gender']) && !empty($filters['gender'])) {
                $whereConditions[] = "p.gender = ?";
                $params[] = $filters['gender'];
            }

            if (isset($filters['city']) && !empty($filters['city'])) {
                $whereConditions[] = "p.city = ?";
                $params[] = $filters['city'];
            }

            if (isset($filters['is_verified'])) {
                $whereConditions[] = "p.is_verified = ?";
                $params[] = $filters['is_verified'] ? 1 : 0;
            }

            if (isset($filters['search']) && !empty($filters['search'])) {
                $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
        }

        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        // Añadir orden y límites
        $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
