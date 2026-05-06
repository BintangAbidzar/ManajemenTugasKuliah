<?php
session_start();

// Menghapus semua data session.
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
