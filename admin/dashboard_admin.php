<?php
include '../includes/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Query to count data
$queryKaryawan = "SELECT COUNT(*) AS total FROM karyawan";
$resultKaryawan = mysqli_query($conn, $queryKaryawan);
$totalKaryawan = mysqli_fetch_assoc($resultKaryawan)['total'];

$queryPetugas = "SELECT COUNT(*) AS total FROM users WHERE role = 'petugas'";
$resultPetugas = mysqli_query($conn, $queryPetugas);
$totalPetugas = mysqli_fetch_assoc($resultPetugas)['total'];

$querySPT = "SELECT COUNT(*) AS total FROM spt";
$resultSPT = mysqli_query($conn, $querySPT);
$totalSPT = mysqli_fetch_assoc($resultSPT)['total'];

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
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <h2>Admin Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Karyawan</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas & Ketua</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Dashboard Admin</h1>
                <p>Selamat Datang di Dashboard Admin</p>
            </header>
            <section class="content">
                <div class="card">
                    <h3>Manage Karyawan</h3>
                    <p>Jumlah Karyawan: <span class="count"><?php echo $totalKaryawan; ?></span></p>
                    <a href="karyawan.php" class="btn-primary">Kelola Karyawan</a>
                </div>
                <div class="card">
                    <h3>Manage Petugas</h3>
                    <p>Jumlah Petugas: <span class="count"><?php echo $totalPetugas; ?></span></p>
                    <a href="petugas.php" class="btn-primary">Kelola Petugas</a>
                </div>
                <div class="card">
                    <h3>SPT</h3>
                    <p>Jumlah SPT: <span class="count"><?php echo $totalSPT; ?></span></p>
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
</body>
</html>
