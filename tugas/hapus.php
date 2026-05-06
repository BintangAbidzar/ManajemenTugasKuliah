<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id == 0) {
    header("Location: index.php");
    exit;
}

// Hapus hanya tugas milik user yang sedang login.
mysqli_query($koneksi, "DELETE FROM tugas WHERE id = '$id' AND user_id = '$user_id'");

header("Location: index.php");
exit;
?>
