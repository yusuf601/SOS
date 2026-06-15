# Analisis Konsistensi DFD Level 0 (Revisi) vs. Flowmap

Laporan ini menyajikan hasil verifikasi konsistensi terbaru setelah memeriksa file revisi diagram **DFD Level 0**.

## Sumber Data
* **DFD Level 0 (Revisi)**: [narkoba/dfd_temp/DFD LEVEL 0_revision.pdf](file:///home/kali/utils/rpl/narkoba/dfd_temp/DFD%20LEVEL%200_revision.pdf)
* **Flowmap (Acuan)**: [narkoba/Flow Map.pdf](file:///home/kali/utils/rpl/narkoba/Flow%20Map.pdf)

---

## Ringkasan Perbandingan Entitas & Penyimpanan Data

| Komponen | Di Flowmap | Di DFD Level 0 (Revisi) | Status Konsistensi |
| :--- | :--- | :--- | :--- |
| **Entitas/Aktor** | Mahasiswa, Asisten, Dosen, Sistem | Mahasiswa, Asisten Praktikum, Dosen/Koordinator | **Konsisten** |
| **Data Store (DB)** | `DB_Pengguna` | `D1: Data Pengguna` | **Konsisten** |
| **Data Store (DB)** | `DB_Kelas` | `D2: Data Kelas` | **Konsisten** |
| **Data Store (DB)** | `DB_Kelompok` | *Tidak ada* | **Tidak Konsisten** (Struktur kelompok masuk ke D2, padahal di Flowmap terpisah) |
| **Data Store (DB)** | `DB_Modul` | `D3: Data Modul` | **Konsisten** |
| **Data Store (DB)** | `DB_Tugas` | `D4: Data Tugas` | **Konsisten** |
| **Data Store (DB)** | `DB_Nilai` | `D5: Data Nilai` | **Konsisten** |
| **Data Store (DB)** | `DB_Presensi` | `D6: Data Presensi` | **Konsisten** |
| **Data Store (DB)** | `DB_Nilai_Akhir` | `D7: Data Rekap Nilai` | **Konsisten** |
| **Data Store (DB)** | `DB_Sanggahan` | `D8: Data Sanggahan` | **Konsisten** |

---

## Perkembangan Perbaikan (Apa yang Sudah Benar & Apa yang Belum)

### Yang Sudah Diperbaiki (Berhasil Diselaraskan)
1. **Manajemen Hak Akses (Proses 1.0) - LOGIN**:
   * **Sebelumnya**: Hanya Mahasiswa yang digambarkan login.
   * **Revisi**: **Selesai**. DFD sekarang sudah menambahkan aliran masuk `Data login` dan aliran keluar `Hak Akses` untuk entitas **Asisten Praktikum** dan **Dosen/Koordinator**.
2. **Bank Modul & Materi (Proses 3.0) - UPLOAD**:
   * **Sebelumnya**: Dosen tidak terhubung ke Proses 3.0 untuk mengunggah materi.
   * **Revisi**: **Selesai**. Ditambahkan aliran data **"Upload Modul/Materi"** dari **Dosen/Koordinator** ke Proses 3.0.

---

### Yang Masih Perlu Diperbaiki (Belum Konsisten)
1. **Manajemen Kelas & Kelompok (Proses 2.0)**:
   * **Masalah**: 
     * Masih belum ada alur dari **Dosen/Koordinator** ke **Proses 2.0** untuk membuat kelas atau kelompok praktikum.
     * Masih terdapat garis aliran langsung ilegal dari **Dosen/Koordinator** ke data store **`D2: Data Kelas`** (secara aturan DFD, entitas luar tidak boleh langsung mengakses data store tanpa melalui lingkaran proses).
   * **Solusi**: Hubungkan aliran data "Buat Kelas & Kelompok" dari Dosen ke **Proses 2.0**, lalu hapus garis langsung dari Dosen ke **`D2`**.
2. **Pembaruan Nilai Sanggah pada Sanggah Nilai (Proses 8.0)**:
   * **Masalah**: Di Flowmap, jika sanggahan disetujui, sistem akan melakukan **Update Nilai** pada `DB_Nilai`. Di DFD, **Proses 8.0** belum memiliki hubungan/panah output ke data store **`D5: Data Nilai`** untuk memperbarui data nilai mahasiswa.
   * **Solusi**: Tambahkan panah aliran data dari **Proses 8.0** menulis/meng-update ke **`D5: Data Nilai`**.
3. **Logika Alur Presensi (Proses 6.0)**:
   * **Masalah**: Di Flowmap, asisten praktikum yang aktif menginput presensi mahasiswa ("Pilih Status Kehadiran"). Di DFD, Mahasiswa digambarkan mengirim data "Presensi" ke Proses 6.0.
   * **Solusi**: Jika presensi diisi oleh Asisten (sesuai Flowmap), aliran data "Presensi" dari Mahasiswa ke Proses 6.0 sebaiknya diubah menjadi output "Lihat Presensi" dari sistem ke Mahasiswa, sedangkan input presensi dihubungkan dari Asisten ke Proses 6.0.

---

## Analisis Progress Terkini
Dengan diperbaikinya alur login (Proses 1.0) untuk semua aktor dan alur upload materi (Proses 3.0) oleh Dosen, progress konsistensi DFD Level 0 meningkat:

* **Sebelum Revisi**: **~67%** (26 dari 39 aliran data utama sesuai)
* **Setelah Revisi**: **~80%** (31 dari 39 aliran data utama sesuai)
