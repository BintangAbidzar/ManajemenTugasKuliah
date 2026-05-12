# Laporan Proyek: TaskFlow - Manajemen Tugas Kuliah

---

## 1. Gambaran Umum

**TaskFlow** adalah aplikasi web berbasis PHP dan MySQL yang dirancang untuk membantu mahasiswa mengelola, melacak, dan mengorganisir tugas kuliah mereka secara efisien. Aplikasi ini menyediakan fitur autentikasi pengguna, CRUD (Create, Read, Update, Delete) tugas, dashboard statistik, serta visualisasi deadline secara real-time.

| Informasi           | Detail                                  |
| ------------------- | --------------------------------------- |
| Nama Aplikasi       | TaskFlow                                |
| Bahasa              | PHP (Server-side), CSS, JavaScript      |
| Database            | MySQL / MariaDB                         |
| Nama Database       | `manajemen_tugas_kuliah`               |
| Server              | Apache (XAMPP)                          |
| Branch Aktif        | `dev`                                   |
| Jumlah File Kode    | 10 file (8 PHP, 1 CSS, 1 MD)           |

---

## 2. Struktur Direktori

```
deadline/
├── index.php                    # Router utama (redirect berdasarkan sesi login)
├── README.md                    # Dokumentasi proyek
├── laporan.md                   # Laporan proyek (file ini)
│
├── assets/
│   └── css/
│       └── style.css            # Seluruh styling aplikasi (1025 baris)
│
├── config/
│   └── koneksi.php              # Konfigurasi koneksi database MySQL
│
├── auth/
│   ├── login.php                # Halaman login pengguna
│   ├── register.php             # Halaman registrasi akun baru
│   └── logout.php               # Proses logout (hapus sesi)
│
├── dashboard/
│   └── index.php                # Halaman dashboard utama
│
└── tugas/
    ├── index.php                # Daftar seluruh tugas (Read)
    ├── tambah.php               # Form tambah tugas baru (Create)
    ├── edit.php                 # Form edit tugas (Update)
    └── hapus.php                # Proses hapus tugas (Delete)
```

---

## 3. Skema Database

Aplikasi menggunakan database MySQL bernama `manajemen_tugas_kuliah` dengan dua tabel utama:

### 3.1 Tabel `users`

Menyimpan data akun pengguna.

| Kolom     | Tipe         | Keterangan                        |
| --------- | ------------ | --------------------------------- |
| `id`      | INT (PK)     | ID unik pengguna, auto increment  |
| `username`| VARCHAR      | Nama pengguna (unik)              |
| `password`| VARCHAR(255) | Password yang di-hash (bcrypt)    |

### 3.2 Tabel `tugas`

Menyimpan data tugas milik setiap pengguna.

| Kolom       | Tipe     | Keterangan                                    |
| ----------- | -------- | --------------------------------------------- |
| `id`        | INT (PK) | ID unik tugas, auto increment                 |
| `user_id`   | INT (FK) | Relasi ke tabel `users.id`                   |
| `judul`     | VARCHAR  | Judul tugas                                   |
| `deskripsi` | TEXT     | Deskripsi/detail tugas                        |
| `deadline`  | DATE     | Tenggat waktu tugas                           |
| `status`    | VARCHAR  | Status tugas: `Belum`, `Proses`, atau `Selesai` |

### Query Pembuatan Database (Disarankan)

```sql
CREATE DATABASE IF NOT EXISTS manajemen_tugas_kuliah;
USE manajemen_tugas_kuliah;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    deadline DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Belum',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 4. Deskripsi Detail Setiap File

### 4.1 [index.php](index.php) - Router Utama

File ini berfungsi sebagai entry point aplikasi. Logika routing:
- Jika pengguna sudah login (`$_SESSION['user_id']` ada) → redirect ke `dashboard/index.php`
- Jika belum login → redirect ke `auth/login.php`

### 4.2 [config/koneksi.php](config/koneksi.php) - Koneksi Database

Konfigurasi koneksi ke MySQL menggunakan `mysqli_connect`:
- Host: `localhost`
- User: `root`
- Password: *(kosong)*
- Database: `manajemen_tugas_kuliah`
- Menggunakan `mysqli_connect_error()` untuk error handling

### 4.3 [auth/login.php](auth/login.php) - Halaman Login

Fitur:
- Form login dengan input username dan password
- Validasi credential menggunakan `password_verify()` (bcrypt hash)
- Auto-creation akun admin default (`admin` / `admin123`) jika tabel users kosong
- Menyimpan `user_id` dan `username` ke session saat login berhasil
- Menampilkan pesan error jika login gagal
- Redirect otomatis ke dashboard jika sudah login

### 4.4 [auth/register.php](auth/register.php) - Halaman Registrasi

Fitur:
- Form registrasi dengan username, password, dan konfirmasi password
- Validasi: field wajib diisi, password minimal 6 karakter, konfirmasi password harus cocok
- Cek duplikasi username di database
- Password di-hash menggunakan `password_hash()` sebelum disimpan
- Menampilkan pesan sukses setelah registrasi berhasil, lalu redirect ke login

### 4.5 [auth/logout.php](auth/logout.php) - Proses Logout

Fitur:
- Menghapus semua data session menggunakan `session_unset()` dan `session_destroy()`
- Redirect ke halaman login

### 4.6 [dashboard/index.php](dashboard/index.php) - Dashboard Utama

Fitur:
- Proteksi halaman: hanya bisa diakses jika sudah login
- Menampilkan statistik tugas pengguna:
  - Total tugas
  - Tugas selesai
  - Tugas sedang proses
- **Progress ring** (SVG): visualisasi persentase penyelesaian tugas dalam bentuk lingkaran
- **Deadline terdekat**: menampilkan 3 tugas dengan deadline paling dekat yang belum selesai
- **Indikator visual deadline**:
  - Latar belakang merah (`terlambat`) → deadline sudah lewat
  - Latar belakang kuning (`deadline-hari-ini`) → deadline hari ini
- Menu profil dropdown dengan avatar (inisial username) dan tombol logout
- Navigasi desktop menu dan bottom navigation (mobile)

### 4.7 [tugas/index.php](tugas/index.php) - Daftar Tugas

Fitur:
- Menampilkan seluruh tugas milik user yang login, diurutkan berdasarkan deadline terdekat
- Tabel dengan kolom: No, Judul, Deskripsi, Deadline, Status, Aksi
- Badge status berwarna sesuai kondisi:
  - `Belum` → abu-abu
  - `Proses` → biru
  - `Selesai` → hijau
- Indikator visual deadline (merah/kuning) pada baris tabel
- Tombol aksi: Edit dan Hapus per tugas
- Konfirmasi JavaScript sebelum menghapus tugas

### 4.8 [tugas/tambah.php](tugas/tambah.php) - Tambah Tugas Baru

Fitur:
- Form input: Judul, Deskripsi, Deadline (date picker), dan Status (dropdown)
- Validasi server-side: judul dan deadline wajib diisi
- Validasi status: hanya menerima nilai `Belum`, `Proses`, atau `Selesai`
- Menggunakan `mysqli_real_escape_string()` untuk mencegah SQL injection
- Redirect ke daftar tugas setelah berhasil menyimpan

### 4.9 [tugas/edit.php](tugas/edit.php) - Edit Tugas

Fitur:
- Mengambil data tugas berdasarkan ID dari parameter URL
- Verifikasi kepemilikan: hanya tugas milik user yang login yang bisa diedit
- Judul tugas tidak bisa diubah (field disabled)
- Field yang bisa diedit: Deskripsi, Deadline, dan Status
- Validasi status dan deadline yang sama seperti form tambah
- Tombol hapus tersedia di halaman edit juga

### 4.10 [tugas/hapus.php](tugas/hapus.php) - Hapus Tugas

Fitur:
- Menghapus tugas berdasarkan ID dari parameter URL
- Verifikasi kepemilikan: hanya menghapus tugas milik user yang login (`user_id` dicek pada query DELETE)
- Redirect ke daftar tugas setelah penghapusan

### 4.11 [assets/css/style.css](assets/css/style.css) - Stylesheet (1025 baris)

Desain UI:
- **Neobrutalism** style: border tebal, shadow offset, warna-warni cerah
- **CSS Variables** untuk konsistensi tema
- **Font**: Lexend (Google Fonts)
- **Ikon**: Material Symbols Outlined (Google Fonts)
- **Layout**: Flexbox dan CSS Grid
- **Responsive**:
  - Breakpoint 820px: menyembunyikan desktop menu, menampilkan bottom navigation, layout single-column
  - Breakpoint 460px: penyesuaian padding, ukuran font, dan avatar
- Komponen utama yang di-style:
  - App bar (header sticky)
  - Task card (kartu tugas utama)
  - Progress ring (SVG animasi)
  - Tabel data tugas
  - Form input (text, textarea, select, date)
  - Buttons (primary, outline, secondary, danger)
  - Status badges (pill-shaped)
  - Alert boxes (error meru, success hijau)
  - Profile dropdown menu
  - Bottom navigation bar (mobile)
  - Login/register page styling

---

## 5. Alur Aplikasi

```
Pengguna membuka index.php
        │
        ├── Sudah login? ──→ Dashboard (/dashboard/index.php)
        │                         │
        │                         ├── Statistik tugas (total, selesai, proses)
        │                         ├── Progress ring (persentase selesai)
        │                         └── Deadline terdekat (3 tugas teratas)
        │
        └── Belum login? ──→ Login (/auth/login.php)
                                │
                                ├── Login berhasil ──→ Dashboard
                                └── Belum punya akun? ──→ Register (/auth/register.php)
                                                              │
                                                              └── Registrasi berhasil ──→ Login

Dari Dashboard, pengguna bisa:
        │
        ├── Lihat daftar tugas (/tugas/index.php)
        ├── Tambah tugas baru (/tugas/tambah.php)
        ├── Edit tugas (/tugas/edit.php?id=X)
        ├── Hapus tugas (/tugas/hapus.php?id=X)
        └── Logout (/auth/logout.php) ──→ kembali ke Login
```

---

## 6. Fitur Keamanan yang Diterapkan

| Aspek                  | Implementasi                                               |
| ---------------------- | ---------------------------------------------------------- |
| Hash Password          | `password_hash()` dengan algoritma bcrypt (PASSWORD_DEFAULT) |
| Verifikasi Password    | `password_verify()` untuk mencocokkan input dengan hash    |
| SQL Injection          | `mysqli_real_escape_string()` pada input pengguna          |
| XSS Prevention         | `htmlspecialchars()` pada output ke HTML                   |
| Session Protection     | Pengecekan `$_SESSION['user_id']` di setiap halaman        |
| Ownership Validation   | Query selalu menyertakan `user_id` untuk verifikasi kepemilikan |
| Type Casting           | `(int)` cast pada `user_id` dan `id` parameter URL         |
| Status Validation      | Whitelist validasi status (`Belum`, `Proses`, `Selesai`)   |

---

## 7. Fitur UI/UX

### Navigasi
- **Desktop**: App bar sticky dengan menu navigasi (Dashboard, Tugas, Tambah) + dropdown profil
- **Mobile**: Bottom navigation bar dengan ikon (Dashboard, Tugas, Tambah, Logout)
- Tombol back pada setiap halaman untuk navigasi mundur

### Visualisasi Deadline
- Warna baris/kartu berubah otomatis:
  - **Merah** (`terlambat`): deadline sudah lewat dan tugas belum selesai
  - **Kuning** (`deadline-hari-ini`): deadline hari ini dan tugas belum selesai
- Tugas diurutkan berdasarkan deadline terdekat

### Progress Ring
- Lingkaran SVG animasi di sidebar dashboard
- Menunjukkan persentase penyelesaian tugas (tugas selesai / total tugas x 100%)
- Warna teal untuk indikasi progress

### Interaksi
- Hover effect pada kartu, tombol, dan avatar (transform + shadow)
- Active effect pada tombol (pressed state)
- Konfirmasi JavaScript sebelum menghapus tugas
- Error dan success alert dengan warna yang jelas

---

## 8. Kelebihan Aplikasi

1. **Struktur kode rapi** - Pemisahan modul yang jelas (auth, config, dashboard, tugas)
2. **Keamanan dasar baik** - Password hashing, SQL injection prevention, XSS protection, ownership validation
3. **UI modern dan responsif** - Neobrutalism design, responsive untuk desktop dan mobile
4. **Fitur lengkap** - CRUD tugas, autentikasi, dashboard statistik, visualisasi progress
5. **User-friendly** - Indikator deadline visual, navigasi intuitif, feedback pesan error/sukses
6. **Multi-user** - Setiap pengguna hanya melihat dan mengelola tugas miliknya sendiri

---

## 9. Kekurangan dan Saran Perbaikan

### Kekurangan

| No | Kekurangan                                        | Penjelasan                                                   |
| -- | ------------------------------------------------- | ------------------------------------------------------------ |
| 1  | Tidak ada file SQL migration                      | Database harus dibuat manual, tidak ada skrip otomatis       |
| 2  | Tidak ada CSRF protection                         | Form tidak dilindungi dari serangan Cross-Site Request Forgery |
| 3  | Tidak ada session timeout                         | Sesi tidak memiliki batas waktu, rentan jika komputer ditinggal |
| 4  | Tidak ada rate limiting pada login                | Login bisa dicoba berkali-kali tanpa batas (rentan brute force) |
| 5  | Tidak ada fitur pencarian/filter tugas            | Tugas tidak bisa dicari atau difilter berdasarkan status/tanggal |
| 6  | Tidak ada paginasi                                | Jika tugas sangat banyak, halaman akan sangat panjang        |
| 7  | Semua operasi full page reload                    | Tidak menggunakan AJAX, setiap aksi me-reload seluruh halaman |
| 8  | Konfigurasi database hardcoded                    | Tidak ada file `.env` atau konfigurasi environment terpisah  |
| 9  | Judul tugas tidak bisa diedit                     | Field judul dihalaman edit dinonaktifkan (`disabled`)        |
| 10 | Tidak ada validasi format tanggal di server       | Input deadline hanya divalidasi di HTML5 (`type="date"`)     |

### Saran Perbaikan

1. **Tambahkan file SQL migration** (`database.sql`) untuk pembuatan otomatis database dan tabel
2. **Implementasikan CSRF token** pada semua form untuk mencegah serangan CSRF
3. **Tambahkan session timeout** (misal 30 menit tidak aktif → logout otomatis)
4. **Implementasikan rate limiting** pada login (misal maks 5 percobaan per 15 menit)
5. **Tambahkan fitur pencarian dan filter** berdasarkan status, tanggal, atau kata kunci
6. **Implementasikan paginasi** untuk daftar tugas (misal 10 tugas per halaman)
7. **Gunakan AJAX/fetch API** untuk operasi CRUD tanpa reload halaman
8. **Gunakan file `.env`** untuk konfigurasi database dan environment lainnya
9. **Izinkan edit judul** atau berikan alasan kenapa judul tidak bisa diedit
10. **Tambahkan validasi tanggal di server-side** untuk memastikan format tanggal valid

---

## 10. Riwayat Git

| Branch | Commit Terakhir | Pesan Commit    |
| ------ | --------------- | --------------- |
| `dev`  | `231aaa0`       | ui baru         |
| `dev`  | `a688930`       | last commit     |
| `dev`  | `a34020c`       | three commit    |
| `dev`  | `095134a`       | frist commit    |
| `dev`  | `3784a6f`       | Initial commit  |

Status working tree: **clean** (tidak ada perubahan yang belum di-commit)

---

## 11. Persyaratan Sistem

| Komponen    | Minimum                  |
| ----------- | ------------------------ |
| Web Server  | Apache (via XAMPP)       |
| PHP         | 7.4+ (menggunakan `password_hash`) |
| Database    | MySQL 5.7+ / MariaDB 10.3+ |
| Browser     | Chrome, Firefox, Edge (modern browser) |

---

## 12. Cara Menjalankan Aplikasi

1. Pastikan XAMPP sudah terinstall dan Apache + MySQL sudah berjalan
2. Buat database `manajemen_tugas_kuliah` di phpMyAdmin
3. Buat tabel `users` dan `tugas` sesuai skema di bagian 3
4. Letakkan folder `deadline` di dalam `C:\xampp\htdocs\`
5. Buka browser dan akses `http://localhost/deadline/`
6. Login menggunakan akun default: **admin** / **admin123**, atau daftar akun baru

---

*Dokumen ini dibuat secara otomatis berdasarkan analisis seluruh file dalam proyek TaskFlow.*
*Tanggal: 12 Mei 2026*
