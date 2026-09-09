# JARA — Advanced To-Do List (Pribadi & Kolaborasi Tim)

> **Aplikasi Web Manajemen Tugas Modern Berbasis Laravel**  
> Repositori Resmi: [tupperwureism/PPK-Project-1](https://github.com/tupperwureism/PPK-Project-1)

---

## 1. Tentang Proyek

**JARA** adalah aplikasi web manajemen tugas (*to-do list*) yang dirancang untuk mengelola produktivitas individu maupun kolaborasi tim. 
* Pengguna dapat membuat dan mengelompokkan tugas ke dalam beberapa daftar (*list/project*).
* Menetapkan tingkat prioritas (*Low, Medium, High, Urgent*) dan tenggat waktu (*deadline*).
* Menandai tugas yang telah selesai serta memantau persentase progres penyelesaian tugas secara visual.
* Pemilik daftar (*List Owner*) dapat menambahkan pengguna lain ke dalam daftar tugasnya untuk berkolaborasi bersama.
* **Administrator (Admin)** bertanggung jawab mengelola akun pengguna (melihat, menambah, dan menghapus akun pengguna dalam sistem).

---

## 2. Fitur Utama (Spesifikasi Sistem)

### 👤 Modul Autentikasi & Manajemen Akun (Admin)
* **Autentikasi Pengguna**: Login dan Logout yang aman dengan enkripsi password dan proteksi sesi.
* **Role-Based Access Control**: Pemisahan hak akses antara Administrator dan Pengguna biasa.
* **Kelola Pengguna**: Admin dapat melihat daftar seluruh pengguna, mendaftarkan akun baru, serta menghapus akun pengguna.

### 📁 Modul Daftar Tugas (List/Project) & Kolaborasi Tim
* **Manajemen List**: Pengguna dapat membuat, melihat, dan menghapus daftar/proyek tugas.
* **Kolaborasi Tim**: Pemilik daftar dapat mengundang pengguna lain ke dalam list untuk bekerja bersama.
* **Pemisahan Hak Akses**: Pengguna dapat mengelola daftar milik pribadi maupun melihat daftar yang dibagikan kepadanya.

### ✅ Modul Tugas (Task) & Pemantauan Progres
* **Atribut Tugas Lengkap**: Setiap tugas dilengkapi judul, tingkat prioritas (*Low, Medium, High, Urgent*), dan batas waktu pengerjaan (*due date*).
* **Status Selesai**: Checklist instan untuk menandai tugas yang telah rampung (*completed*) atau mengembalikannya ke pending.
* **Visual Progress Bar**: Indikator persentase otomatis yang memperlihatkan progres penyelesaian tugas di dalam setiap daftar.

---

## 3. Kebutuhan Non-Fungsional & Keamanan

* **Tampilan Responsif**: Antarmuka bersih, cepat, dan nyaman diakses baik dari browser desktop maupun perangkat seluler.
* **Keamanan Data**: Enkripsi password menggunakan algoritma hashing bawaan Laravel (`bcrypt`/`argon2id`), proteksi token CSRF pada seluruh form, dan sanitasi input via Eloquent ORM.
* **Integritas Relasi Data**: Relasi database terstruktur rapi dengan *cascade delete* (penghapusan list otomatis membersihkan tugas di dalamnya).

---

## 4. Tumpukan Teknologi (Tech Stack)

* **Framework**: Laravel 11 / 13 (PHP 8.3)
* **Frontend**: Blade Templating Engine, Tailwind CSS
* **Database**: SQLite (Development) / MySQL (Laragon)
* **Testing**: PHPUnit / Automated Feature Tests

---

## 5. Panduan Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

```bash
# 1. Clone repositori
git clone https://github.com/tupperwureism/PPK-Project-1.git
cd "PPK PROJECTS"

# 2. Instal dependensi backend
composer install

# 3. Setup file konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi database
php artisan migrate

# 5. Jalankan server lokal
php artisan serve
```

Akses aplikasi melalui browser Anda pada URL: `http://127.0.0.1:8000` (atau via virtual host Laragon di `http://ppk-projects.test`).
