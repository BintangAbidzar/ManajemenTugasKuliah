<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$hari_ini = date("Y-m-d");
$username = $_SESSION['username'];

// Tugas diurutkan berdasarkan deadline paling dekat.
$query_tugas = mysqli_query($koneksi, "SELECT * FROM tugas WHERE user_id = '$user_id' ORDER BY deadline ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tugas - Manajemen Tugas Kuliah</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="appbar">
        <div class="appbar-inner">
            <div class="brand-area">
                <a href="../dashboard/index.php" class="icon-link">
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

    <main class="app-container">
        <section class="task-card">
            <div class="status-strip"></div>
            <div class="task-meta">
                <span class="course-badge">ALL TASKS</span>
                <span class="due-text">
                    <span class="material-symbols-outlined">sort</span>
                    Deadline terdekat di atas
                </span>
            </div>
            <h1>Daftar Tugas Kuliah</h1>
            <p class="card-text">Catatan tugas semester ini.</p>
            <a href="tambah.php" class="btn btn-outline">
                <span class="material-symbols-outlined">add</span>
                Tambah Tugas
            </a>
        </section>

        <section class="content-card">
            <div class="section-title-row">
                <h3>Task List</h3>
                <span class="teks-kecil">Reminder deadline</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($query_tugas) > 0) {
                            while ($tugas = mysqli_fetch_assoc($query_tugas)) {
                                $class_reminder = "";

                                if ($tugas['status'] != "Selesai" && $tugas['deadline'] < $hari_ini) {
                                    $class_reminder = "terlambat";
                                } elseif ($tugas['status'] != "Selesai" && $tugas['deadline'] == $hari_ini) {
                                    $class_reminder = "deadline-hari-ini";
                                }
                        ?>
                            <tr class="<?php echo $class_reminder; ?>">
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($tugas['judul']); ?></strong></td>
                                <td><?php echo htmlspecialchars($tugas['deskripsi']); ?></td>
                                <td><?php echo date("d M Y", strtotime($tugas['deadline'])); ?></td>
                                <td>
                                    <span class="status status-<?php echo strtolower($tugas['status']); ?>">
                                        <?php echo $tugas['status']; ?>
                                    </span>
                                </td>
                                <td class="aksi">
                                    <a href="edit.php?id=<?php echo $tugas['id']; ?>" class="btn btn-secondary">
                                        <span class="material-symbols-outlined">edit</span>
                                        Edit
                                    </a>
                                    <a href="hapus.php?id=<?php echo $tugas['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                                        <span class="material-symbols-outlined">delete</span>
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="teks-tengah">Belum ada tugas.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
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
