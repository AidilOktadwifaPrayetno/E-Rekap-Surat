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

// Fetch karyawan data based on ID
$id = $_GET['id'];
$karyawan_query = "SELECT * FROM karyawan WHERE id = $id";
$karyawan_result = mysqli_query($conn, $karyawan_query);
if (mysqli_num_rows($karyawan_result) == 0) {
    echo "<script>
        alert('Karyawan tidak ditemukan.');
        window.location.href = 'karyawan.php';
    </script>";
    exit;
}
$karyawan = mysqli_fetch_assoc($karyawan_result);

// Fetch all jabatan for the dropdown
$jabatan_result = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $no_hp = $_POST['no_hp'];
    $jabatan_id = $_POST['jabatan_id'];

    $query = "UPDATE karyawan SET nama_lengkap = '$nama_lengkap', no_hp = '$no_hp', jabatan_id = $jabatan_id WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data karyawan berhasil diperbarui!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'karyawan.php';
                });
            });
        </script>";
    } else {
        $errorMessage = mysqli_real_escape_string($conn, "Gagal memperbarui data: " . mysqli_error($conn));
        echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelaksana Tugas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        .logo {
            width: 100px;
            height: 100px;
            margin: 5px;
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
                <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php" class="active"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Edit Pelaksana Tugas</h1>
                <p>Perbarui data Pelaksana Tugas di bawah ini.</p>
            </header>
            <section class="content-edit">
                <div class="form-container">
                    <form method="post" action="">
                        <h1>Edit Pelaksana Tugas</h1>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($karyawan['nama_lengkap']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No HP</label>
                            <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($karyawan['no_hp']); ?>" >
                        </div>
                        <div class="form-group">
                            <label for="jabatan">Jabatan</label>
                            <select id="jabatan" name="jabatan_id" required>
                                <option value="">Pilih Jabatan</option>
                                <?php while ($jabatan_row = mysqli_fetch_assoc($jabatan_result)) { ?>
                                    <option value="<?php echo $jabatan_row['id']; ?>" <?php echo $karyawan['jabatan_id'] == $jabatan_row['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($jabatan_row['nama_jabatan']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update</button>
                            <a href="karyawan.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                        </div>
                    </form>
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
