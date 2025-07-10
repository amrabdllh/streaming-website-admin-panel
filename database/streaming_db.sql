-- Streaming Website Database Structure

CREATE DATABASE IF NOT EXISTS streaming_website;
USE streaming_website;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Videos table
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    iframe_url TEXT NOT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert default settings
INSERT INTO settings (setting_name, setting_value) VALUES 
('site_name', 'StreamSite'),
('telegram_url', 'https://t.me/streamsite'),
('base_url', 'http://localhost'),
('seo_title', 'StreamSite - Watch Videos Online'),
('seo_description', 'Watch the latest videos online on StreamSite'),
('seo_keywords', 'streaming, videos, online, watch');
