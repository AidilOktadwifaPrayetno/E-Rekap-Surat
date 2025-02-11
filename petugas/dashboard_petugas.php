<?php
include '../includes/db_connect.php';
session_start();

// Check if the buyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header('Location: ../login.php');
    exit;
}

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Ambil notifikasi login jika ada
$login_success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : null;

// Hapus session login_success setelah ditampilkan
unset($_SESSION['login_success']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Petugas</title>
    <link rel="stylesheet" href="../assets/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Favicon -->
     <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($login_success) { ?>
                Swal.fire({
                    title: 'Login Berhasil!',
                    text: "<?php echo $login_success; ?>",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            <?php } ?>
        });
    </script>
    <style>
        .logo {
            width: 100px;
            height: 100px;
            margin: 5px;
        }
    </style>

</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/logo.png" alt="Logo" class="logo">
                <h2>Petugas Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_petugas.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Selamat Datang di Dashboard Petugas</h1>
                <p>Gunakan menu di sebelah kiri untuk mengelola SPT.</p>
            </header>
            <section class="content">
                <div class="card">
                    <h3>Data SPT</h3>
                    <p>Kelola data SPT yang terdaftar di sistem.</p>
                    <a href="spt.php" class="btn-primary">Kelola SPT</a>
                </div>
                <div class="card">
                    <h3>Profil</h3>
                    <p>Perbarui informasi pribadi Anda.</p>
                    <a href="profile.php" class="btn-secondary">Lihat Profil</a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
