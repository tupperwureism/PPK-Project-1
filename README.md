# JARA — Advanced To-Do List (Pribadi & Kolaborasi Tim)

> **Praktikum Pengembangan Perangkat Lunak (PPK) — Pertemuan 2 (Study Case Based)**  
> **Repository Resmi:** [tupperwureism/PPK-Project-1](https://github.com/tupperwureism/PPK-Project-1)  
> **Role PM:** Lead Project Manager & System Analyst  

---

## 1. Deskripsi Singkat Proyek

**JARA** adalah aplikasi web manajemen tugas (*to-do list*) yang dirancang untuk mengelola produktivitas pribadi maupun kolaborasi tim. 
* Pengguna dapat membuat, mengelompokkan, dan mengatur tugas ke dalam beberapa daftar (*list/project*).
* Menetapkan tingkat prioritas (*Low, Medium, High, Urgent*) dan tenggat waktu (*due date*).
* Menandai tugas yang telah selesai (*completed*) serta memantau persentase progres penyelesaian tugas.
* Pemilik daftar (*List Owner*) dapat menambahkan pengguna lain ke dalam daftar tugasnya untuk dikerjakan secara bersama-sama.
* **Administrator (Admin)** bertanggung jawab mengelola akun pengguna (melihat, menambah, dan menghapus akun pengguna dalam sistem).

---

## 2. Kebutuhan Fungsional & Non-Fungsional (SRS Ringkas)

### A. Kebutuhan Fungsional (Functional Requirements)

#### • Modul 1: Akun & Admin (Programmer 1)
* **FR-01**: Admin dapat melihat daftar seluruh akun pengguna yang terdaftar di sistem.
* **FR-02**: Admin dapat membuat akun pengguna baru (nama, email, password).
* **FR-03**: Admin dapat menghapus akun pengguna dari sistem.
* **FR-04**: Pengguna dan Admin dapat melakukan autentikasi masuk (*login*) dan keluar (*logout*).

#### • Modul 2: Daftar Tugas / List & Kolaborasi (Programmer 2)
* **FR-05**: Pengguna dapat membuat daftar tugas baru (misal: "Tugas Kuliah", "Projek Web").
* **FR-06**: Pengguna dapat melihat daftar *list* miliknya.
* **FR-07**: Pemilik list dapat menghapus *list* tugas miliknya.
* **FR-08**: Pemilik list dapat menambahkan teman/user lain ke dalam *list* untuk kolaborasi tim.

#### • Modul 3: Tugas (Task) & Pantauan Progres (Programmer 3)
* **FR-09**: Pengguna dapat menambahkan tugas ke dalam *list* (judul, prioritas, tenggat waktu).
* **FR-10**: Pengguna dapat mencentang (*checklist*) tugas sebagai selesai atau membatalkannya.
* **FR-11**: Pengguna dapat menghapus tugas dari dalam *list*.
* **FR-12**: Sistem menghitung dan menampilkan *progress bar* persentase penyelesaian tugas (misal: 3/5 tugas selesai = 60%).

### B. Kebutuhan Non-Fungsional (Non-Functional Requirements)
1. **Usability (Kemudahan Tampilan)**: Antarmuka bersih, tombol aksi jelas, dan nyaman dioperasikan di browser laptop.
2. **Security (Keamanan Dasar)**: Password akun pengguna terenkripsi aman menggunakan algoritma hashing bawaan Laravel.
3. **Data Integrity (Integritas Relasi)**: Jika sebuah list dihapus, seluruh data tugas di dalamnya otomatis ikut terhapus (*cascade on delete*).

---

## 3. Strategi Eksekusi Paralel & Trik Dummy Data (Wajib Dibaca Tim)

Waktu praktikum hanya **60 menit**. Ketiga programmer **HARUS bekerja secara BERSAMAAN (PARALEL)** di laptop masing-masing dan **DILARANG saling menunggu** (misal Programmer 2 menunggu login Programmer 1 selesai).

Gunakan trik **Dummy Data (Mocking)** berikut selama masa pengerjaan:

* **Programmer 1 (Akun & Admin)**: Bekerja mandiri membuat migration role, tabel user, dan form kelola user.
* **Programmer 2 (List & Kolaborasi)**: **TIDAK PERLU menunggu login selesai.** Saat menyimpan list baru, sementara isi kolom `user_id` dengan nilai *fallback* user ID 1:
  ```php
  $todoList->user_id = auth()->id() ?? 1;
  ```
* **Programmer 3 (Task & Progres)**: **TIDAK PERLU menunggu Programmer 2 selesai.** Saat membuat tugas baru, sementara isi kolom `todo_list_id` dengan nilai *fallback* list ID 1:
  ```php
  $task->todo_list_id = $request->todo_list_id ?? 1;
  ```

---

## 4. Matriks Pembagian Beban Kerja Sama Rata (1 : 1 : 1)

| Indikator Beban | Programmer 1<br>(Akun & Admin) | Programmer 2<br>(List & Kolaborasi) | Programmer 3<br>(Task & Progres) |
| :--- | :---: | :---: | :---: |
| **Nama Branch Git** | `feat/user-admin` | `feat/todo-list` | `feat/todo-task` |
| **Jumlah Migration** | 1 file (tambah `is_admin`) | 1 file (tabel `todo_lists`) | 1 file (tabel `tasks`) |
| **Jumlah Controller** | 1 file (`UserController.php`) | 1 file (`TodoListController.php`) | 1 file (`TaskController.php`) |
| **Jumlah View (Blade)**| 1 file (`users.blade.php`) | 1 file (`lists.blade.php`) | 1 file (`tasks.blade.php`) |
| **Method / Fungsi** | `index`, `store`, `destroy` | `index`, `store`, `addMember` | `store`, `toggle`, `destroy` |
| **Estimasi Pengerjaan**| **15 – 20 Menit** | **15 – 20 Menit** | **15 – 20 Menit** |
| **Tulis Tangan Flow** | 1 alur simpel | 1 alur simpel | 1 alur simpel |

---

## 5. Kartu Tugas Spesifik Tiap Programmer

### 👤 A. Programmer 1 (Branch: `feat/user-admin`) — Modul Akun & Admin
* **Fitur**: Tampilkan daftar user, form tambah user baru, dan tombol hapus user.
* **File Terkait**: Migration `add_is_admin_to_users_table`, `UserController.php`, `resources/views/users.blade.php`.
* **Contoh Commit Message (Head & Body)**:
  ```text
  feat: buat fitur manajemen user untuk admin

  - Menambahkan kolom is_admin pada tabel users
  - Membuat UserController dengan method index, store, dan destroy
  - Membuat tampilan users.blade.php untuk kelola pengguna
  ```
* **Bahan Tulis Tangan ke Asprak**:  
  *Alur input user baru -> UserController validasi input -> Simpan ke database users -> Tampil di tabel daftar pengguna.*

---

### 📋 B. Programmer 2 (Branch: `feat/todo-list`) — Modul List & Kolaborasi
* **Fitur**: Buat daftar list tugas, form tambah list, dan form undang teman ke list.
* **File Terkait**: Migration `todo_lists`, `TodoListController.php`, `resources/views/lists.blade.php`.
* **Contoh Commit Message (Head & Body)**:
  ```text
  feat: buat fitur kelola list tugas dan kolaborasi

  - Membuat migration tabel todo_lists
  - Membuat TodoListController untuk buat list dan tambah anggota
  - Membuat tampilan lists.blade.php untuk menampilkan daftar list
  ```
* **Bahan Tulis Tangan ke Asprak**:  
  *Alur input nama list -> TodoListController simpan ke database -> List tampil di layar & pemilik dapat menambah teman.*

---

### ✅ C. Programmer 3 (Branch: `feat/todo-task`) — Modul Tugas & Progres
* **Fitur**: Tambah tugas ke list (deadline & prioritas), centang selesai, dan progress bar persen.
* **File Terkait**: Migration `tasks`, `TaskController.php`, `resources/views/tasks.blade.php`.
* **Contoh Commit Message (Head & Body)**:
  ```text
  feat: buat fitur tugas dan indikator progress bar

  - Membuat migration tabel tasks dengan priority dan due_date
  - Membuat TaskController untuk toggle selesai dan hitung progress bar
  - Menambahkan progress bar persentase penyelesaian tugas pada view
  ```
* **Bahan Tulis Tangan ke Asprak**:  
  *Alur tambah tugas -> Centang selesai -> TaskController update status -> Progress bar persentase terupdate.*

---

## 6. Panduan Git & Standar Penilaian Asprak

Sesuai instruksi Asisten Praktikum, parameter penilaian tertinggi ada pada kerapian alur Git:
1. **Branching Wajib**: Setiap programmer wajib membuat branch baru dari repo yang telah di-clone (DILARANG push langsung ke `main`):
   ```bash
   git checkout -b <nama-branch-kalian>
   ```
2. **Commit Message (Head & Body)**: Commit wajib memiliki judul singkat (*Head*) dan rincian perubahan (*Body*).
3. **Pure Commit**: Dilarang menggunakan *co-author*, commit harus murni dari akun masing-masing programmer.
4. **Push Branch**: Jika selesai, push branch kalian ke GitHub:
   ```bash
   git push -u origin <nama-branch-kalian>
   ```
5. **Tulis Tangan Akhir**: Di 15 menit terakhir praktikum, tiap programmer wajib menulis tangan alur kode yang telah dibuat untuk dikumpulkan ke asprak.

---

## 7. Cara Menjalankan Aplikasi Secara Lokal

1. **Clone repository**:
   ```bash
   git clone https://github.com/tupperwureism/PPK-Project-1.git
   cd "PPK PROJECTS"
   ```
2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```
3. **Salin file environment & generate key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Migrasi database**:
   ```bash
   php artisan migrate
   ```
5. **Jalankan server pengembangan**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada: `http://127.0.0.1:8000` (atau via Laragon di `http://ppk-projects.test`).
