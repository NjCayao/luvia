<?php
// models/Subscription.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Plan.php';

class Subscription {
    /**
     * Obtiene todas las suscripciones
     */
    public static function getAll($limit = 100, $offset = 0) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT s.*, u.phone, u.email, u.user_type, p.name as plan_name
            FROM subscriptions s
            JOIN users u ON s.user_id = u.id
            JOIN plans p ON s.plan_id = p.id
            ORDER BY s.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene suscripciones por usuario
     */
    public static function getByUserId($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT s.*, p.name as plan_name, p.price, p.duration
            FROM subscriptions s
            JOIN plans p ON s.plan_id = p.id
            WHERE s.user_id = ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene la suscripción activa de un usuario
     */
    public static function getActiveByUserId($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT s.*, p.name as plan_name, p.price, p.duration, p.max_photos, p.max_videos
            FROM subscriptions s
            JOIN plans p ON s.plan_id = p.id
            WHERE s.user_id = ? AND s.status = 'active' AND s.end_date > NOW()
            ORDER BY s.end_date DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene una suscripción por ID
     */
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT s.*, p.name as plan_name, p.price, p.duration
            FROM subscriptions s
            JOIN plans p ON s.plan_id = p.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Crea una nueva suscripción
     */
    public static function create($subscriptionData) {
        $conn = getDbConnection();
        
        $sql = "INSERT INTO subscriptions (user_id, plan_id, payment_id, status, start_date, end_date, auto_renew)
                VALUES (:user_id, :plan_id, :payment_id, :status, :start_date, :end_date, :auto_renew)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $subscriptionData['user_id']);
        $stmt->bindParam(':plan_id', $subscriptionData['plan_id']);
        $stmt->bindParam(':payment_id', $subscriptionData['payment_id']);
        $stmt->bindParam(':status', $subscriptionData['status']);
        $stmt->bindParam(':start_date', $subscriptionData['start_date']);
        $stmt->bindParam(':end_date', $subscriptionData['end_date']);
        $stmt->bindParam(':auto_renew', $subscriptionData['auto_renew'], PDO::PARAM_BOOL);
        
        $stmt->execute();
        
        return $conn->lastInsertId();
    }
    
    /**
     * Actualiza una suscripción
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
        
        $sql = "UPDATE subscriptions SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Cancela una suscripción
     */
    public static function cancel($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("UPDATE subscriptions SET status = 'cancelled', auto_renew = FALSE WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Verifica suscripciones expiradas
     */
    public static function checkExpired() {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            UPDATE subscriptions 
            SET status = 'expired' 
            WHERE status = 'active' AND end_date < NOW()
        ");
        return $stmt->execute();
    }
    
    /**
     * Obtiene suscripciones a punto de expirar (próximos 3 días)
     */
    public static function getAboutToExpire() {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT s.*, u.phone, u.email, u.user_type, p.name as plan_name 
            FROM subscriptions s
            JOIN users u ON s.user_id = u.id
            JOIN plans p ON s.plan_id = p.id
            WHERE s.status = 'active' 
            AND s.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)
            AND s.auto_renew = FALSE
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crea un período de prueba para un anunciante
     */
    public static function createTrial($userId) {
        // Verificar que sea un anunciante
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || $user['user_type'] !== 'advertiser') {
            return false;
        }
        
        // Verificar que no tenga ya una suscripción activa
        $activeSub = self::getActiveByUserId($userId);
        if ($activeSub) {
            return false;
        }
        
        // Crear período de prueba
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime('+' . FREE_TRIAL_DAYS . ' days'));
        
        // Usar el plan básico para establecer los límites
        $basicPlan = Plan::getByDurationAndType(30, 'advertiser'); // Plan mensual básico
        $planId = $basicPlan ? $basicPlan['id'] : null;
        
        if (!$planId) {
            // Si no hay plan básico, crear uno temporal (solo para el período de prueba)
            $planId = Plan::create([
                'name' => 'Prueba Gratuita',
                'user_type' => 'advertiser',
                'duration' => FREE_TRIAL_DAYS,
                'price' => 0,
                'max_photos' => 2,
                'max_videos' => 2,
                'featured' => false,
                'description' => 'Período de prueba gratuito'
            ]);
        }
        
        // Crear suscripción de prueba
        return self::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'payment_id' => null,
            'status' => 'trial',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'auto_renew' => false
        ]);
    }
    
    /**
     * Verificar el estado del período de prueba de un usuario
     */
    public static function checkTrialStatus($userId) {
        $conn = getDbConnection();
        
        // Obtener suscripción de prueba
        $stmt = $conn->prepare("
            SELECT * FROM subscriptions 
            WHERE user_id = ? AND status = 'trial'
            ORDER BY end_date DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        $trial = $stmt->fetch();
        
        if (!$trial) {
            return [
                'has_trial' => false,
                'days_left' => 0,
                'trial_ended' => false
            ];
        }
        
        // Verificar si ha expirado
        $now = new DateTime();
        $endDate = new DateTime($trial['end_date']);
        $interval = $now->diff($endDate);
        
        $daysLeft = $interval->invert ? 0 : $interval->days;
        $trialEnded = $interval->invert > 0;
        
        // Actualizar estado si ha expirado
        if ($trialEnded) {
            self::update($trial['id'], ['status' => 'expired']);
        }
        
        return [
            'has_trial' => true,
            'days_left' => $daysLeft,
            'trial_ended' => $trialEnded,
            'trial' => $trial
        ];
    }
}