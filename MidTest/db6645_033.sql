-- Database: db6645_033
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS db6645_033 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db6645_033;

-- Table structure for tb_664230033
CREATE TABLE IF NOT EXISTS tb_664230033 (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
INSERT INTO tb_664230033 (student_id, first_name, last_name, email, phone) VALUES
('664230033', 'เสกสรรญ', 'หลำวรรณะ', '664230033@webmail.npru.ac.th', '065XXXXXXX'),
('664230034', 'สมหญิง', 'รักสนุก', 'somying@example.com', '0823456789'),
('664230035', 'วิชัย', 'มั่นคง', 'wichai@example.com', '0834567890');
