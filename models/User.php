<?php
// models/User.php

require_once __DIR__ . '/../config/database.php';

class User {
    // Obtener usuario por ID
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Obtener usuario por teléfono
    public static function getByPhone($phone) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }
    
    // Obtener usuario por email
    public static function getByEmail($email) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    // Crear nuevo usuario
    public static function create($userData) {
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
    public static function verifyPhone($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE users SET phone_verified = TRUE, status = CASE WHEN email_verified = TRUE THEN 'active' ELSE status END WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    // Verificar email
    public static function verifyEmail($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE users SET email_verified = TRUE, status = CASE WHEN phone_verified = TRUE THEN 'active' ELSE status END WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    // Actualizar último login
    public static function updateLastLogin($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    // Verificar código
    public static function verifyCode($userId, $code) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND verification_code = ? AND verification_expires > NOW()");
        $stmt->execute([$userId, $code]);
        return $stmt->fetch() ? true : false;
    }
    
    // Actualizar usuario
    public static function update($userId, $data) {
        $conn = getDbConnection();
        
        $setFields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $setFields[] = "$field = ?";
            $params[] = $value;
        }
        
        $params[] = $userId;
        
        $sql = "UPDATE users SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    // Listar usuarios (para admin)
    public static function getAll($limit = 100, $offset = 0, $filters = []) {
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
    
    // Contar usuarios
    public static function count($filters = []) {
        $conn = getDbConnection();
        
        $sql = "SELECT COUNT(*) as total FROM users";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['user_type'])) {
                $whereConditions[] = "user_type = ?";
                $params[] = $filters['user_type'];
            }
            
            if (isset($filters['status'])) {
                $whereConditions[] = "status = ?";
                $params[] = $filters['status'];
            }
            
            if (isset($filters['search'])) {
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
        
        return (int)$result['total'];
    }
    
}