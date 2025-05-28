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
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    session_unset();
    session_destroy();
    echo "<script>
        sessionStorage.setItem('session_expired', 'true');
        window.location.href = '../index.php';
    </script>";
    exit;
}

// Ambil data nama lengkap pengguna yang sedang login
$userId = $_SESSION['user_id'];
$queryUser = "SELECT nama_lengkap FROM users WHERE id = $userId";
$resultUser = mysqli_query($conn, $queryUser);
$userData = mysqli_fetch_assoc($resultUser);
$namaLengkap = $userData['nama_lengkap'];

// Query untuk menghitung jumlah data
$queryKaryawan = "SELECT COUNT(*) AS total FROM karyawan";
$resultKaryawan = mysqli_query($conn, $queryKaryawan);
$totalKaryawan = mysqli_fetch_assoc($resultKaryawan)['total'];

$queryPetugas = "SELECT COUNT(*) AS total FROM users WHERE role = 'petugas' OR role = 'monitor'";
$resultPetugas = mysqli_query($conn, $queryPetugas);
$totalPetugas = mysqli_fetch_assoc($resultPetugas)['total'];

$querySPT = "SELECT COUNT(*) AS total FROM spt";
$resultSPT = mysqli_query($conn, $querySPT);
$totalSPT = mysqli_fetch_assoc($resultSPT)['total'];

$queryJabatan = "SELECT COUNT(*) AS total FROM jabatan";
$resultJabatan = mysqli_query($conn, $queryJabatan);
$totalJabatan = mysqli_fetch_assoc($resultJabatan)['total'];

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
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($login_success) { ?>
                Swal.fire({
                    title: 'Login Berhasil!',
                    text: "Selamat datang, <?php echo htmlspecialchars($namaLengkap); ?>!",
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
        .count {
            font-weight: bold;
            font-size: 1.2em;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header" style="border-bottom: 1px solid #ccc; padding-bottom: 0.5px;">
                <img src="../assets/images/logo.png" alt="Logo" class="logo">
                <h5>DPRD <br>Provinsi Sumatera Barat</h5    >
                <p>E-REKAP SPT</p>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Dashboard Admin</h1>
                <p>Selamat Datang, <strong><?php echo htmlspecialchars($namaLengkap); ?></strong>! di Dashboard Admin</p>
            </header>
            <section class="content">
                <div class="card">
                    <h3>SPT</h3>
                    <p>Jumlah SPT: <span class="count"><?php echo $totalSPT; ?></span></p>
                    <a href="spt.php" class="btn-primary">Lihat SPT</a>
                </div>
                <div class="card">
                    <h3> Pelaksana Tugas</h3>
                    <p>Jumlah Pelaksana Tugas: <span class="count"><?php echo $totalKaryawan; ?></span></p>
                    <a href="karyawan.php" class="btn-primary">Kelola Karyawan</a>
                </div>
                <div class="card">
                    <h3>Petugas</h3>
                    <p>Jumlah Petugas: <span class="count"><?php echo $totalPetugas; ?></span></p>
                    <a href="petugas.php" class="btn-primary">Kelola Petugas</a>
                </div>
                <div class="card">
                    <h3>Jabatan</h3>
                    <p>Jumlah Jabatan: <span class="count"><?php echo $totalJabatan; ?></span></p>
                    <a href="jabatan.php" class="btn-primary">Lihat Jabatan</a>
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
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const menuLinks = document.querySelectorAll('.menu a');
            const toggleButton = document.querySelector('.toggle-btn');

            // Fungsi untuk menutup sidebar dengan efek delay
            function closeSidebar() {
                setTimeout(function() {
                    sidebar.classList.add('collapsed'); // Menambahkan kelas collapsed setelah delay
                }, 300); // Delay 300ms (sama dengan durasi transisi)
            }

            // Tutup sidebar otomatis saat klik menu di layar kecil
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Pastikan sidebar tertutup saat klik menu pada layar kecil
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                });
            });

            // Toggle sidebar pada button toggle
            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });

            // Tutup sidebar secara default saat halaman pertama kali dimuat di mobile
            if (window.innerWidth < 768) {
                sidebar.classList.add('collapsed');
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
    </script>
</body>
</html>
