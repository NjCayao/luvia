<?php
// models/Media.php

require_once __DIR__ . '/../config/database.php';

class Media {
    /**
     * Obtiene todos los medios de un perfil
     */
    public static function getByProfileId($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM media WHERE profile_id = ? ORDER BY media_type ASC, is_primary DESC, order_num ASC");
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un medio por ID
     */
    public static function getById($id) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM media WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene la foto principal de un perfil
     */
    public static function getPrimaryPhoto($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM media WHERE profile_id = ? AND media_type = 'photo' AND is_primary = TRUE LIMIT 1");
        $stmt->execute([$profileId]);
        return $stmt->fetch();
    }
    
    /**
     * Obtiene todas las fotos de un perfil
     */
    public static function getPhotosByProfileId($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM media WHERE profile_id = ? AND media_type = 'photo' ORDER BY is_primary DESC, order_num ASC");
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene todos los videos de un perfil
     */
    public static function getVideosByProfileId($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM media WHERE profile_id = ? AND media_type = 'video' ORDER BY order_num ASC");
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Cuenta el número de fotos de un perfil
     */
    public static function countPhotos($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM media WHERE profile_id = ? AND media_type = 'photo'");
        $stmt->execute([$profileId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Cuenta el número de videos de un perfil
     */
    public static function countVideos($profileId) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM media WHERE profile_id = ? AND media_type = 'video'");
        $stmt->execute([$profileId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Crea un nuevo medio
     */
    public static function create($mediaData) {
        $conn = getDbConnection();
        
        // Si es la primera foto y se marca como principal
        if ($mediaData['media_type'] === 'photo' && $mediaData['is_primary']) {
            // Quitar estado de principal a cualquier otra foto
            $stmtReset = $conn->prepare("UPDATE media SET is_primary = FALSE WHERE profile_id = ? AND media_type = 'photo' AND is_primary = TRUE");
            $stmtReset->execute([$mediaData['profile_id']]);
        }
        
        $sql = "INSERT INTO media (profile_id, media_type, filename, order_num, is_primary) 
                VALUES (:profile_id, :media_type, :filename, :order_num, :is_primary)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':profile_id', $mediaData['profile_id']);
        $stmt->bindParam(':media_type', $mediaData['media_type']);
        $stmt->bindParam(':filename', $mediaData['filename']);
        $stmt->bindParam(':order_num', $mediaData['order_num']);
        $stmt->bindParam(':is_primary', $mediaData['is_primary'], PDO::PARAM_BOOL);
        
        $stmt->execute();
        
        return $conn->lastInsertId();
    }
    
    /**
     * Actualiza un medio
     */
    public static function update($id, $data) {
        $conn = getDbConnection();
        
        // Si se está estableciendo como foto principal
        if (isset($data['is_primary']) && $data['is_primary']) {
            $media = self::getById($id);
            if ($media && $media['media_type'] === 'photo') {
                // Quitar estado de principal a cualquier otra foto
                $stmtReset = $conn->prepare("UPDATE media SET is_primary = FALSE WHERE profile_id = ? AND media_type = 'photo' AND is_primary = TRUE");
                $stmtReset->execute([$media['profile_id']]);
            }
        }
        
        $setFields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $setFields[] = "$field = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE media SET " . implode(', ', $setFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Establece una foto como principal
     */
    public static function setAsPrimary($id) {
        $conn = getDbConnection();
        
        // Obtener el medio
        $media = self::getById($id);
        
        if (!$media || $media['media_type'] !== 'photo') {
            return false;
        }
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        try {
            // Quitar estado de principal a todas las fotos del perfil
            $stmtReset = $conn->prepare("UPDATE media SET is_primary = FALSE WHERE profile_id = ? AND media_type = 'photo'");
            $stmtReset->execute([$media['profile_id']]);
            
            // Establecer la foto seleccionada como principal
            $stmtSet = $conn->prepare("UPDATE media SET is_primary = TRUE WHERE id = ?");
            $stmtSet->execute([$id]);
            
            // Confirmar transacción
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $conn->rollBack();
            return false;
        }
    }
    
    /**
     * Elimina un medio
     */
    public static function delete($id) {
        $conn = getDbConnection();
        
        // Obtener el medio antes de eliminarlo
        $media = self::getById($id);
        
        if (!$media) {
            return false;
        }
        
        // Eliminar el registro
        $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        // Si era la foto principal y se eliminó exitosamente, establecer otra como principal
        if ($result && $media['is_primary'] && $media['media_type'] === 'photo') {
            $stmtNext = $conn->prepare("SELECT id FROM media WHERE profile_id = ? AND media_type = 'photo' ORDER BY order_num ASC LIMIT 1");
            $stmtNext->execute([$media['profile_id']]);
            $nextPhoto = $stmtNext->fetch();
            
            if ($nextPhoto) {
                self::setAsPrimary($nextPhoto['id']);
            }
        }
        
        // Devolver la ruta del archivo para poder eliminarlo físicamente
        return $media['filename'];
    }
    
    /**
     * Reordena los medios de un perfil
     */
    public static function reorder($profileId, $mediaType, $idOrder) {
        $conn = getDbConnection();
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        try {
            // Actualizar el orden de cada medio
            for ($i = 0; $i < count($idOrder); $i++) {
                $stmt = $conn->prepare("UPDATE media SET order_num = ? WHERE id = ? AND profile_id = ? AND media_type = ?");
                $stmt->execute([$i, $idOrder[$i], $profileId, $mediaType]);
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