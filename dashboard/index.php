<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];

$query_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tugas WHERE user_id = '$user_id'");
$total_tugas = mysqli_fetch_assoc($query_total)['total'];

$query_selesai = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tugas WHERE user_id = '$user_id' AND status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($query_selesai)['total'];

$query_proses = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tugas WHERE user_id = '$user_id' AND status = 'Proses'");
$total_proses = mysqli_fetch_assoc($query_proses)['total'];

$hari_ini = date("Y-m-d");
$query_dekat = mysqli_query($koneksi, "SELECT * FROM tugas WHERE user_id = '$user_id' AND status != 'Selesai' ORDER BY deadline ASC LIMIT 3");

$persen_selesai = 0;
if ($total_tugas > 0) {
    $persen_selesai = round(($total_selesai / $total_tugas) * 100);
}

$progress_offset = 364 - (364 * $persen_selesai / 100);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Tugas Kuliah</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="appbar">
        <div class="appbar-inner">
            <div class="brand-area">
                <a href="../tugas/index.php" class="icon-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <a href="index.php" class="brand-name">Beranda</a>
            </div>

            <div class="desktop-menu">
                <a href="index.php" class="active">Dashboard</a>
                <a href="../tugas/index.php">Tugas</a>
                <a href="../tugas/tambah.php">Tambah</a>
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
                <span class="course-badge">KULIAH</span>
                <span class="due-text">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <?php echo $total_tugas; ?> tugas aktif
                </span>
            </div>
            <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
            <p class="card-text">Semester ini tetap tertata.</p>
            <a href="../tugas/tambah.php" class="btn btn-outline">
                <span class="material-symbols-outlined">add</span>
                Tambah Tugas
            </a>
        </section>

        <div class="ringkasan">
            <div class="box-ringkasan">
                <span class="material-symbols-outlined">assignment</span>
                <h3><?php echo $total_tugas; ?></h3>
                <p>Total Tugas</p>
            </div>
            <div class="box-ringkasan">
                <span class="material-symbols-outlined">task_alt</span>
                <h3><?php echo $total_selesai; ?></h3>
                <p>Tugas Selesai</p>
            </div>
            <div class="box-ringkasan">
                <span class="material-symbols-outlined">pending_actions</span>
                <h3><?php echo $total_proses; ?></h3>
                <p>Sedang Proses</p>
            </div>
        </div>

        <div class="detail-grid">
            <section class="content-card">
                <h3>Deadline Terdekat</h3>

                <?php if (mysqli_num_rows($query_dekat) > 0) { ?>
                    <div class="task-list-mini">
                        <?php while ($tugas = mysqli_fetch_assoc($query_dekat)) { ?>
                            <?php
                            $class_reminder = "";
                            if ($tugas['deadline'] < $hari_ini) {
                                $class_reminder = "terlambat";
                            } elseif ($tugas['deadline'] == $hari_ini) {
                                $class_reminder = "deadline-hari-ini";
                            }
                            ?>
                            <div class="mini-task <?php echo $class_reminder; ?>">
                                <div>
                                    <span class="course-badge kecil"><?php echo $tugas['status']; ?></span>
                                    <h4><?php echo htmlspecialchars($tugas['judul']); ?></h4>
                                    <p><?php echo htmlspecialchars($tugas['deskripsi']); ?></p>
                                </div>
                                <div class="mini-date">
                                    <span class="material-symbols-outlined">calendar_month</span>
                                    <?php echo date("d M Y", strtotime($tugas['deadline'])); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>Belum ada tugas yang perlu dikerjakan.</p>
                <?php } ?>
            </section>

            <aside class="side-stack">
                <section class="status-panel">
                    <h3>Status</h3>
                    <div class="status-row">
                        <span><?php echo $total_proses; ?> tugas dalam proses</span>
                        <span class="material-symbols-outlined">radio_button_unchecked</span>
                    </div>
                    <a href="../tugas/index.php" class="btn btn-light">Lihat Semua Tugas</a>
                </section>

                <section class="content-card health-card">
                    <h3>Target</h3>
                    <div class="progress-ring">
                        <svg viewBox="0 0 140 140">
                            <circle class="ring-bg" cx="70" cy="70" r="58"></circle>
                            <circle class="ring-fill" cx="70" cy="70" r="58" stroke-dashoffset="<?php echo $progress_offset; ?>"></circle>
                        </svg>
                        <div class="ring-text">
                            <strong><?php echo $persen_selesai; ?>%</strong>
                            <span>Done</span>
                        </div>
                    </div>
                    <p><?php echo $total_selesai; ?> dari <?php echo $total_tugas; ?> tugas sudah selesai.</p>
                </section>
            </aside>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="index.php" class="bottom-item active">
            <span class="material-symbols-outlined">dashboard</span>
            <small>Dashboard</small>
        </a>
        <a href="../tugas/index.php" class="bottom-item">
            <span class="material-symbols-outlined">calendar_month</span>
            <small>Tugas</small>
        </a>
        <a href="../tugas/tambah.php" class="bottom-item bottom-add">
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
