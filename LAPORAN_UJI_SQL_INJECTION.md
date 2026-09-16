# Laporan Hasil Uji Ketahanan SQL Injection pada Modul Autentikasi JARA
**Fitur:** Autentikasi & Login (`feat/login-registrasi`)  
**Penguji:** Developer 1  
**Status Keamanan:** 100% SECURE & RESILIENT (Lolos Uji Otomatis & Manual)

---

## 1. Ringkasan Eksekutif
Pengujian ini bertujuan untuk menguji dan membuktikan secara empiris bahwa modul Login & Autentikasi pada aplikasi **JARA** kebal (*resilient*) terhadap serangan **SQL Injection (SQLi)**, khususnya teknik *Authentication Bypass* (tautologi), *Stacked Queries*, dan *Comment Truncation*.

Berdasarkan pengujian:
* **Automated Unit Tests:** 9/9 Tests Lulus (**100% PASS**, 69 assertions).
* **Database State:** Tabel `users` tetap utuh, tidak ada kebocoran data, tidak ada *syntax error* 500 yang terekspos, dan penyerang tidak pernah berhasil mendapatkan sesi login (*assertGuest* selalu terpenuhi).

---

## 2. Analisis Arsitektur Pertahanan (*Defense in Depth*)

Modul autentikasi JARA dilindungi oleh **3 lapis pertahanan utama**:

```
[ Input Pengguna ]
        │
        ▼
┌─────────────────────────────────────────┐
│ Lapis 1: Request Validation             │ -> Memblokir payload non-email sebelum
│ ('email' => ['required', 'email'])      │    menyentuh query database.
└─────────────────────────────────────────┘
        │
        ▼
┌─────────────────────────────────────────┐
│ Lapis 2: PDO Prepared Statements (ORM)  │ -> Karakter ', --, OR, DROP diperlakukan
│ (Eloquent Query Builder / Auth::attempt)│    murni sebagai string literal (parameter data).
└─────────────────────────────────────────┘
        │
        ▼
┌─────────────────────────────────────────┐
│ Lapis 3: Bcrypt Password Hashing        │ -> Verifikasi password dilakukan di level
│ (Hash::check() di memori PHP)           │    aplikasi, bukan konkat string SQL.
└─────────────────────────────────────────┘
```

---

## 3. Hasil Pengujian Otomatis (Automated Testing)

Jalankan perintah ini di terminal untuk mereplikasi pengujian:

```bash
php artisan test --filter=AuthTest
```

### Kasus Uji yang Ditambahkan:
| Nama Test | Vektor Serangan | Payload yang Diuji | Hasil |
| :--- | :--- | :--- | :--- |
| `test_login_is_resilient_against_classic_sql_injection_payloads` | Tautology Auth Bypass | `' OR '1'='1`<br>`' OR 1=1 --`<br>`admin' --`<br>`admin' #`<br>`admin'/*` | **PASS** (Ditolak validasi, user tetap Guest) |
| `test_login_is_resilient_against_email_formatted_sql_injection` | Bypass berformat email valid | `'or'1'='1'@jara.app`<br>`admin'--@jara.app`<br>`admin'/*@jara.app` | **PASS** (Query mencari string literal, kredensial tidak cocok) |
| `test_login_is_resilient_against_password_field_sql_injection` | SQLi pada kolom Password | `' OR '1'='1`<br>`'; DROP TABLE users; --` | **PASS** (Password diverifikasi via hash, tabel utuh) |
| `test_login_is_resilient_against_destructive_stacked_queries` | Stacked Query / Drop Table | `admin@jara.app'; DROP TABLE users; --` | **PASS** (Tabel users tidak terhapus) |

---

## 4. Panduan Pengujian Manual (Step-by-Step)

Untuk pengujian manual langsung di browser:

1. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
2. **Buka Halaman Login:**
   Akses `http://127.0.0.1:8000/login`

### Skenario Uji Manual:

#### Skenario A: Uji Payload Tautologi Klasik (Auth Bypass)
* **Email:** `' OR '1'='1`
* **Password:** `sembarang123`
* **Klik:** "Masuk"
* **Hasil Aktual:** Form menolak request dengan pesan error validasi: *"The email field must be a valid email address."* (HTTP 302 / Gagal). Penyerang tetap berada di luar sistem.

#### Skenario B: Uji Payload Berformat Email
* **Email:** `'or'1'='1'@jara.app`
* **Password:** `12345678`
* **Klik:** "Masuk"
* **Hasil Aktual:** Muncul pesan error: *"Kredensial yang dimasukkan tidak cocok dengan data kami."* Database mencari user dengan email persis `'or'1'='1'@jara.app` dan tidak mengeksekusi logika SQL injection.

#### Skenario C: Uji Serangan Destruktif (Drop Table pada Password)
* **Email:** `admin@jara.app` (atau email akun yang ada)
* **Password:** `'; DROP TABLE users; --`
* **Klik:** "Masuk"
* **Hasil Aktual:** Login ditolak dengan aman, tabel database `users` tetap berdiri kokoh tanpa terjadi kehilangan data.

---

## 5. Kesimpulan
Modul Autentikasi JARA **terbukti 100% aman dan tahan terhadap SQL Injection**. Tidak ditemukan celah query mentah (*raw queries*), dan mekanisme *Prepared Statements* bawaan Laravel bekerja sempurna dalam menetralisir seluruh variasi karakter injeksi.
