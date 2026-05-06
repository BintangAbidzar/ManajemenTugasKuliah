<?php
session_start();

// Halaman awal hanya mengarahkan user sesuai status login.
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/index.php");
} else {
    header("Location: auth/login.php");
}
exit;
?>
