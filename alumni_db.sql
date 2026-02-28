-- ============================================
-- Online Alumni Management System - SQL Setup
-- Database: alumni_db
-- Run this in phpMyAdmin SQL tab
-- ============================================

CREATE DATABASE IF NOT EXISTS alumni_db;
USE alumni_db;

-- Users table (login credentials + role)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'alumni') DEFAULT 'alumni',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Alumni profile details
CREATE TABLE IF NOT EXISTS alumni_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    passing_year INT,
    company VARCHAR(150),
    location VARCHAR(150),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jobs table
CREATE TABLE IF NOT EXISTS jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(150) NOT NULL,
    description TEXT,
    posted_by INT,
    posted_date DATE DEFAULT (CURRENT_DATE)
);

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin logs
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action VARCHAR(255),
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Default Admin Account
-- Email: admin@alumni.com | Password: admin123
-- ============================================
INSERT INTO users (name, email, password, role, status) VALUES
('Admin', 'admin@alumni.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved');

-- Sample approved alumni
INSERT INTO users (name, email, password, role, status) VALUES
('John Smith', 'john@alumni.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', 'approved'),
('Priya Sharma', 'priya@alumni.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', 'approved');

INSERT INTO alumni_profiles (user_id, phone, department, passing_year, company, location) VALUES
(2, '9876543210', 'Computer Science', 2020, 'Google', 'Bangalore'),
(3, '9123456789', 'Electronics', 2019, 'Infosys', 'Mumbai');

-- Sample events
INSERT INTO events (title, description, event_date, created_by) VALUES
('Annual Alumni Meet 2025', 'Join us for the annual reunion at the campus main hall.', '2025-06-15', 1),
('Tech Talk: AI & Future', 'Guest lecture by industry experts on AI trends.', '2025-04-20', 1);

-- Sample jobs
INSERT INTO jobs (company, position, description, posted_by, posted_date) VALUES
('TechCorp', 'Software Engineer', 'Looking for passionate developers with 2+ years experience.', 1, CURDATE()),
('DataInc', 'Data Analyst', 'Freshers welcome. Excel and Python skills required.', 1, CURDATE());
