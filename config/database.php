<?php
// config/database.php

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'luvia');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexión a la base de datos
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }
    
    return $conn;
}