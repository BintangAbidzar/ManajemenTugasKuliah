<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pesan_error = "";

if ($id == 0) {
    header("Location: index.php");
    exit;
}

$query_data = mysqli_query($koneksi, "SELECT * FROM tugas WHERE id = '$id' AND user_id = '$user_id'");
$tugas = mysqli_fetch_assoc($query_data);

if (!$tugas) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $status_valid = array("Belum", "Proses", "Selesai");
    if (!in_array($status, $status_valid)) {
        $status = "Belum";
    }

    if ($deadline == "") {
        $pesan_error = "Deadline wajib diisi.";
    } else {
        $query = "UPDATE tugas SET
                  deskripsi = '$deskripsi',
                  deadline = '$deadline',
                  status = '$status'
                  WHERE id = '$id' AND user_id = '$user_id'";
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
    <title>Edit Tugas</title>
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
                <a href="index.php" class="active">Tugas</a>
                <a href="tambah.php">Tambah</a>
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
                <span class="course-badge"><?php echo $tugas['status']; ?></span>
                <span class="due-text">
                    <span class="material-symbols-outlined">calendar_month</span>
                    Due: <?php echo date("d M Y", strtotime($tugas['deadline'])); ?>
                </span>
            </div>
            <h1><?php echo htmlspecialchars($tugas['judul']); ?></h1>
            <div class="task-actions">
                <a href="hapus.php?id=<?php echo $tugas['id']; ?>" class="icon-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                    <span class="material-symbols-outlined">delete</span>
                </a>
            </div>
        </section>

        <section class="content-card">
            <h3>Edit Task</h3>

            <?php if ($pesan_error != "") { ?>
                <div class="alert-error"><?php echo $pesan_error; ?></div>
            <?php } ?>

            <form method="POST">
                <label>Judul</label>
                <input type="text" value="<?php echo htmlspecialchars($tugas['judul']); ?>" disabled>

                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5"><?php echo htmlspecialchars($tugas['deskripsi']); ?></textarea>

                <label>Deadline</label>
                <input type="date" name="deadline" value="<?php echo $tugas['deadline']; ?>" required>

                <label>Status</label>
                <select name="status">
                    <option value="Belum" <?php if ($tugas['status'] == "Belum") echo "selected"; ?>>Belum</option>
                    <option value="Proses" <?php if ($tugas['status'] == "Proses") echo "selected"; ?>>Proses</option>
                    <option value="Selesai" <?php if ($tugas['status'] == "Selesai") echo "selected"; ?>>Selesai</option>
                </select>

                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="../dashboard/index.php" class="bottom-item">
            <span class="material-symbols-outlined">dashboard</span>
            <small>Dashboard</small>
        </a>
        <a href="index.php" class="bottom-item active">
            <span class="material-symbols-outlined">calendar_month</span>
            <small>Tugas</small>
        </a>
        <a href="tambah.php" class="bottom-item bottom-add">
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
