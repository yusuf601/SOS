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
-- 5. TESTING/SELECTION DATA INITIALIZATION
-- ==========================================

-- Insert Dummy Kelas (Classes)
INSERT INTO Tabel_Kelas (Nama_Kelas) VALUES
('Praktikum Pemrograman Web'),
('Praktikum Basis Data'),
('Praktikum Jaringan Komputer');

-- Insert Dummy Kelompok (Groups)
INSERT INTO Tabel_Kelompok (Nama_Kelompok, ID_Kelas) VALUES
('Kelompok 1 (Kel. 1)', 1), -- Kelompok 1 for Web
('Kelompok 2 (Kel. 2)', 3), -- Kelompok 2 for Network
('Kelompok 3 (Kel. 3)', 2); -- Kelompok 3 for Database

-- Insert Dummy User (Mahasiswa & Asisten)
-- Password: 'password' (bcrypt hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('E1E122001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'John Doe', 1), -- ID: 17
('E1E122002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Chris Redfield', NULL), -- ID: 18
('E1E122003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Jane Smith', 2); -- ID: 19

-- Plotting Assistant (Chris Redfield is assistant for Web Class)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES
(18, 1);

-- Insert Dummy Modul
INSERT INTO Tabel_Modul (Judul_Modul, File_Materi) VALUES
('Dasar HTML & Struktur Web', 'materi_html.pdf'),
('CSS Styling & Layouting Flexbox', 'materi_css.pdf'),
('CSS Grid & Responsive Design', 'materi_grid.pdf');

-- Insert Dummy Attendance (John Doe has been present 2 times, sick 1 time -> 66.7% presence)
INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) VALUES
(17, 1, 'Hadir', '2026-06-01'),
(17, 2, 'Hadir', '2026-06-08'),
(17, 3, 'Sakit', '2026-06-15');

-- Insert Dummy Tasks (Tugas)
INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES
(1, 'Buatlah struktur halaman biodata diri menggunakan HTML5 semantik.', '2026-06-07 23:59:59'),
(2, 'Buatlah layout web responsive sederhana menggunakan CSS Flexbox.', '2026-06-14 23:59:59'),
(3, 'Buatlah layout grid portofolio modern menggunakan CSS Grid.', '2026-06-21 23:59:59');

-- Insert Dummy Submissions (John Doe has submitted Task 1 and Task 2)
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES
(1, 17, 'tugas1_john_doe.zip', '2026-06-06 14:30:00'),
(2, 17, 'tugas2_john_doe.zip', '2026-06-13 18:45:00');

-- Insert Dummy Grades (Task 1 is graded: 85.5, Task 2 is pending/ungraded)
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES
(1, 18, 85.50, 'Kerja bagus, HTML semantik ditulis dengan sangat rapi.', 'Selesai');

-- Insert Additional Dummy User for richer dashboard experience (Alice Margatroid)
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('E1E122004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Alice Margatroid', 1);

-- Insert Additional Submissions for Alice Margatroid
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES
(1, 20, 'tugas1_alice_margatroid.zip', '2026-06-06 15:00:00'),
(2, 20, 'tugas2_alice_margatroid.zip', '2026-06-13 19:00:00');

-- Insert Additional Grades (Alice's Task 1 has a dispute/sanggahan)
INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES
(3, 18, 70.00, 'Tugas lengkap, namun styling CSS masih kurang rapi.', 'Sanggah', 'Mohon maaf kak, sepertinya ada kekeliruan. Saya sudah menerapkan Flexbox sesuai instruksi modul. Mohon diperiksa kembali.');

-- ==============================================================================
-- 6. INDONESIAN DEMO DATA FOR LECTURER ADHA MASHUR SAJIAH (ID: 14)
-- ==============================================================================
-- Plot Dosen Adha Mashur Sajiah ke Kelas 1 (Web), Kelas 2 (Basis Data), dan Kelas 3 (Jaringan)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES
(14, 1),
(14, 2),
(14, 3);

-- Tambah Asisten Baru (Wowok)
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('E1E122005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Asisten', 'Wowok Hermawan', NULL);

-- Plot Asisten Wowok ke Kelas 2 (Basis Data) dan Kelas 3 (Jaringan)
INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES
((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122005'), 2),
((SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122005'), 3);

-- Tambah Mahasiswa Baru (Jokowi, Raka, Yusuf, Budi Santoso, Siti Aminah)
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('E1E122006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Jokowi', 1),
('E1E122007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Raka', 3),
('E1E122008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Yusuf', 3),
('E1E122009', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Budi Santoso', 2),
('E1E122010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Siti Aminah', 2);

-- Raka mengumpulkan Tugas 1 (Basis Data) dan dinilai oleh Asisten Wowok
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES
(1, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122007'), 'tugas1_raka.zip', '2026-06-14 20:00:00');

INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas) VALUES
((SELECT ID_Pengumpulan FROM Tabel_Pengumpulan WHERE File_Tugas = 'tugas1_raka.zip' LIMIT 1), 
 (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122005'), 
 88.00, 'Kueri SQL ditulis dengan sangat efisien dan benar.', 'Selesai');

-- Yusuf mengumpulkan Tugas 1 (Basis Data) dan mengajukan Sanggah atas nilai Wowok
INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) VALUES
(1, (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122008'), 'tugas1_yusuf.zip', '2026-06-06 17:00:00');

INSERT INTO Tabel_Nilai (ID_Pengumpulan, ID_Asisten, Nilai_Angka, Feedback, Status_Tugas, Alasan_Sanggah) VALUES
((SELECT ID_Pengumpulan FROM Tabel_Pengumpulan WHERE File_Tugas = 'tugas1_yusuf.zip' LIMIT 1), 
 (SELECT ID_User FROM Tabel_User WHERE Username = 'E1E122005'), 
 65.00, 'Ada kesalahan relasi di ERD.', 'Sanggah', 'Mohon maaf kak, relasi many-to-many sudah saya uraikan menjadi tabel perantara. Mohon diperiksa kembali.');

-- Data 5 Mahasiswa dummy yang tersedia (belum memiliki kelompok)
INSERT INTO Tabel_User (Username, Password, Role, Nama_Lengkap, ID_Kelompok) VALUES
('E1E121001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Ahmad Faisal', NULL),
('E1E121002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Siti Nurhaliza', NULL),
('E1E121003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Budi Santoso', NULL),
('E1E121004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Rina Melati', NULL),
('E1E121005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mahasiswa', 'Joko Anwar', NULL);
