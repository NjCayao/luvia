
-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'advertiser', 'visitor') NOT NULL,
    status ENUM('pending', 'active', 'suspended', 'deleted') DEFAULT 'pending',
    phone_verified BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(10),
    verification_expires DATETIME,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de perfiles (para anunciantes)
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    gender ENUM('female', 'male', 'trans') NOT NULL,
    description TEXT,
    whatsapp VARCHAR(20),
    city VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    schedule TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    whatsapp_clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de tarifas
CREATE TABLE rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    rate_type ENUM('hour', 'half_hour', 'extra') NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
);

-- Tabla de medios (fotos/videos)
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    media_type ENUM('photo', 'video') NOT NULL,
    filename VARCHAR(255) NOT NULL,
    order_num INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
);

-- Tabla de planes
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_type ENUM('advertiser', 'visitor') NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in days',
    price DECIMAL(10,2) NOT NULL,
    max_photos INT,
    max_videos INT,
    featured BOOLEAN DEFAULT FALSE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de pagos
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PEN',
    payment_method VARCHAR(50) NOT NULL COMMENT 'card, yape, etc',
    transaction_id VARCHAR(100),
    payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    izipay_session_id VARCHAR(100),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id)
);

-- Tabla de suscripciones
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    payment_id INT,
    status ENUM('trial', 'active', 'expired', 'cancelled') DEFAULT 'trial',
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    auto_renew BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    FOREIGN KEY (payment_id) REFERENCES payments(id)
);

-- Tabla de estadísticas
CREATE TABLE stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT,
    action_type ENUM('view', 'whatsapp_click') NOT NULL,
    ip_address VARCHAR(50),
    user_agent TEXT,
    visitor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (visitor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla de verificación (para registros de verificación)
CREATE TABLE verification_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    verification_type ENUM('id_card', 'selfie', 'document') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    verified_by INT,
    document_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id)
);