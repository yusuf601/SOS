Bertindaklah sebagai Database Architect senior. Buatkan script SQL (DDL) lengkap untuk database MySQL/MariaDB yang akan digunakan untuk backend PHP native. Sistem ini adalah platform manajemen tugas dan penilaian praktikum. 

Desain database harus menggunakan NAMA TABEL dan KOLOM yang persis saya berikan di bawah ini, serta mematuhi aturan relasi (Foreign Key):

1. TABEL MASTER:
- `Tabel_User` (ID_User INT PK, Username VARCHAR, Password VARCHAR, Role ENUM('Mahasiswa', 'Asisten', 'Dosen'), Nama_Lengkap VARCHAR).
- `Tabel_Kelas` (ID_Kelas INT PK, Nama_Kelas VARCHAR).
- `Tabel_Kelompok` (ID_Kelompok INT PK, Nama_Kelompok VARCHAR, ID_Kelas INT FK ke Tabel_Kelas).
- `Tabel_Modul` (ID_Modul INT PK, Judul_Modul VARCHAR, File_Materi VARCHAR).

2. TABEL TRANSAKSIONAL:
- `Tabel_Presensi` (ID_Presensi INT PK, ID_User INT FK ke Tabel_User, ID_Modul INT FK ke Tabel_Modul, Status_Kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alpa'), Tanggal DATE).
- `Tabel_Tugas` (ID_Tugas INT PK, ID_Modul INT FK ke Tabel_Modul, Instruksi_Tugas TEXT, Deadline_Upload DATETIME).
- `Tabel_Pengumpulan` (ID_Pengumpulan INT PK, ID_Tugas INT FK ke Tabel_Tugas, ID_User INT FK ke Tabel_User, File_Tugas VARCHAR, Waktu_Submit DATETIME).
- `Tabel_Nilai` (ID_Nilai INT PK, ID_Pengumpulan INT FK ke Tabel_Pengumpulan, ID_Asisten INT FK ke Tabel_User, Nilai_Angka DECIMAL(5,2), Feedback TEXT, Status_Tugas ENUM('Selesai', 'Sanggah', 'Revisi')).

ATURAN OUTPUT CODE:
- Gunakan tipe data yang paling efisien dan umum (contoh: VARCHAR(100) untuk nama, VARCHAR(255) untuk file/password).
- Definisikan `PRIMARY KEY` dan lengkapi dengan `AUTO_INCREMENT`.
- Buat RELASI `FOREIGN KEY` yang benar dengan opsi `ON DELETE CASCADE` atau `RESTRICT` yang logis antar tabel. Catatan khusus: `ID_Asisten` pada `Tabel_Nilai` adalah FK yang merujuk ke `ID_User` di `Tabel_User`.
- Cukup berikan output berupa raw SQL script (CREATE TABLE beserta susunan Foreign Key). 
- Jangan gunakan syntax ORM, murni SQL standar.
