# Analisis Konsistensi & Kelayakan DFD Level 1 vs. Flowmap

Laporan ini menyajikan hasil verifikasi untuk diagram **DFD Level 1** pada proyek sistem praktikum.

## Sumber Data
* **DFD Level 1**: [narkoba/dfd_temp/DFD LEVEL 1.pdf](file:///home/kali/utils/rpl/narkoba/dfd_temp/DFD%20LEVEL%201.pdf)
* **Flowmap (Acuan)**: [narkoba/Flow Map.pdf](file:///home/kali/utils/rpl/narkoba/Flow%20Map.pdf)

---

## Ringkasan Penilaian DFD Level 1

Secara keseluruhan, **DFD Level 1** saat ini masih sangat jauh dari kata siap, dengan tingkat kemajuan sekitar **25% - 30%** menuju 100%. Ditemukan kesalahan fatal berupa *copy-paste* label dan hilangnya separuh proses utama dari diagram.

### 1. Masalah Kelengkapan (Missing Processes)
DFD Level 1 seharusnya mendekomposisi seluruh proses utama yang ada di DFD Level 0 (total 10 proses). Namun, pada file PDF saat ini, **hanya ada 5 proses** yang dijabarkan. **5 proses lainnya hilang sepenuhnya**:
* **Proses 2.0 (Manajemen Kelas & Kelompok)** - *Tidak ada*
* **Proses 3.0 (Bank Modul & Materi)** - *Tidak ada*
* **Proses 8.0 (Sanggah Nilai)** - *Tidak ada*
* **Proses 9.0 (Status Kelulusan)** - *Tidak ada*
* **Proses 10.0 (Export Transkrip Nilai)** - *Tidak ada*

---

### 2. Evaluasi Detail per Proses yang Ada

#### **Proses 1.0 Manajemen Hak Akses** (Konsistensi: ~40%)
* **Temuan**: Dekomposisi proses (1.1 Verifikasi Login, 1.2 Penentuan Hak Akses, 1.3 Kelola Data Pengguna) secara struktur sudah cukup baik.
* **Kesalahan**: 
  * Aktor **Dosen** sama sekali tidak digambarkan di sini (hanya Mahasiswa dan Asisten). Dosen juga memerlukan login dan penentuan hak akses.
  * Aliran data dari proses 1.3 mengarah ke Asisten dengan label "kelola pengguna konfirmasi", padahal Asisten adalah pengguna biasa yang menerima hak akses, bukan pengelola pengguna.

#### **Proses 4.0 Pengumpulan Tugas** (Konsistensi: ~75%)
* **Temuan**: Alurnya cukup runtut (4.1 Cek Deadline -> 4.2 Upload -> 4.3 Validasi & Rekam).
* **Kesalahan**: 
  * Proses **4.1 Cek Deadline Tugas** memerlukan data tenggat waktu untuk dibandingkan. Namun, tidak ada panah input dari data store tugas (`D4` atau `D3`) menuju proses 4.1.

#### **Proses 5.0 Grading & Feedback** (Konsistensi: **0% - FATAL ERROR**)
* **Temuan**: Terjadi kesalahan fatal dalam pembuatan diagram (*copy-paste error*).
* **Kesalahan**: 
  * Semua lingkaran sub-proses di bawah judul "Proses 5.0 Grading & Feedback" berlabel **"4.1 Cek Deadline Tugas"** (terduplikasi dari Proses 4.0).
  * Penomoran dan fungsinya salah total dan tidak menggambarkan aktivitas penilaian asisten sama sekali.
* **Rekomendasi**: Ubah lingkaran proses menjadi:
  * **5.1 Verifikasi Tugas** (Menerima tugas dari `D4` dan divalidasi Asisten).
  * **5.2 Input Nilai & Feedback** (Asisten menginput nilai, disimpan ke `D5`).
  * **5.3 Tampilkan Nilai & Feedback** (Mengirimkan nilai ke Mahasiswa).

#### **Proses 6.0 Presensi Digital** (Konsistensi: ~30%)
* **Temuan**: Ada sub-proses 6.1 Absen Mahasiswa, 6.2 Verifikasi Presensi Asisten, dan 6.3 Rekam & Kirim Notifikasi.
* **Kesalahan**:
  * Di Flowmap, yang melakukan input data kehadiran adalah **Asisten** ("Pilih Status Kehadiran"). Namun di DFD Level 1 ini, Mahasiswa mengirim "data presensi" ke sub-proses 6.1, sedangkan Asisten hanya melakukan verifikasi di 6.2. Ini tidak konsisten dengan Flowmap.

#### **Proses 7.0 Rekap Nilai Otomatis** (Konsistensi: ~90%)
* **Temuan**: Struktur sub-proses (7.1 Ambil Data, 7.2 Hitung Nilai Akhir, 7.3 Simpan Rekap) sudah logis dan terhubung dengan benar ke data store terkait (`D5`, `D6`, `D7`).

---

## Langkah Perbaikan Utama untuk Mencapai 100%

1. **Buat Dekomposisi untuk 5 Proses yang Hilang**:
   * **Proses 2.0**: Jabarkan pembuatan kelas oleh dosen, pembuatan kelompok, dan plotting asisten.
   * **Proses 3.0**: Jabarkan unggah modul oleh dosen dan akses materi oleh mahasiswa.
   * **Proses 8.0**: Jabarkan pengajuan sanggah oleh mahasiswa, peninjauan oleh asisten, keputusan sanggah, dan update nilai akhir ke `D5`.
   * **Proses 9.0**: Jabarkan pengecekan nilai akhir terhadap ambang batas kelulusan.
   * **Proses 10.0**: Jabarkan permohonan ekspor oleh dosen dan pembuatan file transkrip.
2. **Koreksi Copy-Paste Label pada Proses 5.0**:
   * Ubah semua lingkaran proses 5.0 agar sesuai dengan fungsinya (5.1 Verifikasi, 5.2 Input Nilai, 5.3 Kirim Feedback).
3. **Masukkan Dosen pada Proses 1.0 (Login)**:
   * Tambahkan entitas Dosen/Koordinator pada dekomposisi login di Proses 1.0.
4. **Seleraskan Alur Presensi (Proses 6.0) dengan Flowmap**:
   * Sesuaikan agar Asisten menjadi pemberi input utama data presensi.
