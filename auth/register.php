<?php
session_start();
include "../config/koneksi.php";

$pesan_error = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $pesan_error = "Semua field harus diisi.";
    } elseif ($password !== $confirm_password) {
        $pesan_error = "Password dan konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $pesan_error = "Password minimal 6 karakter.";
    } else {
        // Cek apakah username sudah ada
        $query_check = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($query_check) > 0) {
            $pesan_error = "Username sudah digunakan.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user baru
            $query_insert = mysqli_query($koneksi, "INSERT INTO users (username, password) VALUES ('$username', '$password_hash')");

            if ($query_insert) {
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php");
                exit;
            } else {
                $pesan_error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Manajemen Tugas Kuliah</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="login-page">
    <main class="login-shell">
        <div class="login-brand">
            <span class="material-symbols-outlined">arrow_back</span>
            <h1>TaskFlow</h1>
            <span class="material-symbols-outlined">notifications</span>
        </div>

        <section class="task-card login-card">
            <div class="status-strip"></div>
            <div class="task-meta">
                <span class="course-badge">STUDENT</span>
                <span class="due-text">
                    <span class="material-symbols-outlined">person_add</span>
                    Registrasi
                </span>
            </div>

            <h1>Daftar Akun Baru</h1>
            <p class="card-text">Buat akun untuk mengelola tugas kuliahmu.</p>

            <?php if ($pesan_error != "") { ?>
                <div class="alert-error"><?php echo $pesan_error; ?></div>
            <?php } ?>

            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php } ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Password</label>
                <input type="password" name="password" required minlength="6">

                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required minlength="6">

                <button type="submit" name="register" class="btn btn-primary">Daftar</button>
            </form>

            <p class="teks-kecil akun-demo">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </p>
        </section>
    </main>
</body>
</html>