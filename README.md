# Sistem Pendaftaran Pelajar Tahfiz Ainuddin

Sistem web pendaftaran pelajar secara atas talian untuk **Tahfiz Ainuddin**. Sistem ini merangkumi borang pendaftaran berperingkat (Wizard) bagi pelajar/penjaga dan panel kawalan pentadbir (Admin Dashboard) bagi tujuan semakan dan kelulusan permohonan.

## 🚀 Ciri-Ciri Utama

### 1. Borang Pendaftaran Berperingkat (Wizard - 6 Langkah)
* **Langkah 1: Maklumat Pelajar** - Borang maklumat peribadi wajib dengan pengesahan format No. Kad Pengenalan secara automatik.
* **Langkah 2: Maklumat Penjaga** - Borang maklumat bapa dan ibu/penjaga.
* **Langkah 3: Akademik & Hafazan** - Kemasukan sekolah terdahulu, tahap penguasaan Al-Quran, status khatam, surah hafazan, serta input dinamik subjek akademik/agama.
* **Langkah 4: Maklumat Kesihatan** - Pengisytiharan alahan, penyakit kronik, pengambilan ubat (dengan togol interaktif "Ada/Tiada"), nombor telefon kecemasan, dan kebenaran rawatan.
* **Langkah 5: Muat Naik Dokumen** - Sokongan fail PDF dan Imej (PNG/JPG) bagi kad pengenalan dan sijil-sijil akademik dengan fungsi tambah/buang sijil dinamik dan preview visual secara langsung (instant preview).
* **Langkah 6: Semak & Hantar** - Paparan ringkasan permohonan sebelum penghantaran rasmi dibuat.

### 2. Draf Permohonan ("Simpan & Keluar")
* Pemohon boleh menyimpan draf permohonan pada mana-mana langkah wizard (Langkah 1 hingga 5).
* Validasi medan wajib dan format fail dilepaskan semasa mod draf, membolehkan penyimpanan data secara separa.
* Pemohon boleh menyambung semula draf permohonan terus dari Dashboard utama.

### 3. Panel Kawalan Pentadbir (Admin Dashboard)
* **Senarai Permohonan**: Semakan status permohonan (Draf, Diterima, Ditolak, Diluluskan, Dalam Proses).
* **Perincian Permohonan**: Semakan terperinci data pelajar/penjaga, kesihatan, subjek akademik/agama, serta paparan fail dokumen (IC/Gambar dipaparkan terus, PDF boleh dibuka di tab baru).
* **Kelulusan Berkelompok & Selamat (Transaction Safe)**: Proses kelulusan menggunakan transaksi pangkalan data (`PDO transaction`) bagi memastikan integriti data semasa penjanaan No. Pelajar rasmi.

### 4. Input Telefon Lebih Mesra Pengguna (UX Spacing)
* Pemformatan nombor telefon automatik secara *real-time* mengikut piawaian Malaysia (cth: `12 345 6789` atau `11 1234 5678`).
* Pembersihan kod negara `+60` dan sifar awalan secara automatik semasa menaip.

---

## 🛠️ Stack Teknologi

* **Bahasa Pengaturcaraan**: PHP (Vanilla)
* **Pangkalan Data**: MySQL / MariaDB
* **Reka Bentuk & Antaramuka**: HTML5, CSS3 (Vanilla), JavaScript (Vanilla)
* **Seni Bina**: MVC (Model-View-Controller) dengan router berpusat (`index.php`)

---

## 💻 Cara Pemasangan & Penyediaan

### 1. Keperluan Sistem
* XAMPP / WampServer (menyokong PHP 8.0 ke atas dan MySQL)
* Git

### 2. Langkah Penyediaan
1. **Klon / Salin Fail Projek**:
   Letakkan folder projek ini di dalam direktori `htdocs` anda (contoh: `C:\xampp\htdocs\ainuddin-registration\`).
2. **Import Pangkalan Data**:
   * Buka phpMyAdmin (`http://localhost/phpmyadmin/`).
   * Cipta pangkalan data baru bernama `ainuddin_registration`.
   * Import fail SQL yang disediakan secara berasingan (skema pangkalan data).
3. **Konfigurasi Sambungan**:
   Konfigurasi sambungan database terdapat di dalam fail `config/database.php`. Secara lalai, ia dikonfigurasikan untuk XAMPP:
   ```php
   $host = "localhost";
   $dbname = "ainuddin_registration";
   $username = "root";
   $password = "";
   ```

### 3. Log Masuk Lalai (Default Accounts)
* **Akaun Pentadbir (Admin)**:
  * Emel: `admin@gmail.com`
* **Akaun Pelajar (Demo)**:
  * Emel: `niko@gmail.com`

---

## 🔒 Privasi Data & Keselamatan
* Fail pangkalan data (`database/`) dan dokumen sulit (`public/uploads/`) yang dimuat naik oleh pelajar diisytiharkan di dalam `.gitignore` untuk mengelakkan kebocoran data sulit di GitHub.
