CREATE DATABASE IF NOT EXISTS student_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_management;

CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    course ENUM('BSIT', 'BSCS') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO students (id, name, email, age, course) VALUES
    (1, 'Juan Dela Cruz', 'juan@umindanao.edu.ph', 20, 'BSIT'),
    (2, 'Maria Santos', 'maria@umindanao.edu.ph', 19, 'BSCS'),
    (3, 'Pedro Reyes', 'pedro@umindanao.edu.ph', 21, 'BSIT');