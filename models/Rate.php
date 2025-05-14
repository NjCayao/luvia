<?php
// models/Rate.php

require_once __DIR__ . '/../config/database.php';

class Rate {
    /**
     * Obtiene todas las tarifas de un perfil
     */
    public static function getByProfileId($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM rates WHERE profile_id = ? ORDER BY rate_type ASC");
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene una tarifa por ID
     */
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM rates WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene una tarifa por tipo
     */
    public static function getByType($profileId, $rateType) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM rates WHERE profile_id = ? AND rate_type = ?");
        $stmt->execute([$profileId, $rateType]);
        return $stmt->fetch();
    }
    
    /**
     * Crea una nueva tarifa
     */
    public static function create($rateData) {
        $conn = getDbConnection();
        
        // Verificar si ya existe una tarifa de este tipo
        $existingRate = self::getByType($rateData['profile_id'], $rateData['rate_type']);
        
        // Si existe, actualizar en vez de crear
        if ($existingRate) {
            return self::update($existingRate['id'], [
                'description' => $rateData['description'],
                'price' => $rateData['price']
            ]);
        }
        
        $sql = "INSERT INTO rates (profile_id, rate_type, description, price) 
                VALUES (:profile_id, :rate_type, :description, :price)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':profile_id', $rateData['profile_id']);
        $stmt->bindParam(':rate_type', $rateData['rate_type']);
        $stmt->bindParam(':description', $rateData['description']);
        $stmt->bindParam(':price', $rateData['price']);
        
        $stmt->execute();
        
        return $conn->lastInsertId();
    }
    
    /**
     * Actualiza una tarifa
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
        
        $sql = "UPDATE rates SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Elimina una tarifa
     */
    public static function delete($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("DELETE FROM rates WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Guarda múltiples tarifas para un perfil
     */
    public static function saveMultiple($profileId, $rates) {
        $conn = getDbConnection();
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        try {
            foreach ($rates as $rate) {
                $existingRate = self::getByType($profileId, $rate['rate_type']);
                
                if ($existingRate) {
                    // Actualizar
                    $stmt = $conn->prepare("UPDATE rates SET description = ?, price = ? WHERE id = ?");
                    $stmt->execute([$rate['description'], $rate['price'], $existingRate['id']]);
                } else {
                    // Crear
                    $stmt = $conn->prepare("INSERT INTO rates (profile_id, rate_type, description, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$profileId, $rate['rate_type'], $rate['description'], $rate['price']]);
                }
            }
            
            // Confirmar transacción
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $conn->rollBack();
            return false;
        }
    }
}