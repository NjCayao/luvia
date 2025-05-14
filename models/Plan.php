<?php
// models/Plan.php

require_once __DIR__ . '/../config/database.php';

class Plan {
    /**
     * Obtiene todos los planes
     */
    public static function getAll() {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM plans ORDER BY price ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene planes por tipo de usuario
     */
    public static function getByUserType($userType) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM plans WHERE user_type = ? ORDER BY price ASC");
        $stmt->execute([$userType]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un plan por ID
     */
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Crea un nuevo plan
     */
    public static function create($planData) {
        $conn = getDbConnection();
        
        $sql = "INSERT INTO plans (name, user_type, duration, price, max_photos, max_videos, featured, description)
                VALUES (:name, :user_type, :duration, :price, :max_photos, :max_videos, :featured, :description)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $planData['name']);
        $stmt->bindParam(':user_type', $planData['user_type']);
        $stmt->bindParam(':duration', $planData['duration']);
        $stmt->bindParam(':price', $planData['price']);
        $stmt->bindParam(':max_photos', $planData['max_photos']);
        $stmt->bindParam(':max_videos', $planData['max_videos']);
        $stmt->bindParam(':featured', $planData['featured'], PDO::PARAM_BOOL);
        $stmt->bindParam(':description', $planData['description']);
        
        $stmt->execute();
        
        return $conn->lastInsertId();
    }
    
    /**
     * Actualiza un plan existente
     */
    public static function update($id, $data) {
        $conn = getDbConnection();
        
        $setFields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $setFields[] = "$field = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE plans SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Elimina un plan
     */
    public static function delete($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("DELETE FROM plans WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtiene el plan de visitante básico
     */
    public static function getBasicVisitorPlan() {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM plans WHERE user_type = 'visitor' ORDER BY price ASC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtiene el plan por duración y tipo de usuario
     */
    public static function getByDurationAndType($duration, $userType) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM plans WHERE duration = ? AND user_type = ?");
        $stmt->execute([$duration, $userType]);
        return $stmt->fetch();
    }
    
    /**
     * Verifica si hay usuarios usando un plan
     */
    public static function hasUsers($planId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM subscriptions WHERE plan_id = ?");
        $stmt->execute([$planId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}