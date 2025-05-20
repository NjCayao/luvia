<?php
// models/User.php

require_once __DIR__ . '/../config/database.php';

class User
{
    // Obtener usuario por ID
    public static function getById($id)
    {
        try {
            $conn = getDbConnection();

            // Para debugging
            error_log("Buscando usuario con ID: $id");

            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Registrar el resultado para debugging
            error_log("Usuario encontrado: " . ($user ? 'Sí' : 'No'));
            if ($user) {
                error_log("Datos del usuario: " . print_r($user, true));
            }

            return $user;
        } catch (PDOException $e) {
            error_log("Error en User::getById: " . $e->getMessage());
            return false;
        }
    }

    // Obtener usuario por teléfono
    public static function getByPhone($phone)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }

    // Obtener usuario por email
    public static function getByEmail($email)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Crear nuevo usuario
    public static function create($userData)
    {
        $conn = getDbConnection();

        // Encriptar contraseña
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Generar código de verificación
        $verificationCode = rand(100000, 999999);
        $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $sql = "INSERT INTO users (phone, email, password, user_type, status, verification_code, verification_expires) 
                VALUES (:phone, :email, :password, :user_type, 'pending', :verification_code, :verification_expires)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':phone', $userData['phone']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':password', $userData['password']);
        $stmt->bindParam(':user_type', $userData['user_type']);
        $stmt->bindParam(':verification_code', $verificationCode);
        $stmt->bindParam(':verification_expires', $expiryTime);

        $stmt->execute();

        return [
            'id' => $conn->lastInsertId(),
            'verification_code' => $verificationCode
        ];
    }

    // Verificar teléfono
    public static function verifyPhone($userId)
    {
        $conn = getDbConnection();
        // Actualizar verificación de teléfono y estado de la cuenta a activo
        $stmt = $conn->prepare("UPDATE users SET phone_verified = TRUE, status = 'active' WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Verificar email
    public static function verifyEmail($userId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE users SET email_verified = TRUE, status = CASE WHEN phone_verified = TRUE THEN 'active' ELSE status END WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Actualizar último login
    public static function updateLastLogin($userId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Verificar código
    public static function verifyCode($userId, $code)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND verification_code = ? AND verification_expires > NOW()");
        $stmt->execute([$userId, $code]);
        return $stmt->fetch() ? true : false;
    }

    // Actualizar usuario
    public static function update($id, $data)
    {
        try {
            $conn = getDbConnection();
            error_log("User::update - Inicio para ID: $id");
            error_log("Datos a actualizar: " . print_r($data, true));

            $setClause = '';
            $params = [];

            foreach ($data as $key => $value) {
                if (!empty($setClause)) {
                    $setClause .= ', ';
                }
                $setClause .= "$key = :$key";
                $params[":$key"] = $value;
            }

            $params[':id'] = $id;

            $sql = "UPDATE users SET $setClause WHERE id = :id";
            error_log("SQL: $sql");
            error_log("Parámetros: " . print_r($params, true));

            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);

            error_log("Resultado de execute: " . ($result ? 'true' : 'false'));
            error_log("Filas afectadas: " . $stmt->rowCount());

            return $result;
        } catch (PDOException $e) {
            error_log("Error en User::update: " . $e->getMessage());
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        }
    }

    // Listar usuarios (para admin)
    public static function getAll($limit = 100, $offset = 0, $filters = [])
    {
        $conn = getDbConnection();

        $sql = "SELECT * FROM users";
        $params = [];

        // Aplicar filtros
        if (!empty($filters)) {
            $whereConditions = [];

            if (isset($filters['user_type'])) {
                $whereConditions[] = "user_type = ?";
                $params[] = $filters['user_type'];
            }

            if (isset($filters['status'])) {
                $whereConditions[] = "status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(' AND ', $whereConditions);
            }
        }

        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Contar usuarios - VERSIÓN CORREGIDA
    public static function count($filters = [])
    {
        $conn = getDbConnection();

        $sql = "SELECT COUNT(*) as total FROM users";
        $params = [];
        $whereConditions = [];

        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['user_type']) && !empty($filters['user_type'])) {
                $whereConditions[] = "user_type = ?";
                $params[] = $filters['user_type'];
            }

            if (isset($filters['status']) && !empty($filters['status'])) {
                $whereConditions[] = "status = ?";
                $params[] = $filters['status'];
            }

            if (isset($filters['search']) && !empty($filters['search'])) {
                $whereConditions[] = "(email LIKE ? OR phone LIKE ?)";
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

        return (int)($result['total'] ?? 0);
    }

    // Método adicional para contar usuarios nuevos
    public static function countNewUsers($startDate, $endDate)
    {
        $conn = getDbConnection();
        $sql = "SELECT COUNT(*) as total FROM users WHERE created_at BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    // Método para obtener el crecimiento de usuarios
    public static function getUserGrowth($period = 'month')
    {
        $conn = getDbConnection();

        $periodDays = 30; // Default para mes
        if ($period === 'week') {
            $periodDays = 7;
        } else if ($period === 'year') {
            $periodDays = 365;
        }

        $currentDate = date('Y-m-d H:i:s');
        $pastDate = date('Y-m-d H:i:s', strtotime("-$periodDays days"));

        $sql = "SELECT COUNT(*) as total FROM users WHERE created_at BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pastDate, $currentDate]);
        $result = $stmt->fetch();

        return (int)($result['total'] ?? 0);
    }
}
