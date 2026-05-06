<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];
$pesan_error = "";

if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $status_valid = array("Belum", "Proses", "Selesai");
    if (!in_array($status, $status_valid)) {
        $status = "Belum";
    }

    if ($judul == "" || $deadline == "") {
        $pesan_error = "Judul dan deadline wajib diisi.";
    } else {
        $query = "INSERT INTO tugas (user_id, judul, deskripsi, deadline, status)
                  VALUES ('$user_id', '$judul', '$deskripsi', '$deadline', '$status')";
        mysqli_query($koneksi, $query);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="appbar">
        <div class="appbar-inner">
            <div class="brand-area">
                <a href="index.php" class="icon-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <a href="../dashboard/index.php" class="brand-name">Beranda</a>
            </div>

            <div class="desktop-menu">
                <a href="../dashboard/index.php">Dashboard</a>
                <a href="index.php">Tugas</a>
                <a href="tambah.php" class="active">Tambah</a>
            </div>

            <div class="top-actions">
                <span class="material-symbols-outlined">notifications</span>
                <div class="avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
            </div>
        </div>
    </header>

    <main class="app-container form-page">
        <section class="task-card">
            <div class="status-strip"></div>
            <div class="task-meta">
                <span class="course-badge">NEW TASK</span>
                <span class="due-text">
                    <span class="material-symbols-outlined">edit_calendar</span>
                    Form tugas
                </span>
            </div>
            <h1>Tambah Tugas</h1>
            <p class="card-text">Data tugas baru.</p>
        </section>

        <section class="content-card">
            <h3>Task Description</h3>

            <?php if ($pesan_error != "") { ?>
                <div class="alert-error"><?php echo $pesan_error; ?></div>
            <?php } ?>

            <form method="POST">
                <label>Judul</label>
                <input type="text" name="judul" required>

                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5"></textarea>

                <label>Deadline</label>
                <input type="date" name="deadline" required>

                <label>Status</label>
                <select name="status">
                    <option value="Belum">Belum</option>
                    <option value="Proses">Proses</option>
                    <option value="Selesai">Selesai</option>
                </select>

                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="../dashboard/index.php" class="bottom-item">
            <span class="material-symbols-outlined">dashboard</span>
            <small>Dashboard</small>
        </a>
        <a href="index.php" class="bottom-item">
            <span class="material-symbols-outlined">calendar_month</span>
            <small>Tugas</small>
        </a>
        <a href="tambah.php" class="bottom-item bottom-add active">
            <span class="material-symbols-outlined">add_circle</span>
            <small>Tambah</small>
        </a>
        <a href="../auth/logout.php" class="bottom-item">
            <span class="material-symbols-outlined">logout</span>
            <small>Logout</small>
        </a>
    </nav>
</body>
</html>
