<?php
session_start();
include "../config/koneksi.php";

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$pesan_error = "";

// Contoh penggunaan password_hash untuk membuat user admin otomatis
// jika tabel users masih kosong.
$cek_user = mysqli_query($koneksi, "SELECT id FROM users LIMIT 1");
if ($cek_user && mysqli_num_rows($cek_user) == 0) {
    $password_admin = password_hash("admin123", PASSWORD_DEFAULT);
    mysqli_query($koneksi, "INSERT INTO users (username, password) VALUES ('admin', '$password_admin')");
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    $data_user = mysqli_fetch_assoc($query);

    // password_verify digunakan untuk mencocokkan password input dengan hash di database.
    if ($data_user && password_verify($password, $data_user['password'])) {
        $_SESSION['user_id'] = $data_user['id'];
        $_SESSION['username'] = $data_user['username'];

        header("Location: ../dashboard/index.php");
        exit;
    } else {
        $pesan_error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Tugas Kuliah</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                    <span class="material-symbols-outlined">lock</span>
                    Login
                </span>
            </div>

            <h1>Manajemen Tugas Kuliah</h1>
            <p class="card-text">Masuk ke ruang tugasmu.</p>

            <?php if ($pesan_error != "") { ?>
                <div class="alert-error"><?php echo $pesan_error; ?></div>
            <?php } ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </form>

            <p class="teks-kecil akun-demo">Demo: admin / admin123</p>
        </section>
    </main>
</body>
</html>
