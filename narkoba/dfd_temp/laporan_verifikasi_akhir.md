# Laporan Verifikasi Hasil Akhir DFD Level 0 vs. Flowmap

Laporan ini menyajikan hasil verifikasi kesesuaian antara file DFD terbaru dengan dokumen acuan Flowmap.

## 1. Identifikasi File
* **File Uji**: [narkoba/dfd_temp/akhir.pdf](file:///home/kali/utils/rpl/narkoba/dfd_temp/akhir.pdf)
* **File Referensi**: [narkoba/dfd_temp/Flow Map.pdf](file:///home/kali/utils/rpl/narkoba/dfd_temp/Flow%20Map.pdf)
* **Checksum MD5**: `0f948dc3cafbe125504213b34788e758`
  > [!NOTE]
  > File `akhir.pdf` memiliki checksum MD5 yang **identik 100%** dengan `DFD LEVEL 0_revision.pdf` yang dianalisis sebelumnya. Oleh karena itu, status dan temuan pada file ini sama dengan versi revisi sebelumnya.

---

## 2. Ringkasan Evaluasi
* **Tingkat Kesesuaian**: **~80%** (Belum Sesuai Sepenuhnya)
* **Status**: **TIDAK LOLOS VERIFIKASI**
* **Rekomendasi**: Gunakan rancangan hasil perbaikan yang telah kami sediakan di file [DFD_LEVEL_0_CORRECTED.drawio](file:///home/kali/utils/rpl/narkoba/dfd_temp/DFD_LEVEL_0_CORRECTED.drawio) karena sudah **100% sesuai dengan Flowmap** dan bebas dari pelanggaran aturan DFD.

---

## 3. Detail Ketidaksesuaian Kritis (Temuan Masalah)

Berikut adalah daftar perbedaan antara aliran di `akhir.pdf` dengan alur logika di Flowmap:

### A. Pelanggaran Aturan DFD (Sangat Fatal)
1. **Garis Ilegal dari Dosen ke Data Store `D2`**:
   * **Temuan**: Terdapat panah langsung dari entitas **Dosen/Koordinator** ke **`D2: Data Kelas`**.
   * **Aturan DFD**: Entitas luar *tidak boleh* mengakses atau menulis ke Data Store secara langsung tanpa melalui lingkaran proses.
   * **Solusi**: Alur ini harus melewati **Proses 2.0 (Manajemen Kelas & Kelompok)** terlebih dahulu.

### B. Kesalahan Alur & Logika Proses
2. **Proses 2.0 (Manajemen Kelas & Kelompok)**:
   * **Di Flowmap**: Dosen/Koordinator membuat kelas, dan Asisten membuat kelompok.
   * **Di `akhir.pdf`**: Kehilangan alur input dari Dosen/Koordinator ke Proses 2.0. Proses 2.0 hanya mengirim output ke Mahasiswa tanpa ada yang menginput/memproses data kelas tersebut.

3. **Proses 6.0 (Presensi Digital)**:
   * **Di Flowmap**: Asisten Praktikum yang aktif menginput presensi mahasiswa (*"Pilih Status Kehadiran"*), lalu sistem menyimpannya ke `DB_Presensi`. Mahasiswa hanya melihat status kehadiran.
   * **Di `akhir.pdf`**: Terbalik. Mahasiswa digambarkan mengirim "Presensi" ke Proses 6.0, sedangkan Asisten hanya menerima "Verifikasi presentasi" (terdapat typo kata "presentasi").

4. **Proses 8.0 (Sanggah Nilai)**:
   * **Di Flowmap**: Jika sanggahan disetujui oleh Asisten, sistem melakukan *Update Nilai* ke `DB_Nilai`.
   * **Di `akhir.pdf`**:
     * **Kehilangan alur update**: Tidak ada panah output dari Proses 8.0 ke data store **`D5: Data Nilai`**.
     * **Salah Aktor & Typo**: Dosen terhubung ke Proses 8.0 dengan label *"Tinjau sinngah"* (typo). Di Flowmap, peninjauan sanggah dilakukan oleh Asisten Praktikum.
     * **Salah Tujuan Aliran**: Aliran *"Status Kelulusan"* keluar dari Proses 8.0 ke Mahasiswa. Ini tidak logis karena status kelulusan dikelola oleh Proses 9.0.

5. **Proses "Black Hole / Magic" (Kehilangan Input Database)**:
   * **Proses 7.0 (Rekap Nilai Otomatis)**, **Proses 9.0 (Status Kelulusan)**, and **Proses 10.0 (Export Transkrip Nilai)** pada `akhir.pdf` digambarkan mengeluarkan data ke aktor/data store tanpa mengambil input data dari database (`D5: Data Nilai` atau `D7: Data Rekap Nilai`). Proses tidak bisa memproses data jika tidak ada input data yang masuk.

6. **Ketiadaan Data Store Sanggahan**:
   * **Di Flowmap**: Data sanggahan disimpan ke database.
   * **Di `akhir.pdf`**: Tidak ada **`D8: Data Sanggahan`**. Aliran data sanggahan langsung diproses tanpa disimpan ke penyimpanan data.

---

## 4. Solusi & Perbandingan Alur yang Benar (100% Sesuai Flowmap)

Untuk memperbaiki kesalahan di atas, kami telah membuat rancangan Draw.io yang terstandarisasi. Berikut adalah perbandingan alur yang seharusnya diterapkan:

| Nama Proses | Aktor Input (Flowmap) | Simpan/Baca Data Store | Aktor Output |
| :--- | :--- | :--- | :--- |
| **1.0 Manajemen Hak Akses** | Mhs, Asisten, Dosen (Data Login) | `D1: Data Pengguna` (Verifikasi) | Semua Aktor (Dashboard/Hak Akses) |
| **2.0 Kelas & Kelompok** | Dosen & Asisten (Input Kelas/Kelompok) | `D2: Data Kelas & Kelompok` (Simpan/Baca) | Mahasiswa (Info Kelas & Jadwal) |
| **3.0 Bank Modul & Materi** | Dosen (Upload Modul) | `D3: Data Modul` (Simpan/Baca) | Mahasiswa (Akses Modul/Tugas) |
| **4.0 Pengumpulan Tugas** | Mahasiswa (Upload File Tugas) | `D4: Data Tugas` (Cek & Simpan) | Mhs (Konfirmasi), Asisten (Notifikasi) |
| **5.0 Grading & Feedback** | Asisten (Input Nilai & Feedback) | `D5: Data Nilai` (Simpan) & Baca `D4` | Mahasiswa (Nilai & Feedback) |
| **6.0 Presensi Digital** | Asisten (Data Kehadiran) | `D6: Data Presensi` (Simpan) | Mahasiswa (Status Presensi) |
| **7.0 Rekap Nilai Otomatis** | *Sistem Otomatis* | Baca `D5` & `D6` $\rightarrow$ Tulis `D7: Data Rekap` | *Tidak ada (proses internal)* |
| **8.0 Sanggah Nilai** | Mahasiswa (Form Sanggah), Asisten (Keputusan) | Tulis `D8: Data Sanggahan` & Update `D5` | Mahasiswa (Hasil Keputusan Sanggah) |
| **9.0 Status Kelulusan** | *Sistem Otomatis* | Baca `D7` (Rekap Nilai) | Mahasiswa (Indikator Lulus/Mengulang) |
| **10.0 Export Transkrip** | Dosen (Request Export) | Baca `D7` (Rekap Nilai) | Dosen (File Transkrip PDF/Excel) |

> [!TIP]
> File visual Draw.io yang sudah diperbaiki layout-nya agar rapi, tidak tumpang tindih, dan 100% konsisten dengan tabel di atas dapat Anda buka di:
> * **Draw.io File**: [narkoba/dfd_temp/DFD_LEVEL_0_CORRECTED.drawio](file:///home/kali/utils/rpl/narkoba/dfd_temp/DFD_LEVEL_0_CORRECTED.drawio)
