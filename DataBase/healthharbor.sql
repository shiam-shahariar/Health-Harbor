-- ============================================================
-- HealthHarbor schema and repeatable sample data
-- Import this file using phpMyAdmin's Import tab.
-- Seeded login password for every sample account: admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS healthharbor CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE healthharbor;

CREATE TABLE IF NOT EXISTS users (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    email     VARCHAR(100) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    phone     VARCHAR(20),
    role      ENUM('patient','doctor','admin') NOT NULL DEFAULT 'patient',
    specialty VARCHAR(100) DEFAULT NULL,
    status    VARCHAR(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS specialties (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS slots (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id  INT NOT NULL,
    slot_date  DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time   TIME NOT NULL,
    is_booked  TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uq_slot_times (doctor_id, slot_date, start_time, end_time),
    UNIQUE KEY uq_slot_id_doctor (id, doctor_id),
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id  INT NOT NULL,
    slot_id    INT DEFAULT NULL UNIQUE,
    appt_date  DATE NOT NULL,
    appt_time  TIME NOT NULL,
    reason     VARCHAR(255),
    status     ENUM('Pending','Accepted','Rejected','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    notes      TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id, doctor_id) REFERENCES slots(id, doctor_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id  INT NOT NULL,
    rating     INT NOT NULL,
    review     TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- INSERT IGNORE keeps repeat imports safe without overwriting account changes.
INSERT IGNORE INTO users (name, email, password, phone, role, specialty, status) VALUES
('Admin User',    'admin@healthharbor.com',   '$2b$10$Zxxsmj2vJnQEe.y0haEAQe7vd1Nw09C.iCdzwlr.rvFnbE7y3PToK', '01700000000', 'admin',   NULL,         'active'),
('Dr. Rahman',    'rahman@healthharbor.com',  '$2b$10$Zxxsmj2vJnQEe.y0haEAQe7vd1Nw09C.iCdzwlr.rvFnbE7y3PToK', '01711111111', 'doctor',  'Cardiology', 'active'),
('Dr. Sultana',   'sultana@healthharbor.com', '$2b$10$Zxxsmj2vJnQEe.y0haEAQe7vd1Nw09C.iCdzwlr.rvFnbE7y3PToK', '01722222222', 'doctor',  'Neurology',  'pending'),
('Karim Ahmed',   'karim@mail.com',       '$2b$10$Zxxsmj2vJnQEe.y0haEAQe7vd1Nw09C.iCdzwlr.rvFnbE7y3PToK', '01733333333', 'patient', NULL,         'active');

INSERT IGNORE INTO specialties (name) VALUES
('Cardiology'), ('Dermatology'), ('Pediatrics'), ('Orthopedics'), ('Neurology');

-- A completed historical sample appointment is valid even though the date has passed.
INSERT IGNORE INTO slots (doctor_id, slot_date, start_time, end_time, is_booked)
SELECT d.id, '2026-08-28', '10:00:00', '11:00:00', 1
FROM users d
WHERE d.email = 'rahman@healthharbor.com' AND d.role = 'doctor';

INSERT IGNORE INTO appointments (patient_id, doctor_id, slot_id, appt_date, appt_time, reason, status)
SELECT p.id, d.id, s.id, s.slot_date, s.start_time, 'Chest pain check-up', 'Completed'
FROM users p
JOIN users d ON d.email = 'rahman@healthharbor.com' AND d.role = 'doctor'
JOIN slots s ON s.doctor_id = d.id
    AND s.slot_date = '2026-08-28'
    AND s.start_time = '10:00:00' AND s.end_time = '11:00:00'
WHERE p.email = 'karim@mail.com' AND p.role = 'patient';

UPDATE slots s
JOIN users d ON d.id = s.doctor_id AND d.email = 'rahman@healthharbor.com'
SET s.is_booked = 1
WHERE s.slot_date = '2026-08-28'
  AND s.start_time = '10:00:00' AND s.end_time = '11:00:00'
  AND EXISTS (SELECT 1 FROM appointments a WHERE a.slot_id = s.id);
