# JARA — Advanced To-Do List

Aplikasi web manajemen tugas untuk mengelola tugas pribadi maupun tim. Pengguna dapat membuat, mengelompokkan, dan mengatur tugas ke dalam daftar/proyek, menetapkan prioritas dan tenggat waktu, menandai tugas selesai, menambahkan kolaborator, dan memantau progres tugas secara visual. Sistem dilengkapi manajemen akun oleh Admin, transaksi atomik untuk integritas data daftar, otorisasi ketat, serta perlindungan injeksi SQL menggunakan query terparameterisasi (*prepared statements*).

## User Story

- **Sebagai Admin**, saya ingin menambah dan menghapus akun pengguna dalam sistem agar akses akun pengguna dapat dikelola dengan aman dan terkontrol.
- **Sebagai Pengguna (Owner)**, saya ingin membuat daftar tugas baru (otomatis menjadi pemilik), menghapus daftar tugas beserta seluruh tugas dan anggotanya secara atomik, serta menambahkan pengguna lain ke dalam daftar agar dapat berkolaborasi mengerjakan tugas bersama.
- **Sebagai Pengguna (Owner/Member)**, saya ingin membuat, mengelompokkan, mengatur prioritas dan tenggat waktu tugas, menandai tugas sebagai selesai, serta memantau progres penyelesaian tugas dalam daftar agar aktivitas proyek terpantau dengan jelas.

## Demo

Aplikasi dapat diakses secara lokal melalui browser pada alamat:  
`http://127.0.0.1:8000` (atau via virtual host Laragon di `http://ppk-projects.test`)

## Daftar SRS

| Kode | Deskripsi | Acceptance Criteria |
| :--- | :--- | :--- |
| **SRS-001** | Autentikasi pengguna & otorisasi hak akses (*Role-Based Access Control*). | - Pengguna dapat login dan logout dengan aman menggunakan kredensial terenkripsi.<br>- Permintaan dari pengguna yang tidak berwenang wajib ditolak (*unauthorized request rejected*).<br>- Pemisahan akses ketat antara Admin dan Pengguna reguler. |
| **SRS-002** | Manajemen akun pengguna oleh Administrator (*Add & Delete User*). | - Admin dapat melihat daftar seluruh pengguna dalam sistem.<br>- Admin dapat menambahkan akun pengguna baru melalui form yang divalidasi.<br>- Admin dapat menghapus akun pengguna dari sistem.<br>- Pengguna non-admin diblokir dari akses rute/tindakan manajemen pengguna. |
| **SRS-003** | Pembuatan daftar tugas (*Create List*) dengan penetapan kepemilikan otomatis. | - Pengguna dapat membuat daftar tugas (*list/project*) baru dengan mengisi nama/judul daftar.<br>- Pengguna pembuat secara otomatis tercatat sebagai pemilik sah (*Owner*) daftar tersebut.<br>- Input divalidasi dan diproses dengan *prepared statement*. |
| **SRS-004** | Penghapusan daftar tugas (*Delete List*) secara atomik beserta *cascade cleanup*. | - Hanya pemilik daftar (*Owner*) yang memiliki hak untuk menghapus daftar.<br>- Proses penghapusan berjalan secara atomik (menggunakan transaksi database).<br>- Menghapus daftar otomatis menghapus seluruh tugas dan status keanggotaan kolaborator di dalamnya.<br>- Jika salah satu langkah penghapusan gagal, seluruh transaksi dibatalkan (*rollback*) tanpa ada data tersisa/inkonsisten. |
| **SRS-005** | Manajemen kolaborator daftar tugas (*Add Member to List*). | - Pemilik daftar (*Owner*) dapat menambahkan pengguna lain yang terdaftar ke dalam daftarnya.<br>- Pengguna yang ditambahkan terdaftar sebagai anggota (*Member*) dan dapat mengakses daftar tersebut.<br>- Permintaan penambahan kolaborator dari non-owner ditolak. |
| **SRS-006** | Pengelolaan tugas (*Task CRUD*), pengelompokan, prioritas, dan tenggat waktu. | - Anggota dan pemilik daftar dapat membuat tugas baru di dalam daftar.<br>- Pengguna dapat mengelompokkan tugas (kategori/grup).<br>- Setiap tugas dapat diatur tingkat prioritasnya (*Low, Medium, High, Urgent*) dan tenggat waktunya (*due date*).<br>- Input tugas divalidasi dan diproses via *prepared statement*. |
| **SRS-007** | Penandaan tugas selesai (*Task Completion Toggle*). | - Pengguna dapat menandai tugas sebagai selesai (*completed*) dengan satu kali klik (checkbox/toggle).<br>- Pengguna dapat mengembalikan status tugas menjadi belum selesai (*pending*).<br>- Perubahan status langsung tersimpan ke database. |
| **SRS-008** | Pemantauan progres penyelesaian tugas (*Visual Progress Bar & Counter*). | - Terdapat indikator visual persentase dan/atau rasio tugas selesai berbanding total tugas di header daftar.<br>- Nilai progres terhitung otomatis secara dinamis saat tugas ditambahkan, dihapus, atau diubah status penyelesaiannya. |
| **SRS-009** | Validasi input menyeluruh & mitigasi SQL Injection. | - Seluruh input dari pengguna (form user, list, task) divalidasi ketat di sisi backend sebelum diproses.<br>- Seluruh operasi query database wajib menggunakan query terparameterisasi (*prepared statement* / Eloquent parameter binding) untuk mencegah serangan SQL Injection. |

## Menjalankan Proyek

Pastikan telah menginstal PHP (>= 8.2), Composer, serta MySQL/Laragon di komputer Anda.

```bash
# 1. Clone repository
git clone https://github.com/tupperwureism/PPK-Project-1.git
cd "PPK PROJECTS"

# 2. Instal dependensi backend
composer install

# 3. Setup file environment
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi & seeder database
php artisan migrate --seed

# 5. Jalankan server lokal
php artisan serve
```

## Akun Demo & Pengujian

Setelah menjalankan `php artisan migrate --seed`, Anda dapat menggunakan akun bawaan berikut untuk login dan menguji aplikasi:

| Peran (Role) | Nama Pengguna | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | Admin JARA | `admin@jara.test` | `password` | Mengelola akun pengguna (melihat, menambah, dan menghapus user) |
| **User (Owner)** | User JARA Satu | `user1@jara.test` | `password` | Membuat daftar tugas, kelola tugas, dan undang anggota |
| **User (Member)** | User JARA Dua | `user2@jara.test` | `password` | Kolaborasi mengerjakan tugas pada daftar yang dibagikan |
| **User (Member)** | User JARA Tiga | `user3@jara.test` | `password` | Kolaborasi mengerjakan tugas pada daftar yang dibagikan |

## Struktur Folder

```text
PPK PROJECTS/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controller penanganan request (Admin, List, Task)
│   │   └── Middleware/        # Middleware otorisasi dan proteksi role
│   └── Models/                # Model data (User, TodoList, Task, Member)
├── database/
│   ├── migrations/            # Skema database & relasi foreign key cascade
│   └── seeders/               # Dummy data untuk pengujian role Admin & User
├── resources/
│   └── views/                 # Template antarmuka pengguna (Blade templates)
├── routes/
│   └── web.php                # Definisi rute web dan pengelompokan hak akses
├── public/                    # Aset statis aplikasi
├── .env.example               # Contoh konfigurasi environment
├── composer.json              # Definisi dependensi PHP
└── README.md                  # Dokumentasi proyek
```
