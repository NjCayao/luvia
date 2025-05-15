<?php
// includes/validation.php

/**
 * Valida un campo requerido
 */
function validateRequired($value, $field = 'Este campo') {
    if (empty($value)) {
        return "$field es obligatorio";
    }
    return null;
}

/**
 * Valida el formato de email
 */
function validateEmail($email) {
    if (empty($email)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El correo electrónico no tiene un formato válido";
    }
    
    return null;
}

/**
 * Valida el formato de número de teléfono peruano
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    // Formato peruano: +51999999999 o 999999999
    $phone = preg_replace('/\s+/', '', $phone); // Eliminar espacios
    
    // Validar formato
    if (preg_match('/^\+51[9]\d{8}$/', $phone) || preg_match('/^[9]\d{8}$/', $phone)) {
        return null;
    }
    
    return "El número de teléfono no tiene un formato válido";
}

/**
 * Valida la longitud mínima
 */
function validateMinLength($value, $min, $field = 'Este campo') {
    if (empty($value)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if (mb_strlen($value) < $min) {
        return "$field debe tener al menos $min caracteres";
    }
    
    return null;
}

/**
 * Valida la longitud máxima
 */
function validateMaxLength($value, $max, $field = 'Este campo') {
    if (empty($value)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if (mb_strlen($value) > $max) {
        return "$field no puede exceder los $max caracteres";
    }
    
    return null;
}

/**
 * Valida que las contraseñas coincidan
 */
function validatePasswordMatch($password, $confirmPassword) {
    if ($password !== $confirmPassword) {
        return "Las contraseñas no coinciden";
    }
    
    return null;
}

/**
 * Valida la fortaleza de la contraseña
 */
function validatePasswordStrength($password) {
    if (empty($password)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    // Al menos 8 caracteres, una letra mayúscula, una minúscula y un número
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        return "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula y un número";
    }
    
    return null;
}

/**
 * Valida una URL
 */
function validateUrl($url) {
    if (empty($url)) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return "La URL no tiene un formato válido";
    }
    
    return null;
}

/**
 * Valida un valor numérico
 */
function validateNumeric($value, $field = 'Este campo') {
    if (empty($value) && $value !== '0') {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if (!is_numeric($value)) {
        return "$field debe ser un valor numérico";
    }
    
    return null;
}

/**
 * Valida un rango numérico
 */
function validateRange($value, $min, $max, $field = 'Este campo') {
    if (empty($value) && $value !== '0') {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    $numericError = validateNumeric($value, $field);
    if ($numericError !== null) {
        return $numericError;
    }
    
    if ($value < $min || $value > $max) {
        return "$field debe estar entre $min y $max";
    }
    
    return null;
}

/**
 * Valida un archivo (tamaño y tipo)
 */
function validateFile($file, $maxSize, $allowedTypes, $field = 'El archivo') {
    if (empty($file['tmp_name'])) {
        return null; // Se manejará con validateRequired si es necesario
    }
    
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / (1024 * 1024);
        return "$field no debe exceder los $maxSizeMB MB";
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return "$field tiene un formato no permitido";
    }
    
    return null;
}

/**
 * Validador general para formularios
 */
function validateForm($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $fieldRules) {
        foreach ($fieldRules as $rule) {
            $ruleName = $rule[0];
            $ruleParams = array_slice($rule, 1);
            $value = $data[$field] ?? '';
            
            // Agregar el valor como primer parámetro
            array_unshift($ruleParams, $value);
            
            // Llamar a la función de validación correspondiente
            $error = call_user_func_array($ruleName, $ruleParams);
            
            if ($error !== null) {
                $errors[$field] = $error;
                break; // Pasar a la siguiente regla del campo
            }
        }
    }
    
    return $errors;
}

