-- =========================================
-- DATABASE
-- =========================================
CREATE DATABASE IF NOT EXISTS `iot_tongkat-pintar`;
USE `iot_tongkat-pintar`;

-- =========================================
-- TABLE: gps_logs (LOKASI)
-- =========================================
CREATE TABLE IF NOT EXISTS gps_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    latitude DOUBLE NOT NULL,
    longitude DOUBLE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- TABLE: alat_status (SENSOR)
-- =========================================
CREATE TABLE IF NOT EXISTS alat_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alat INT DEFAULT 1,
    latitude DOUBLE,
    longitude DOUBLE,
    jarak INT,
    buzzer TINYINT(1),
    batterai_persen INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================
-- TABLE: sos_logs (SOS)
-- =========================================
CREATE TABLE IF NOT EXISTS sos_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    latitude DOUBLE NOT NULL,
    longitude DOUBLE NOT NULL,
    sos_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- =========================================
-- TABLE: settings (CONFIG)
-- =========================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_setting VARCHAR(100),
    value VARCHAR(255)
);

-- =========================================
-- TABLE: users (LOGIN & OTP)
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_hp VARCHAR(20),
    otp VARCHAR(10),
    otp_expired DATETIME
);

-- =========================================
-- INSERT DEFAULT DATA
-- =========================================

-- Nomor SOS
INSERT INTO settings (nama_setting, value)
VALUES ('nomor_sos', '6285719916327');

-- User default
INSERT INTO users (nomor_hp, otp, otp_expired)
VALUES ('6285719916327', '123456', DATE_ADD(NOW(), INTERVAL 10 MINUTE));

-- Status alat default
INSERT INTO alat_status (id_alat, latitude, longitude, jarak, buzzer, batterai_persen)
VALUES (1, -7.134418, 112.380799, 0, 0, 100);

-- GPS sample
INSERT INTO gps_logs (latitude, longitude)
VALUES (-7.134215, 112.378447);

-- SOS sample (inactive)
INSERT INTO sos_logs (latitude, longitude, is_active)
VALUES (-0.53321, 117.12532, 0);