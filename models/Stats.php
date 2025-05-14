<?php
// models/Stats.php

require_once __DIR__ . '/../config/database.php';

class Stats {
    /**
     * Obtener estadísticas generales del sistema
     */
    public static function getGeneralStats() {
        $db = getConnection();
        
        $stats = [
            'users' => self::getUserStats(),
            'profiles' => self::getProfileStats(),
            'payments' => self::getPaymentStats(),
            'subscriptions' => self::getSubscriptionStats(),
            'traffic' => self::getTrafficStats()
        ];
        
        return $stats;
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public static function getUserStats() {
        $db = getConnection();
        
        // Total de usuarios
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Usuarios por tipo
        $stmt = $db->prepare("SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
        $stmt->execute();
        $byType = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byType[$row['user_type']] = $row['count'];
        }
        
        // Usuarios por estado
        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM users GROUP BY status");
        $stmt->execute();
        $byStatus = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byStatus[$row['status']] = $row['count'];
        }
        
        // Usuarios nuevos por día (últimos 30 días)
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM users 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $newByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $newByDay[$row['date']] = $row['count'];
        }
        
        return [
            'total' => $total,
            'by_type' => $byType,
            'by_status' => $byStatus,
            'new_by_day' => $newByDay
        ];
    }
    
    /**
     * Obtener estadísticas de perfiles
     */
    public static function getProfileStats() {
        $db = getConnection();
        
        // Total de perfiles
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM profiles");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Perfiles por género
        $stmt = $db->prepare("SELECT gender, COUNT(*) as count FROM profiles GROUP BY gender");
        $stmt->execute();
        $byGender = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byGender[$row['gender']] = $row['count'];
        }
        
        // Perfiles por ciudad
        $stmt = $db->prepare("SELECT city, COUNT(*) as count FROM profiles GROUP BY city ORDER BY count DESC LIMIT 10");
        $stmt->execute();
        $byCity = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byCity[$row['city']] = $row['count'];
        }
        
        // Perfiles más vistos
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.gender, p.city, p.views, p.is_verified, 
                   (SELECT filename FROM media WHERE profile_id = p.id AND is_primary = 1 LIMIT 1) as main_photo
            FROM profiles p
            ORDER BY views DESC
            LIMIT 10
        ");
        $stmt->execute();
        $mostViewed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Perfiles más contactados (clicks en WhatsApp)
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.gender, p.city, p.whatsapp_clicks, p.is_verified, 
                   (SELECT filename FROM media WHERE profile_id = p.id AND is_primary = 1 LIMIT 1) as main_photo
            FROM profiles p
            ORDER BY whatsapp_clicks DESC
            LIMIT 10
        ");
        $stmt->execute();
        $mostContacted = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Porcentaje de perfiles verificados
        $stmt = $db->prepare("SELECT COUNT(*) as verified FROM profiles WHERE is_verified = 1");
        $stmt->execute();
        $verified = $stmt->fetch(PDO::FETCH_ASSOC)['verified'];
        $verifiedPercentage = $total > 0 ? round(($verified / $total) * 100, 2) : 0;
        
        return [
            'total' => $total,
            'by_gender' => $byGender,
            'by_city' => $byCity,
            'most_viewed' => $mostViewed,
            'most_contacted' => $mostContacted,
            'verified' => $verified,
            'verified_percentage' => $verifiedPercentage
        ];
    }
    
    /**
     * Obtener estadísticas de pagos
     */
    public static function getPaymentStats($period = 'month') {
        $db = getConnection();
        
        // Intervalo de fechas según período
        $startDate = '';
        switch ($period) {
            case 'week':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
                break;
            default:
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        // Total recaudado en el período
        $stmt = $db->prepare("
            SELECT SUM(amount) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND created_at >= {$startDate}
        ");
        $stmt->execute();
        $totalAmount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Pagos por estado
        $stmt = $db->prepare("
            SELECT payment_status, COUNT(*) as count 
            FROM payments 
            WHERE created_at >= {$startDate}
            GROUP BY payment_status
        ");
        $stmt->execute();
        $byStatus = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byStatus[$row['payment_status']] = $row['count'];
        }
        
        // Pagos por método
        $stmt = $db->prepare("
            SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND created_at >= {$startDate}
            GROUP BY payment_method
        ");
        $stmt->execute();
        $byMethod = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byMethod[$row['payment_method']] = [
                'count' => $row['count'],
                'total' => $row['total']
            ];
        }
        
        // Ingresos por día
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, SUM(amount) as total 
            FROM payments 
            WHERE payment_status = 'completed' 
            AND created_at >= {$startDate}
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $revenueByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $revenueByDay[$row['date']] = $row['total'];
        }
        
        // Total por tipo de usuario
        $stmt = $db->prepare("
            SELECT p.user_type, COUNT(*) as count, SUM(amount) as total 
            FROM payments p
            INNER JOIN users u ON p.user_id = u.id
            WHERE p.payment_status = 'completed' 
            AND p.created_at >= {$startDate}
            GROUP BY p.user_type
        ");
        $stmt->execute();
        $byUserType = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byUserType[$row['user_type']] = [
                'count' => $row['count'],
                'total' => $row['total']
            ];
        }
        
        return [
            'total_amount' => $totalAmount,
            'by_status' => $byStatus,
            'by_method' => $byMethod,
            'revenue_by_day' => $revenueByDay,
            'by_user_type' => $byUserType
        ];
    }
    
    /**
     * Obtener estadísticas de suscripciones
     */
    public static function getSubscriptionStats() {
        $db = getConnection();
        
        // Total de suscripciones activas
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM subscriptions WHERE status = 'active'");
        $stmt->execute();
        $activeTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Suscripciones por estado
        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM subscriptions GROUP BY status");
        $stmt->execute();
        $byStatus = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byStatus[$row['status']] = $row['count'];
        }
        
        // Suscripciones por plan
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.user_type, COUNT(s.id) as count 
            FROM subscriptions s
            INNER JOIN plans p ON s.plan_id = p.id
            WHERE s.status = 'active'
            GROUP BY p.id
            ORDER BY count DESC
        ");
        $stmt->execute();
        $byPlan = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byPlan[$row['id']] = [
                'name' => $row['name'],
                'user_type' => $row['user_type'],
                'count' => $row['count']
            ];
        }
        
        // Renovación automática
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM subscriptions WHERE auto_renew = 1 AND status = 'active'");
        $stmt->execute();
        $autoRenewCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $autoRenewPercentage = $activeTotal > 0 ? round(($autoRenewCount / $activeTotal) * 100, 2) : 0;
        
        return [
            'active_total' => $activeTotal,
            'by_status' => $byStatus,
            'by_plan' => $byPlan,
            'auto_renew_count' => $autoRenewCount,
            'auto_renew_percentage' => $autoRenewPercentage
        ];
    }
    
    /**
     * Obtener estadísticas de tráfico
     */
    public static function getTrafficStats($period = 'month') {
        $db = getConnection();
        
        // Intervalo de fechas según período
        $startDate = '';
        switch ($period) {
            case 'week':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
                break;
            default:
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        // Visitas totales por día
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM stats 
            WHERE action_type = 'view' 
            AND created_at >= {$startDate}
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $viewsByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $viewsByDay[$row['date']] = $row['count'];
        }
        
        // Clicks en WhatsApp por día
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM stats 
            WHERE action_type = 'whatsapp_click' 
            AND created_at >= {$startDate}
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $clicksByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clicksByDay[$row['date']] = $row['count'];
        }
        
        // Tasa de conversión (clicks / vistas)
        $conversionRate = [];
        foreach (array_keys($viewsByDay) as $date) {
            $views = $viewsByDay[$date] ?? 0;
            $clicks = $clicksByDay[$date] ?? 0;
            
            $conversionRate[$date] = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
        }
        
        return [
            'views_by_day' => $viewsByDay,
            'clicks_by_day' => $clicksByDay,
            'conversion_rate' => $conversionRate
        ];
    }
    
    /**
     * Obtener estadísticas de un perfil específico
     */
    public static function getProfileSpecificStats($profileId, $period = 'month') {
        $db = getConnection();
        
        // Validar perfil
        if (!$profileId || !is_numeric($profileId)) {
            return false;
        }
        
        // Intervalo de fechas según período
        $startDate = '';
        $periodDays = 30;
        switch ($period) {
            case 'week':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                $periodDays = 7;
                break;
            case 'month':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                $periodDays = 30;
                break;
            case 'year':
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
                $periodDays = 365;
                break;
            default:
                $startDate = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                $periodDays = 30;
        }
        
        // Información básica del perfil
        $stmt = $db->prepare("
            SELECT p.*, u.created_at as user_created_at,
                  (SELECT COUNT(*) FROM media WHERE profile_id = p.id AND media_type = 'photo') as photo_count,
                  (SELECT COUNT(*) FROM media WHERE profile_id = p.id AND media_type = 'video') as video_count
            FROM profiles p
            INNER JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$profileId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$profile) {
            return false;
        }
        
        // Visitas totales
        $totalViews = $profile['views'] ?? 0;
        
        // Clicks en WhatsApp totales
        $totalClicks = $profile['whatsapp_clicks'] ?? 0;
        
        // Tasa de conversión total
        $totalConversionRate = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;
        
        // Visitas por día
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM stats 
            WHERE action_type = 'view' AND profile_id = ?
            AND created_at >= {$startDate}
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$profileId]);
        $viewsByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $viewsByDay[$row['date']] = $row['count'];
        }
        
        // Clicks en WhatsApp por día
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM stats 
            WHERE action_type = 'whatsapp_click' AND profile_id = ?
            AND created_at >= {$startDate}
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$profileId]);
        $clicksByDay = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clicksByDay[$row['date']] = $row['count'];
        }
        
        // Tasa de conversión diaria (clicks / vistas)
        $conversionRate = [];
        foreach (array_keys($viewsByDay) as $date) {
            $views = $viewsByDay[$date] ?? 0;
            $clicks = $clicksByDay[$date] ?? 0;
            
            $conversionRate[$date] = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
        }
        
        // Promedio diario de visitas y clicks
        $periodViewsSum = array_sum($viewsByDay);
        $periodClicksSum = array_sum($clicksByDay);
        
        $avgDailyViews = $periodDays > 0 ? round($periodViewsSum / $periodDays, 2) : 0;
        $avgDailyClicks = $periodDays > 0 ? round($periodClicksSum / $periodDays, 2) : 0;
        
        // Comparativa con promedios generales (top 20%)
        $stmt = $db->prepare("
            SELECT AVG(views) as avg_views, AVG(whatsapp_clicks) as avg_clicks
            FROM profiles
            WHERE is_verified = 1 AND gender = ?
        ");
        $stmt->execute([$profile['gender']]);
        $averages = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $avgCategoryViews = $averages['avg_views'] ?? 0;
        $avgCategoryClicks = $averages['avg_clicks'] ?? 0;
        
        // Posición en el ranking
        $stmt = $db->prepare("
            SELECT COUNT(*) as position
            FROM profiles
            WHERE gender = ? AND views > ?
        ");
        $stmt->execute([$profile['gender'], $totalViews]);
        $rankingPosition = $stmt->fetch(PDO::FETCH_ASSOC)['position'] + 1;
        
        // Contar total de perfiles en la misma categoría
        $stmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM profiles
            WHERE gender = ?
        ");
        $stmt->execute([$profile['gender']]);
        $totalInCategory = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calcular percentil (posición relativa en %)
        $percentile = $totalInCategory > 0 ? 100 - round(($rankingPosition / $totalInCategory) * 100, 2) : 0;
        
        return [
            'profile' => $profile,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'total_conversion_rate' => $totalConversionRate,
            'views_by_day' => $viewsByDay,
            'clicks_by_day' => $clicksByDay,
            'conversion_rate' => $conversionRate,
            'avg_daily_views' => $avgDailyViews,
            'avg_daily_clicks' => $avgDailyClicks,
            'avg_category_views' => $avgCategoryViews,
            'avg_category_clicks' => $avgCategoryClicks,
            'ranking_position' => $rankingPosition,
            'total_in_category' => $totalInCategory,
            'percentile' => $percentile,
            'period' => $period,
            'period_days' => $periodDays
        ];
    }
    
    /**
     * Registrar una nueva vista de perfil
     */
    public static function logProfileView($profileId, $visitorId = null) {
        $db = getConnection();
        
        // Verificar que el perfil exista
        $stmt = $db->prepare("SELECT id FROM profiles WHERE id = ?");
        $stmt->execute([$profileId]);
        if (!$stmt->fetch()) {
            return false;
        }
        
        // Obtener información del visitante
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Insertar estadística
        $stmt = $db->prepare("
            INSERT INTO stats (profile_id, action_type, ip_address, user_agent, visitor_id, created_at)
            VALUES (?, 'view', ?, ?, ?, NOW())
        ");
        $stmt->execute([$profileId, $ipAddress, $userAgent, $visitorId]);
        
        // Incrementar contador en el perfil
        $stmt = $db->prepare("
            UPDATE profiles 
            SET views = views + 1
            WHERE id = ?
        ");
        $stmt->execute([$profileId]);
        
        return true;
    }
    
    /**
     * Registrar un nuevo click en WhatsApp
     */
    public static function logWhatsappClick($profileId, $visitorId = null) {
        $db = getConnection();
        
        // Verificar que el perfil exista
        $stmt = $db->prepare("SELECT id FROM profiles WHERE id = ?");
        $stmt->execute([$profileId]);
        if (!$stmt->fetch()) {
            return false;
        }
        
        // Obtener información del visitante
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Insertar estadística
        $stmt = $db->prepare("
            INSERT INTO stats (profile_id, action_type, ip_address, user_agent, visitor_id, created_at)
            VALUES (?, 'whatsapp_click', ?, ?, ?, NOW())
        ");
        $stmt->execute([$profileId, $ipAddress, $userAgent, $visitorId]);
        
        // Incrementar contador en el perfil
        $stmt = $db->prepare("
            UPDATE profiles 
            SET whatsapp_clicks = whatsapp_clicks + 1
            WHERE id = ?
        ");
        $stmt->execute([$profileId]);
        
        return true;
    }
}