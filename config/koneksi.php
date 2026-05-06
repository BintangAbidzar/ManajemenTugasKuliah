<?php
// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$password = "";
$database = "manajemen_tugas_kuliah";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
