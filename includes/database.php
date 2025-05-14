<?php
// includes/database.php

require_once __DIR__ . '/../config/database.php';

/**
 * Ejecuta una consulta SELECT y devuelve todos los resultados
 */
function dbSelect($query, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Ejecuta una consulta SELECT y devuelve un solo resultado
 */
function dbSelectOne($query, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Ejecuta una consulta INSERT y devuelve el ID insertado
 */
function dbInsert($table, $data) {
    $conn = getDbConnection();
    
    $fields = array_keys($data);
    $placeholders = array_fill(0, count($fields), '?');
    
    $query = "INSERT INTO $table (" . implode(', ', $fields) . ") 
              VALUES (" . implode(', ', $placeholders) . ")";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_values($data));
    
    return $conn->lastInsertId();
}

/**
 * Ejecuta una consulta UPDATE
 */
function dbUpdate($table, $data, $where, $whereParams = []) {
    $conn = getDbConnection();
    
    $setFields = [];
    $params = [];
    
    foreach ($data as $field => $value) {
        $setFields[] = "$field = ?";
        $params[] = $value;
    }
    
    $query = "UPDATE $table SET " . implode(', ', $setFields) . " WHERE $where";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge($params, $whereParams));
    
    return $stmt->rowCount();
}

/**
 * Ejecuta una consulta DELETE
 */
function dbDelete($table, $where, $params = []) {
    $conn = getDbConnection();
    
    $query = "DELETE FROM $table WHERE $where";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    
    return $stmt->rowCount();
}

/**
 * Ejecuta una consulta COUNT
 */
function dbCount($table, $where = '1', $params = []) {
    $conn = getDbConnection();
    
    $query = "SELECT COUNT(*) as total FROM $table WHERE $where";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'];
}

/**
 * Inicia una transacción
 */
function dbBeginTransaction() {
    $conn = getDbConnection();
    return $conn->beginTransaction();
}

/**
 * Confirma una transacción
 */
function dbCommit() {
    $conn = getDbConnection();
    return $conn->commit();
}

/**
 * Revierte una transacción
 */
function dbRollback() {
    $conn = getDbConnection();
    return $conn->rollBack();
}

/**
 * Ejecuta una consulta personalizada
 */
function dbQuery($query, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Escapa un identificador SQL (tabla, columna)
 */
function dbEscapeIdentifier($identifier) {
    return '`' . str_replace('`', '``', $identifier) . '`';
}