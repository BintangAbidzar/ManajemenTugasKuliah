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
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    deadline DATE NOT NULL,
    status ENUM('Belum', 'Proses', 'Selesai') NOT NULL DEFAULT 'Belum',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Akun demo:
-- username: admin
-- password: admin123
-- Password di bawah dibuat dengan password_hash('admin123', PASSWORD_DEFAULT).
INSERT IGNORE INTO users (username, password) VALUES
('admin', '$2y$10$PCBPLrjZ6X7.UKwg72911uYHBTyxqrM3vuGAXcn6qhrpOtp4Kah6q');

INSERT INTO tugas (user_id, judul, deskripsi, deadline, status) VALUES
(1, 'Makalah Sistem Informasi', 'Menyelesaikan bab pendahuluan dan daftar pustaka.', '2026-05-08', 'Proses'),
(1, 'Latihan Basis Data', 'Mengerjakan soal normalisasi dan ERD.', '2026-05-15', 'Belum'),
(1, 'Presentasi Pemrograman Web', 'Menyiapkan slide dan demo aplikasi PHP Native.', '2026-06-01', 'Belum'),
(1, 'Review Materi Algoritma', 'Mengulang materi sorting dan searching.', '2026-05-04', 'Selesai');
