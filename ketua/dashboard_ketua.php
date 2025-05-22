<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah caching total
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include '../includes/db_connect.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'monitor') {
    session_unset();
    session_destroy();
    echo "<script>
        sessionStorage.setItem('session_expired', 'true');
        window.location.href = '../index.php';
    </script>";
    exit;
}

// Ambil notifikasi login jika ada
$login_success = $_SESSION['login_success'] ?? null;
unset($_SESSION['login_success']);

// Ambil nama lengkap
$userId = $_SESSION['user_id'];
$queryUser = "SELECT nama_lengkap FROM users WHERE id = $userId";
$resultUser = mysqli_query($conn, $queryUser);
$namaLengkap = ($resultUser && mysqli_num_rows($resultUser) > 0) ? mysqli_fetch_assoc($resultUser)['nama_lengkap'] : 'Pengguna';

// Jumlah data
$totalSPT = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM spt"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header" style="border-bottom: 1px solid #ccc;">
                <img src="../assets/images/logo.png" alt="Logo" class="logo" style="width:100px;height:100px;margin:5px;">
                <h5>DPRD <br>Provinsi Sumatera Barat</h5>
                <p>E-REKAP SPT</p>
            </div>
            <ul class="menu">
                <li><a href="dashboard_ketua.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Dashboard Users</h1>
                <p>Selamat Datang, <strong><?= htmlspecialchars($namaLengkap); ?></strong>!</p>
            </header>
            <section class="content">
                <div class="card">
                    <h3>SPT</h3>
                    <p>Jumlah SPT: <span class="count"><?= $totalSPT; ?></span></p>
                    <a href="spt.php" class="btn-primary">Lihat SPT</a>
                </div>
                <div class="card">
                    <h3>Profil</h3>
                    <p>Perbarui informasi pribadi Anda.</p>
                    <a href="profile.php" class="btn-secondary">Lihat Profil</a>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Tampilkan notifikasi login berhasil
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($login_success): ?>
                Swal.fire({
                    title: 'Login Berhasil!',
                    text: "Selamat datang, <?= htmlspecialchars($namaLengkap); ?>!",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>

            // Jika sebelumnya sesi sudah berakhir
                if (sessionStorage.getItem('session_expired')) {
                    sessionStorage.removeItem('session_expired');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesi Telah Berakhir!',
                        text: 'Silakan login kembali.',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Blok tombol back
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, location.href);
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Telah Berakhir!',
                    text: 'Silakan login kembali.',
                    confirmButtonText: 'Login'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            });

            // Deteksi ketika kembali ke tab (tab visibility)
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    fetch('../check_session.php')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'expired') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sesi Anda Telah Berakhir',
                                    text: 'Silakan login kembali.',
                                    confirmButtonText: 'Login'
                                }).then(() => {
                                    window.location.href = '../index.php';
                                });
                            }
                        });
                }
            });

            // Sidebar responsive toggle
            function toggleSidebar() {
                document.querySelector('.sidebar').classList.toggle('collapsed');
            }
    </script>
</body>
</html>
