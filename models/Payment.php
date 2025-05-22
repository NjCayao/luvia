<?php
// models/Payment.php

require_once __DIR__ . '/../config/database.php';

class Payment {
    /**
     * Obtiene todos los pagos
     */
    public static function getAll($limit = 100, $offset = 0, $filters = []) {
        $conn = getDbConnection();
        
        $sql = "SELECT p.*, u.email as user_email, u.phone as user_phone, pl.name as plan_name 
                FROM payments p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN plans pl ON p.plan_id = pl.id";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['payment_status'])) {
                $whereConditions[] = "p.payment_status = ?";
                $params[] = $filters['payment_status'];
            }
            
            if (isset($filters['payment_method'])) {
                $whereConditions[] = "p.payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            if (isset($filters['search'])) {
                $whereConditions[] = "(p.transaction_id LIKE ? OR p.order_id LIKE ? OR u.email LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
            
            if (isset($filters['start_date'])) {
                $whereConditions[] = "p.created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "p.created_at <= ?";
                $params[] = $filters['end_date'];
            }
        }
        
        // Construir condición WHERE
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
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
     * Obtiene un pago por order_id
     */
    public static function getByOrderId($orderId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene pagos de un usuario
     */
    public static function getByUserId($userId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("
            SELECT p.*, pl.name as plan_name 
            FROM payments p
            LEFT JOIN plans pl ON p.plan_id = pl.id
            WHERE p.user_id = ? 
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crea un nuevo pago - MEJORADO
     */
    public static function create($paymentData) {
        $conn = getDbConnection();
        
        $sql = "INSERT INTO payments (user_id, plan_id, amount, currency, payment_method, payment_status, order_id, created_at) 
                VALUES (:user_id, :plan_id, :amount, :currency, :payment_method, :payment_status, :order_id, NOW())";
        
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
        
        // Agregar updated_at automáticamente
        $setFields[] = "updated_at = NOW()";
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
        
        $sql = "SELECT COUNT(*) as total FROM payments p LEFT JOIN users u ON p.user_id = u.id";
        $params = [];
        $whereConditions = [];
        
        // Aplicar filtros
        if (!empty($filters)) {
            if (isset($filters['payment_status'])) {
                $whereConditions[] = "p.payment_status = ?";
                $params[] = $filters['payment_status'];
            }
            
            if (isset($filters['payment_method'])) {
                $whereConditions[] = "p.payment_method = ?";
                $params[] = $filters['payment_method'];
            }
            
            if (isset($filters['search'])) {
                $whereConditions[] = "(p.transaction_id LIKE ? OR p.order_id LIKE ? OR u.email LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }
            
            if (isset($filters['start_date'])) {
                $whereConditions[] = "p.created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "p.created_at <= ?";
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
     * Obtiene estadísticas de pagos
     */
    public static function getStats($filters = []) {
        $conn = getDbConnection();
        
        $whereConditions = [];
        $params = [];
        
        // Aplicar filtros de fecha si existen
        if (!empty($filters)) {
            if (isset($filters['start_date'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['end_date'];
            }
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Consulta para obtener estadísticas
        $sql = "SELECT 
                    SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount,
                    COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as completed_count,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN payment_status = 'processing' THEN 1 END) as processing_count,
                    COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_count,
                    COUNT(*) as total_count
                FROM payments $whereClause";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return [
            'total_amount' => floatval($result['total_amount'] ?? 0),
            'completed_count' => intval($result['completed_count'] ?? 0),
            'pending_count' => intval($result['pending_count'] ?? 0),
            'processing_count' => intval($result['processing_count'] ?? 0),
            'failed_count' => intval($result['failed_count'] ?? 0),
            'total_count' => intval($result['total_count'] ?? 0)
        ];
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