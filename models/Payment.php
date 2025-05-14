<?php
// models/Payment.php

require_once __DIR__ . '/../config/database.php';

class Payment {
    /**
     * Obtiene todos los pagos
     */
    public static function getAll($limit = 100, $offset = 0, $filters = []) {
        $conn = getDbConnection();
        
        $sql = "SELECT * FROM payments";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['payment_status'])) {
                $whereConditions[] = "payment_status = ?";
                $params[] = $filters['payment_status'];
            }
            
            if (isset($filters['payment_method'])) {
                $whereConditions[] = "payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            if (isset($filters['search'])) {
                $whereConditions[] = "(transaction_id LIKE ? OR order_id LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
            
            if (isset($filters['start_date'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['end_date'];
            }
        }
        
        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un pago por ID
     */
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene un pago por ID de sesión de Izipay
     */
    public static function getBySessionId($sessionId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM payments WHERE izipay_session_id = ?");
        $stmt->execute([$sessionId]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene pagos de un usuario
     */
    public static function getByUserId($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crea un nuevo pago
     */
    public static function create($paymentData) {
        $conn = getDbConnection();
        
        $sql = "INSERT INTO payments (user_id, plan_id, amount, currency, payment_method, payment_status, order_id) 
                VALUES (:user_id, :plan_id, :amount, :currency, :payment_method, :payment_status, :order_id)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $paymentData['user_id']);
        $stmt->bindParam(':plan_id', $paymentData['plan_id']);
        $stmt->bindParam(':amount', $paymentData['amount']);
        $stmt->bindParam(':currency', $paymentData['currency']);
        $stmt->bindParam(':payment_method', $paymentData['payment_method']);
        $stmt->bindParam(':payment_status', $paymentData['payment_status']);
        $stmt->bindParam(':order_id', $paymentData['order_id']);
        
        $stmt->execute();
        
        return $conn->lastInsertId();
    }
    
    /**
     * Actualiza un pago
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
        
        $sql = "UPDATE payments SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Cuenta los pagos según filtros
     */
    public static function count($filters = []) {
        $conn = getDbConnection();
        
        $sql = "SELECT COUNT(*) as total FROM payments";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['payment_status'])) {
                $whereConditions[] = "payment_status = ?";
                $params[] = $filters['payment_status'];
            }
            
            if (isset($filters['payment_method'])) {
                $whereConditions[] = "payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            if (isset($filters['search'])) {
                $whereConditions[] = "(transaction_id LIKE ? OR order_id LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
            
            if (isset($filters['start_date'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['end_date'];
            }
        }
        
        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }
    
    /**
     * Obtiene el monto total de pagos
     */
    public static function getTotalAmount($filters = []) {
        $conn = getDbConnection();
        
        $sql = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'completed'";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros adicionales
        if (!empty($filters)) {
            if (isset($filters['payment_method'])) {
                $whereConditions[] = "payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            if (isset($filters['search'])) {
                $whereConditions[] = "(transaction_id LIKE ? OR order_id LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
            
            if (isset($filters['start_date'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['end_date'];
            }
        }
        
        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " AND " . implode(' AND ', $whereConditions);
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Obtiene ingresos agrupados por día
     */
    public static function getRevenueByDay($startDate, $endDate) {
        $conn = getDbConnection();
        
        $sql = "SELECT DATE(created_at) as date, SUM(amount) as total 
                FROM payments 
                WHERE payment_status = 'completed' 
                AND created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        
        $results = $stmt->fetchAll();
        $revenueByDay = [];
        
        foreach ($results as $row) {
            $revenueByDay[$row['date']] = floatval($row['total']);
        }
        
        return $revenueByDay;
    }
}