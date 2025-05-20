<?php

require_once __DIR__ . '/../config/database.php';

class Province
{
    /**
     * Obtiene todas las provincias
     */
    public static function getAll()
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM provinces ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una provincia por ID
     */
    public static function getById($id)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM provinces WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene una provincia por nombre
     */
    public static function getByName($name)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM provinces WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Crea una nueva provincia
     */
    public static function create($name)
    {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO provinces (name) VALUES (?)");
        $stmt->execute([$name]);
        return $conn->lastInsertId();
    }
}