<?php

require_once __DIR__ . '/../config/database.php';

class District
{
    /**
     * Obtiene todos los distritos de una provincia
     */
    public static function getByProvinceId($provinceId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM districts WHERE province_id = ? ORDER BY name");
        $stmt->execute([$provinceId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un distrito por ID
     */
    public static function getById($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM districts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene un distrito por nombre y provincia
     */
    public static function getByNameAndProvince($name, $provinceId)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM districts WHERE name = ? AND province_id = ?");
        $stmt->execute([$name, $provinceId]);
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo distrito
     */
    public static function create($provinceId, $name)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO districts (province_id, name) VALUES (?, ?)");
        $stmt->execute([$provinceId, $name]);
        return $conn->lastInsertId();
    }
}