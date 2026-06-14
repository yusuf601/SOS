-- SQL Script for Practical Work Task Management & Grading Platform
-- Target DBMS: MySQL / MariaDB (Optimized for PHP Native Backend)
-- Role: Senior Database Architect

-- Create database if it does not exist and select it
CREATE DATABASE IF NOT EXISTS db_praktikum;
USE db_praktikum;

-- Disable foreign key checks temporarily to drop tables in reverse dependency order safely
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they already exist to ensure clean initialization
DROP TABLE IF EXISTS Tabel_Nilai_Akhir;
DROP TABLE IF EXISTS Tabel_Nilai;
DROP TABLE IF EXISTS Tabel_Pengumpulan;
DROP TABLE IF EXISTS Tabel_Tugas;
DROP TABLE IF EXISTS Tabel_Presensi;
DROP TABLE IF EXISTS Tabel_Plotting_Asisten;
DROP TABLE IF EXISTS Tabel_Modul;
DROP TABLE IF EXISTS Tabel_User;
DROP TABLE IF EXISTS Tabel_Kelompok;
DROP TABLE IF EXISTS Tabel_Kelas;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- 1. TABEL MASTER & PLOTTING
-- ==========================================

-- Tabel_Kelas: Stores names of classes for the practical sessions
CREATE TABLE Tabel_Kelas (
    ID_Kelas INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Kelas VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Kelompok: Stores group details which belong to a specific class
CREATE TABLE Tabel_Kelompok (
    ID_Kelompok INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Kelompok VARCHAR(100) NOT NULL,
    ID_Kelas INT NOT NULL,
    CONSTRAINT FK_Kelompok_Kelas FOREIGN KEY (ID_Kelas) 
        REFERENCES Tabel_Kelas(ID_Kelas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_User: Stores credential and identity data for Mahasiswa, Asisten, and Dosen.
-- Mahasiswa is mapped to exactly one Kelompok via ID_Kelompok (nullable for Asisten/Dosen/unassigned Mahasiswa).
CREATE TABLE Tabel_User (
    ID_User INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Mahasiswa', 'Asisten', 'Dosen') NOT NULL,
    Nama_Lengkap VARCHAR(255) NOT NULL,
    ID_Kelompok INT DEFAULT NULL,
    CONSTRAINT FK_User_Kelompok FOREIGN KEY (ID_Kelompok)
        REFERENCES Tabel_Kelompok(ID_Kelompok)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Plotting_Asisten: Junction table for Many-to-Many mapping of Asisten/Dosen to Classes
CREATE TABLE Tabel_Plotting_Asisten (
    ID_Plotting INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_Kelas INT NOT NULL,
    CONSTRAINT FK_Plotting_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_Plotting_Kelas FOREIGN KEY (ID_Kelas) 
        REFERENCES Tabel_Kelas(ID_Kelas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Modul: Stores practical work modules and materials
CREATE TABLE Tabel_Modul (
    ID_Modul INT AUTO_INCREMENT PRIMARY KEY,
    Judul_Modul VARCHAR(255) NOT NULL,
    File_Materi VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 2. TABEL TRANSAKSIONAL
-- ==========================================

-- Tabel_Presensi: Tracks student attendance for each module
CREATE TABLE Tabel_Presensi (
    ID_Presensi INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_Modul INT NOT NULL,
    Status_Kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NOT NULL,
    Tanggal DATE NOT NULL,
    CONSTRAINT FK_Presensi_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_Presensi_Modul FOREIGN KEY (ID_Modul) 
        REFERENCES Tabel_Modul(ID_Modul) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Tugas: Contains assignments associated with specific modules
CREATE TABLE Tabel_Tugas (
    ID_Tugas INT AUTO_INCREMENT PRIMARY KEY,
    ID_Modul INT NOT NULL,
    Instruksi_Tugas TEXT NOT NULL,
    Deadline_Upload DATETIME NOT NULL,
    CONSTRAINT FK_Tugas_Modul FOREIGN KEY (ID_Modul) 
        REFERENCES Tabel_Modul(ID_Modul) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Pengumpulan: Tracks homework submissions by students
CREATE TABLE Tabel_Pengumpulan (
    ID_Pengumpulan INT AUTO_INCREMENT PRIMARY KEY,
    ID_Tugas INT NOT NULL,
    ID_User INT NOT NULL,
    File_Tugas VARCHAR(255) NOT NULL,
    Waktu_Submit DATETIME NOT NULL,
    CONSTRAINT FK_Pengumpulan_Tugas FOREIGN KEY (ID_Tugas) 
        REFERENCES Tabel_Tugas(ID_Tugas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_Pengumpulan_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Nilai: Stores grades, feedback, and tasks statuses assigned by assistants.
-- Also stores dispute (sanggahan) information initiated by students.
CREATE TABLE Tabel_Nilai (
    ID_Nilai INT AUTO_INCREMENT PRIMARY KEY,
    ID_Pengumpulan INT NOT NULL,
    ID_Asisten INT NOT NULL,
    Nilai_Angka DECIMAL(5,2) NOT NULL,
    Feedback TEXT DEFAULT NULL,
    Status_Tugas ENUM('Selesai', 'Sanggah', 'Revisi') NOT NULL,
    Alasan_Sanggah TEXT DEFAULT NULL,
    Tanggapan_Sanggah TEXT DEFAULT NULL,
    CONSTRAINT FK_Nilai_Pengumpulan FOREIGN KEY (ID_Pengumpulan) 
        REFERENCES Tabel_Pengumpulan(ID_Pengumpulan) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    -- Using ON DELETE RESTRICT for ID_Asisten to ensure historical grading integrity.
    -- An assistant cannot be deleted from the system if they have already graded submissions.
    CONSTRAINT FK_Nilai_Asisten FOREIGN KEY (ID_Asisten) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 3. TABEL EVALUASI AKHIR (DB_Nilai_Akhir)
-- ==========================================

-- Tabel_Nilai_Akhir: Stores the final calculated grades and graduation status per student per class
CREATE TABLE Tabel_Nilai_Akhir (
    ID_Nilai_Akhir INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL, -- Mahasiswa
    ID_Kelas INT NOT NULL, -- Kelas praktikum terkait
    Nilai_Akhir DECIMAL(5,2) NOT NULL,
    Status_Kelulusan ENUM('Lulus', 'Mengulang') NOT NULL,
    CONSTRAINT FK_NilaiAkhir_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_NilaiAkhir_Kelas FOREIGN KEY (ID_Kelas) 
        REFERENCES Tabel_Kelas(ID_Kelas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
