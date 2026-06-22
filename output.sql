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
DROP TABLE IF EXISTS Tabel_KRS;
DROP TABLE IF EXISTS Tabel_Kelas;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- 1. TABEL MASTER & PLOTTING
-- ==========================================

-- Tabel_Kelas: Stores names of classes for the practical sessions
CREATE TABLE Tabel_Kelas (
    ID_Kelas INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Kelas VARCHAR(100) NOT NULL,
    Jadwal VARCHAR(50) DEFAULT NULL,
    Token_Kelas VARCHAR(10) NULL
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
    Nama_Lengkap VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_KRS: Stores mapping of Mahasiswa to Kelas (Joined via Token)
CREATE TABLE Tabel_KRS (
    ID_KRS INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_Kelas INT NOT NULL,
    ID_Kelompok INT DEFAULT NULL,
    CONSTRAINT FK_KRS_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_KRS_Kelas FOREIGN KEY (ID_Kelas) 
        REFERENCES Tabel_Kelas(ID_Kelas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_KRS_Kelompok FOREIGN KEY (ID_Kelompok) 
        REFERENCES Tabel_Kelompok(ID_Kelompok) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    UNIQUE KEY UQ_User_Kelas (ID_User, ID_Kelas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel_Plotting_Asisten: Junction table for Many-to-Many mapping of Asisten/Dosen to Classes
CREATE TABLE Tabel_Plotting_Asisten (
    ID_Plotting INT AUTO_INCREMENT PRIMARY KEY,
    ID_User INT NOT NULL,
    ID_Kelas INT NOT NULL,
    ID_Kelompok INT DEFAULT NULL,
    CONSTRAINT FK_Plotting_User FOREIGN KEY (ID_User) 
        REFERENCES Tabel_User(ID_User) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_Plotting_Kelas FOREIGN KEY (ID_Kelas) 
        REFERENCES Tabel_Kelas(ID_Kelas) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT FK_Plotting_Kelompok FOREIGN KEY (ID_Kelompok) 
        REFERENCES Tabel_Kelompok(ID_Kelompok) 
        ON DELETE SET NULL 
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

-- ==========================================
-- 4. DATA INITIALIZATION (DOSEN DATA)
-- ==========================================

-- Insert Dosen (Lecturers) from daftar_dosen.md
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('0017117606', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Isnawaty, S.Si., M.T', NULL),
('0022078406', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'L.M. Fid Aksara, S.Kom., M.Kom.', NULL),
('0007118106', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Statiswaty, S.T., M.MSI', NULL),
('0017089402', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Asa Hari Wibowo, S.T., M.Eng.', NULL),
('0022027607', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Sutardi, S.Kom., M.T.', NULL),
('0020107601', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Prof. Dr. Ir. Laode Muh. Golok Jaya, S.T., M.T.', NULL),
('0025047107', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Bambang Pramono, S.Si., M.T.', NULL),
('0006049104', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Rizal Adi Saputra, S.T., M.Kom', NULL),
('0906028701', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Jumadil Nangi, S.Kom., M.T.', NULL),
('0009096503', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Dr. Ir. Muhammad Ihsan Sarita, M.Kom.', NULL),
('0016018306', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Ika Purwanti Ningrum Purnama, S.Kom., M.Cs.', NULL),
('0929098602', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'La Ode Muhammad Bahtiar Aksara, S.T., M.T.', NULL),
('0014068304', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Dr. Ir. Muh. Yamin, S.T., M.Eng', NULL),
('0023069101', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Adha Mashur Sajiah, S.T., M.Eng.', NULL),
('0912069303', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Muhammad Irwan Syahib, S.T., M.Kom.', NULL),
('0117019203', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dosen', 'Ryan Rinaldi Hadistio, S.Kom., M.Kom.', NULL);

-- ==========================================
-- 5. MASSIVE DATA SEEDING (merged from old sections 5, 6, 7)
-- ==========================================



-- ==============================================================================
-- 7. MASSIVE DATA SEEDING (BIASED STUDENT DISTRIBUTION FOR DEMO)
-- ==============================================================================

-- Asisten Angkatan 23
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap) VALUES
('E1E123101', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 1'),
('E1E123102', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 2'),
('E1E123103', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 3'),
('E1E123104', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 4'),
('E1E123105', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 5'),
('E1E123106', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 6'),
('E1E123107', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 7'),
('E1E123108', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 8'),
('E1E123109', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 9'),
('E1E123110', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 10'),
('E1E123111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 11'),
('E1E123112', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 12'),
('E1E123113', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 13'),
('E1E123114', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 14'),
('E1E123115', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 15'),
('E1E123116', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 16'),
('E1E123117', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 17'),
('E1E123118', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 18'),
('E1E123119', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 19'),
('E1E123120', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 20'),
('E1E123121', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 21'),
('E1E123122', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 22'),
('E1E123123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 23'),
('E1E123124', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 24'),
('E1E123125', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 25'),
('E1E123126', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 26'),
('E1E123127', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 27'),
('E1E123128', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 28'),
('E1E123129', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 29'),
('E1E123130', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Asisten Praktikum 30');

-- Mahasiswa Angkatan 24 & 25
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap) VALUES
('E1E124021', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WA ODE INDAH NURRAMADHANI'),
('E1E124052', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SUCI WULANDARI'),
('E1E124051', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SRI MAHARANI'),
('E1E124016', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SHEERA ANNISA'),
('E1E124050', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SASKYA MEYTRA ODE'),
('E1E124077', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'RIZKMAH LAILATUL RAMADHANI'),
('E1E124076', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'PUTRI FADHILAH ZUHAIRAH'),
('E1E124012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'NINDY ASMAWATY'),
('E1E124046', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'NEYSA RAZANA MAHNEERAH'),
('E1E124071', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. ALBYANSYAH QAISHAR POROSI'),
('E1E123038', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'M. AL-FATH NAZRIEL RAJAB'),
('E1E124064', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUHAMAD AHLUL BAIT'),
('E1E124039', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE GUNTUR KAIMUDIN'),
('E1E124036', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FRISILIA FEBIOLA'),
('E1E124033', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FADHILAH FAJAR RAHMA HEDA'),
('E1E124004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'AWALIYAH FADHILATUN NISA'),
('E1E124057', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ANNISA NURUL FAIZAH'),
('E1E123060', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Alvin S. Padangaran'),
('E1E124025', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'AGUS HARTONO'),
('E1E124022', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WA RAHMIYANTI'),
('E1E124080', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WA ODE YURISMAWATI'),
('E1E124020', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WA LIANDANI'),
('E1E124019', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'THALIA DWI PUSPITA AYU'),
('E1E124053', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SYAFIQ DAWWAS'),
('E1E124015', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'REZKI ALYA PASRUN'),
('E1E124034', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FAIRUZ NAILAL RAJWA PUTRI AMRAN'),
('E1E124013', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'PUTU EKA FEBRIANI'),
('E1E124047', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'NIKMAL ANAKORUO'),
('E1E123072', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Rafli Radiyatul Zulfikar'),
('E1E124069', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH FILDAN PRATAMA'),
('E1E124068', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MIKHAEL ABRAHAM WIDIANTO'),
('E1E123067', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LUCKY AWARDA SANJUNG'),
('E1E124008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUHAMMAD SULTHAN KOLOGOU'),
('E1E124007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUHAMAD DIRGA'),
('E1E124063', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUAMMAR RAIHAN SALADIN'),
('E1E124062', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE AFDAL MUNIFA'),
('E1E124035', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FATIH MUHAMMAD BINTANG POSSUMAH'),
('E1E124060', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FAJRINA AULIA AMLAN'),
('E1E124032', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ELDA INDAH SANDA LANGI'),
('E1E124005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ELANG FIRMANSYAH'),
('E1E124030', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ASRAF FALAJ BUNTALA'),
('E1E124029', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ANISSA SALSABILA'),
('E1E124056', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ALISCHA PUTRI WULAN AZ-ZAHWA'),
('E1E124002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ABDUL RAHIM HUSEIN'),
('E1E124055', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ABDILLAH JUMAWAL KODA'),
('E1E124042', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. FAHRIL'),
('E1E123019', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'UCOK MARULLI MATONDANG'),
('E1E124054', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SYAWAL AHMAD RABIUL'),
('E1E124018', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SYAKILA MEILANA RUSLIN'),
('E1E124017', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SYAFIATUL ADAWIAH'),
('E1E124014', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'RESKY YANI'),
('E1E124048', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'RAHMI'),
('E1E124010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUHAMMAD ROZZAAQ NUR RAMADHAN'),
('E1E124073', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUHAMAD ASWAAD SATRIA PRATAMA'),
('E1E124044', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. YUSUF'),
('E1E124043', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. NUR ALAM SYAHRIR'),
('E1E124082', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ZULFAN NURCAHAYDI'),
('E1E124041', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MIQDAD ASYRAF RIZQULLAH'),
('E1E124067', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MAYA AGUSTIN'),
('E1E124066', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE NAUVAL AQILLAH TSAQIF'),
('E1E124040', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUHAMAD INDRA RUKMANA'),
('E1E124037', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'GILANG SYAH FITRAH RAMADHAN'),
('E1E124006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'FARID KHADHRA RAMADHAN'),
('E1E123073', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'RABIATUL ADAWIA'),
('E1E124031', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'DIVA PRATIWI SARMUDIN'),
('E1E124058', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ARRIYA RAHALDI AL KANDARI'),
('E1E124003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'AQNY DANU UTAMI'),
('E1E124028', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ANDER GIBRAN SIREGAR'),
('E1E124027', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ANANDA ADEN PUTRA'),
('E1E124026', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'AIRUL ROFIQ RAMADHAN'),
('E1E124024', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'AGUS CASHFLOWER AKBAR'),
('E1E124001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'A. MUFIDAH IDRIS'),
('E1E123040', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. ARIF RAHMAN GANI'),
('E1E122126', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'NADYA ELFARETA AZARIN'),
('E1E124072', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUH. RABILDZAN'),
('E1E122084', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ADIPATI CAESAR IBANEZ'),
('E1E124081', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WAHAB RAHMAN SAPUTRA'),
('E1E120096', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'WA ODE NAILA NTANGU'),
('E1E124078', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'TOBING ADYA YAKOP'),
('E1E123077', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SLAMET DZUBAIR ALFATH'),
('E1E122022', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUJIB CHUSNI MUBAROK'),
('E1E124074', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUHAMMAD DZAKY FIRDAUS'),
('E1E121061', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUH. NURALAL HAMDIH HALU'),
('E1E122054', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'GAMALIEL GUSRAYANTO'),
('E1E122092', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'DIKHSAN DWIRANGGA TIBONG'),
('E1E123059', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'ALFA CHRISTO REOMEGA'),
('E1E124079', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'VYOLA CECILIA POTTO'),
('E1E124038', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'GITA PRANGESTI'),
('E1E124045', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'MUHAMMAD RIZKY YAMIN'),
('E1E124061', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'KAHAR MUNAJAT'),
('E1E124023', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'A. TENRY LIU MEY ASPAT COLLE'),
('E1E124049', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'SAFARIL ADAM'),
('E1E124065', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'LA ODE MUHAMMAD FAUZILAZHIM'),
('E1E125003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Afriani'),
('E1E125026', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Salsabila'),
('E1E125047', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'La Ode Abdul Kadir Rambega'),
('E1E125088', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Rifky Safaat Sidarima'),
('E1E125076', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ibnu Zabil Sudiro'),
('E1E125052', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Laode Muhammad Syahril'),
('E1E125027', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Sri Mulandari'),
('E1E125095', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nurul Mulya Azahra'),
('E1E125081', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Mawar Agustina'),
('E1E125064', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ramlan Ahmaddaun'),
('E1E125029', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Yuliah Rifka'),
('E1E125038', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Berland Tongapa'),
('E1E125092', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nindi Dwiani'),
('E1E125039', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Dwi Azizah Nur\'Abidah'),
('E1E125041', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Filsa Salsabilah'),
('E1E125051', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Langid Gilang Ramadhan Ode'),
('E1E125055', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Ashif Al Banna'),
('E1E125017', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Maharani Prita Anansari'),
('E1E125011', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Husain Mubarak'),
('E1E125087', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Ali Shanjaya'),
('E1E125101', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Sheyla Rahmaniar'),
('E1E125093', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nurul Aulia Ramadhani'),
('E1E125040', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Endrico Gleno Delo'),
('E1E125089', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Sultan Maftuh Ramadhan'),
('E1E125099', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Rustang'),
('E1E123027', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Arilma Marchino Rombe Matandung'),
('E1E125034', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Annisa Alimmunirah'),
('E1E125103', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Tisya Amirah Raisya'),
('E1E125044', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Iskandar Zacfaron'),
('E1E125083', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Zayyan'),
('E1E125013', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Idayana. S'),
('E1E125016', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Lutfi Rahman Al-Fayed'),
('E1E125053', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Mario Christian Choukrosimon'),
('E1E125001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Abdul Razak'),
('E1E125062', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Oskar Dwiyana'),
('E1E125104', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Yasni Rezky Fitri Azhara'),
('E1E125025', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nur Rahma Rezki'),
('E1E125084', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhhmad Ade Saputra Rizal'),
('E1E125075', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Fardan'),
('E1E125043', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Hilbran Safar Deyaska'),
('E1E125077', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Irdina Zaafarani'),
('E1E125071', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ahmad Farel Al Husen'),
('E1E125012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'I Made Merta Santika'),
('E1E12510', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Afriani'),
('E1E125061', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nur Aisyah'),
('E1E125054', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Nabil'),
('E1E125020', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Assjul Subhi'),
('E1E125066', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Roihan Fajrur Ramadhan'),
('E1E125068', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Tegar Adiyatma Nugraha Santoso'),
('E1E125102', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Suci Agista Ramadani Lubis'),
('E1E125035', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ayu Ratih Suputri'),
('E1E125009', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Fitra Nurul Fadya'),
('E1E125008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Fainal'),
('E1E125024', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nayla Rizqy Dermawan'),
('E1E125059', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Hilal Adrian'),
('E1E125074', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Fadil Tri Augusta'),
('E1E125019', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Arsyaf Nawir'),
('E1E125018', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Moch. Rayzhan Absar Alwanie'),
('E1E125080', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Maisy Putri Syahrani Saranani'),
('E1E125105', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Zahra Kirana'),
('E1E125006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Denyawan'),
('E1E125063', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Purwanti Rethob Rumlean'),
('E1E125100', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Sara Nabila Putri Sahlin'),
('E1E125086', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Alfin'),
('E1E125096', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Oliv'),
('E1E125014', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Arul'),
('E1E125067', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Saskia Masita'),
('E1E125037', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Badran Nawwaar Rumudale'),
('E1E125004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Andi Maiva Ismar'),
('E1E125042', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Hikmat Hidayat'),
('E1E125056', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Fahri Fairuz R.'),
('E1E125045', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Isma Ayu'),
('E1E125007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Dino Fahri'),
('E1E125015', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Leon Christian'),
('E1E125050', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'La Ode Muhammad Faathir Asshadiq'),
('E1E125078', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Keisya Zalwa Azzahra'),
('E1E125091', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nilsa Kiraniya Nurmi Zaluna L.'),
('E1E125069', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Wa Ode Khairatun'),
('E1E125082', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Alfaril Anugrah L.'),
('E1E125021', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Fisabillah'),
('E1E125070', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Adinda rizky wulandari'),
('E1E125098', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'La Ode Muh Raditya Fardhan'),
('E1E125046', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Khasanul Fajri'),
('E1E125032', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ahmat Landfran'),
('E1E125048', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'La Ode Agus Syaifullah'),
('E1E125028', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Vickya Heriana Saputri'),
('E1E125060', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Munabila'),
('E1E125058', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh. Fathan Ramadhan'),
('E1E125033', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Andi Muhammad Arfan Said'),
('E1E125023', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nailah Nur Salsabila Salim'),
('E1E125036', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Azka Aria Putra'),
('E1E125057', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muh Farrel Prasetya Rasit'),
('E1E125005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Annisa Fitria Zahra A.'),
('E1E125079', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Laode Usman Rassya Rianta'),
('E1E125094', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nurul Cahyanisa Putri'),
('E1E125097', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Putri Aisyiah Wahyuni'),
('E1E125002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Abdurrahman Al-Arafah'),
('E1E125090', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Nandira Kanza Alika Ramdani'),
('E1E125073', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Arief Dwi Yanuar'),
('E1E125085', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Muhammad Akbar Al Badawi'),
('E1E125049', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'La Ode Erlan Al Azham Are'),
('E1E125065', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Reva Aurel Amanda');

INSERT INTO Tabel_Kelas (Nama_Kelas) VALUES
('PEMROGRAMAN BERORIENTASI OBJEK'),
('Pemrograman Web'),
('REKAYASA PERANGKAT LUNAK'),
('Struktur Data'),
('Sistem Basis Data'),
('Jaringan Komputer'),
('Sistem Operasi'),
('Analisis dan Desain Perangkat Lunak'),
('Cloud Computing'),
('Kecerdasan Buatan'),
('Pembelajaran Mesin'),
('Data Mining (Pilihan 1)'),
('Keamanan Data dan Informasi (Cyber Security)'),
('INTERAKSI MANUSIA DAN KOMPUTER'),
('Sistem Paralel dan Terdistribusi'),
('METODE RISET'),
('ETIKA PROFESI'),
('Analisis Jejaring Sosial'),
('Pemrograman Dasar'),
('Matematika Diskrit'),
('Sistem Digital'),
('Organisasi dan Arsitektur Komputer'),
('Statistika'),
('ALJABAR LINEAR'),
('METODE NUMERIK');

-- Plotting Dosen ke Kelas
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES
((SELECT ID_User FROM Tabel_User WHERE Username = '0023069101' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0023069101' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0023069101' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0023069101' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022027607' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022027607' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022027607' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022078406' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022078406' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0022078406' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0017089402' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0017089402' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0017089402' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0007118106' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0007118106' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0007118106' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0025047107' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0025047107' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0025047107' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0006049104' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0006049104' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0020107601' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0020107601' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0016018306' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data' LIMIT 1)),
((SELECT ID_User FROM Tabel_User WHERE Username = '0016018306' LIMIT 1), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial' LIMIT 1));


-- Processing Class: PEMROGRAMAN BERORIENTASI OBJEK (25 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'PEMROGRAMAN BERORIENTASI OBJEK'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124021';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124052';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124051';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124016';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124050';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124077';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124076';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124012';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124046';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124071';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E123038';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124064';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124039';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124036';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124033';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124004';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124057';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123060';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124025';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124022';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124080';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124020';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124019';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124053';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124015';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 PEMROGRAMAN BERORIENTASI OBJEK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 44, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 74, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124046'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 PEMROGRAMAN BERORIENTASI OBJEK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 54, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 PEMROGRAMAN BERORIENTASI OBJEK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 30, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 PEMROGRAMAN BERORIENTASI OBJEK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 68, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 47, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 62, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 84, 'Bagus', 'Selesai');

-- Processing Class: Pemrograman Web (25 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Web'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124034';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124013';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124047';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E123072';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124069';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124068';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E123067';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124008';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124007';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124063';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124062';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124035';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124060';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124032';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124005';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124030';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124029';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124056';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124002';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124055';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124042';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123019';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124054';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124018';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124017';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Pemrograman Web', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 64, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 62, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 68, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 57, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 54, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Pemrograman Web', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 68, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 34, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Pemrograman Web', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Pemrograman Web', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 67, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124030'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 46, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 95, 'Bagus', 'Selesai');

-- Processing Class: REKAYASA PERANGKAT LUNAK (25 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'REKAYASA PERANGKAT LUNAK'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124014';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124048';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124010';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124073';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124044';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124043';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124082';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124041';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124067';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124066';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124040';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E124037';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124006';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123073';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124031';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124058';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124003';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124028';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124027';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124026';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124024';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124001';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123040';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E122126';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124072';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 REKAYASA PERANGKAT LUNAK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 70, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 REKAYASA PERANGKAT LUNAK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 68, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 64, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 REKAYASA PERANGKAT LUNAK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 66, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 58, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 67, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 REKAYASA PERANGKAT LUNAK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 49, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124010'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124031'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124026'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 38, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122126'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124072'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 77, 'Bagus', 'Selesai');

-- Processing Class: Struktur Data (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Struktur Data'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E122084';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Struktur Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Struktur Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Struktur Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Struktur Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 88, 'Bagus', 'Selesai');

-- Processing Class: Sistem Basis Data (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Basis Data'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124081';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Sistem Basis Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Sistem Basis Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Sistem Basis Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Sistem Basis Data', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 90, 'Bagus', 'Selesai');

-- Processing Class: Jaringan Komputer (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Jaringan Komputer'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E120096';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Jaringan Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Jaringan Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Jaringan Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Jaringan Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E120096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), 90, 'Bagus', 'Selesai');

-- Processing Class: Sistem Operasi (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123121'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Operasi'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124078';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Sistem Operasi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Sistem Operasi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123121'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Sistem Operasi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 62, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Sistem Operasi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124078'), @mod_id, 'Hadir', NOW());

-- Processing Class: Analisis dan Desain Perangkat Lunak (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123128'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis dan Desain Perangkat Lunak'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123077';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Analisis dan Desain Perangkat Lunak', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Analisis dan Desain Perangkat Lunak', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Analisis dan Desain Perangkat Lunak', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Analisis dan Desain Perangkat Lunak', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 76, 'Bagus', 'Selesai');

-- Processing Class: Cloud Computing (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123124'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Cloud Computing'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E122022';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Cloud Computing', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Cloud Computing', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Cloud Computing', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Cloud Computing', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122022'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 87, 'Bagus', 'Selesai');

-- Processing Class: Kecerdasan Buatan (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123129'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Kecerdasan Buatan'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124074';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Kecerdasan Buatan', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Kecerdasan Buatan', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Kecerdasan Buatan', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Kecerdasan Buatan', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 85, 'Bagus', 'Selesai');

-- Processing Class: Pembelajaran Mesin (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123113'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123121'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pembelajaran Mesin'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E121061';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Pembelajaran Mesin', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Pembelajaran Mesin', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123113'), 45, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Pembelajaran Mesin', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Pembelajaran Mesin', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E121061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 76, 'Bagus', 'Selesai');

-- Processing Class: Data Mining (Pilihan 1) (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Data Mining (Pilihan 1)'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E122054';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Data Mining (Pilihan 1)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Data Mining (Pilihan 1)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Data Mining (Pilihan 1)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 65, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Data Mining (Pilihan 1)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 94, 'Bagus', 'Selesai');

-- Processing Class: Keamanan Data dan Informasi (Cyber Security) (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Keamanan Data dan Informasi (Cyber Security)'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E122092';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Keamanan Data dan Informasi (Cyber Security)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123115'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Keamanan Data dan Informasi (Cyber Security)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Keamanan Data dan Informasi (Cyber Security)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Keamanan Data dan Informasi (Cyber Security)', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 92, 'Bagus', 'Selesai');

-- Processing Class: INTERAKSI MANUSIA DAN KOMPUTER (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123128'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'INTERAKSI MANUSIA DAN KOMPUTER'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123059';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 INTERAKSI MANUSIA DAN KOMPUTER', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 INTERAKSI MANUSIA DAN KOMPUTER', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123127'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 INTERAKSI MANUSIA DAN KOMPUTER', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123128'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 INTERAKSI MANUSIA DAN KOMPUTER', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123128'), 84, 'Bagus', 'Selesai');

-- Processing Class: Sistem Paralel dan Terdistribusi (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123113'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Paralel dan Terdistribusi'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124079';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Sistem Paralel dan Terdistribusi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Sistem Paralel dan Terdistribusi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Sistem Paralel dan Terdistribusi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Sistem Paralel dan Terdistribusi', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 79, 'Bagus', 'Selesai');

-- Processing Class: METODE RISET (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123113'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE RISET'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124038';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 METODE RISET', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 73, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 METODE RISET', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 METODE RISET', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123118'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 METODE RISET', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124038'), @mod_id, 'Hadir', NOW());

-- Processing Class: ETIKA PROFESI (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ETIKA PROFESI'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124045';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 ETIKA PROFESI', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 ETIKA PROFESI', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 68, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 ETIKA PROFESI', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 ETIKA PROFESI', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123114'), 89, 'Bagus', 'Selesai');

-- Processing Class: Analisis Jejaring Sosial (1 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Analisis Jejaring Sosial'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E124061';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Analisis Jejaring Sosial', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Analisis Jejaring Sosial', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Analisis Jejaring Sosial', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Analisis Jejaring Sosial', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E124061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123105'), 95, 'Bagus', 'Selesai');

-- Processing Class: Pemrograman Dasar (40 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Pemrograman Dasar'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125003';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125026';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125047';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125088';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125076';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125052';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125027';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125095';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125081';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125064';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125029';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125038';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125092';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125039';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125041';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125051';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125055';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125017';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125011';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125087';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125101';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125093';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125040';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125089';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125099';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E123027';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125034';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125103';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125044';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125083';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125013';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125016';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125053';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125001';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125062';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125104';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125025';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125084';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125075';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125043';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Pemrograman Dasar', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 62, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 36, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 53, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 62, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 74, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 34, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 49, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Pemrograman Dasar', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 73, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Pemrograman Dasar', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 73, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 43, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 56, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 73, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 56, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Pemrograman Dasar', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125003'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125026'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125047'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125088'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125076'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125052'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125095'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125081'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 52, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125064'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125029'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125038'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125092'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125039'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125041'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125051'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 40, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125055'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125017'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125011'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125087'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125101'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125093'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125040'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125089'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125099'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123027'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125034'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125103'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125044'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125083'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125013'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125016'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125053'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125001'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125062'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125104'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125025'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123126'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125084'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125075'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125043'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123120'), 89, 'Bagus', 'Selesai');

-- Processing Class: Matematika Diskrit (10 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Matematika Diskrit'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125077';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125071';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125012';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E12510';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125061';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125054';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125020';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125066';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125068';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125102';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Matematika Diskrit', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Matematika Diskrit', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 39, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Matematika Diskrit', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Matematika Diskrit', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125077'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125071'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125012'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E12510'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125061'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125054'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 73, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125020'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125066'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 70, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125068'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125102'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 98, 'Bagus', 'Selesai');

-- Processing Class: Sistem Digital (10 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Sistem Digital'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125035';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125009';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125008';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125024';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125059';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125074';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125019';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125018';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125080';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125105';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Sistem Digital', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Sistem Digital', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 100, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Sistem Digital', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Sistem Digital', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125035'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125009'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125008'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125024'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125059'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125074'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125019'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125018'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123111'), 77, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125080'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125105'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123110'), 92, 'Bagus', 'Selesai');

-- Processing Class: Organisasi dan Arsitektur Komputer (10 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Organisasi dan Arsitektur Komputer'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125006';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125063';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125100';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125086';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125096';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125014';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125067';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125037';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125004';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125042';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Organisasi dan Arsitektur Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 37, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 63, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Organisasi dan Arsitektur Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123108'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Organisasi dan Arsitektur Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123106'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 53, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Organisasi dan Arsitektur Komputer', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125006'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125063'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125100'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125086'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123112'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125096'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125014'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125067'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125037'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125004'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125042'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 78, 'Bagus', 'Selesai');

-- Processing Class: Statistika (10 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'Statistika'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125056';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125045';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125007';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125015';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125050';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125078';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125091';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125069';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125082';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125021';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 Statistika', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 83, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 Statistika', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 Statistika', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123123'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 Statistika', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125056'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125045'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125007'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125015'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125050'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125078'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123102'), 79, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125091'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125069'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123125'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125082'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 55, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125021'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123107'), 53, 'Ulangi', 'Revisi');

-- Processing Class: ALJABAR LINEAR (10 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'ALJABAR LINEAR'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125070';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125098';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125046';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125032';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125048';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125028';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125060';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125058';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125033';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125023';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 ALJABAR LINEAR', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 71, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 ALJABAR LINEAR', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 87, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 43, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 95, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 80, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 ALJABAR LINEAR', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 55, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 51, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123119'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 ALJABAR LINEAR', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125070'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125098'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125046'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 48, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125032'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123101'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125048'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 67, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125028'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125060'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 94, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125058'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125033'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123116'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125023'), @mod_id, 'Hadir', NOW());

-- Processing Class: METODE NUMERIK (12 students)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK'));
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK'));
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES ('Kelompok 1', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK')), ('Kelompok 2', (SELECT ID_Kelas FROM Tabel_Kelas WHERE Nama_Kelas = 'METODE NUMERIK'));
SET @k1 = LAST_INSERT_ID();
SET @k2 = LAST_INSERT_ID() + 1;
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125036';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125057';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125005';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125079';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125094';
UPDATE Tabel_User SET ID_Kelompok = @k1 WHERE Username = 'E1E125097';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125002';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125090';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125073';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125085';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125049';
UPDATE Tabel_User SET ID_Kelompok = @k2 WHERE Username = 'E1E125065';
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 1 METODE NUMERIK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 1', DATE_ADD(NOW(), INTERVAL 1 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 69, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 99, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), @mod_id, 'Alpa', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 75, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 90, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 98, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 2 METODE NUMERIK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 2', DATE_ADD(NOW(), INTERVAL 2 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 88, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 93, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 42, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 85, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 65, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 60, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 3 METODE NUMERIK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 3', DATE_ADD(NOW(), INTERVAL 3 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 61, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 56, 'Ulangi', 'Revisi');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 89, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 86, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 72, 'Tingkatkan', 'Sanggah', 'Mohon dikoreksi ulang kak');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 78, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123109'), 81, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 91, 'Bagus', 'Selesai');
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES ('Modul 4 METODE NUMERIK', 'modul.pdf');
SET @mod_id = LAST_INSERT_ID();
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (@mod_id, 'Tugas 4', DATE_ADD(NOW(), INTERVAL 4 WEEK));
SET @task_id = LAST_INSERT_ID();
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125036'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125057'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 97, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125005'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125079'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125094'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125097'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 92, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125002'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 84, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125090'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), @mod_id, 'Hadir', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125073'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123103'), 82, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), @mod_id, 'Sakit', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125085'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123130'), 76, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), @mod_id, 'Izin', NOW());
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES (@task_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125049'), 'tugas.zip', NOW());
SET @sub_id = LAST_INSERT_ID();
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES (@sub_id, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E123122'), 96, 'Bagus', 'Selesai');
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES ((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E125065'), @mod_id, 'Izin', NOW());
