-- Создание базы данных
CREATE DATABASE IF NOT EXISTS myne_sam_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myne_sam_db;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица видов услуг
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Заполнение таблицы услуг
INSERT INTO services (name) VALUES
('Общий клининг'),
('Генеральная уборка'),
('Послестроительная уборка'),
('Химчистка ковров и мебели');

-- Таблица заявок
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service_id INT NOT NULL,
    service_date DATETIME NOT NULL,
    payment_type ENUM('наличные', 'банковская карта') NOT NULL,
    status ENUM('новая заявка', 'услуга оказана', 'услуга отменена') DEFAULT 'новая заявка',
    rejection_reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
