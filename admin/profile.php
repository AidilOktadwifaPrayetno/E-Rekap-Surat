<?php
include '../includes/db_connect.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Ambil ID admin dari session

// Ambil data admin berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id' AND role = 'admin'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data admin tidak ditemukan.";
    exit();
}
$admin = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($password)) {
        $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username', password = '$password' WHERE id = '$user_id' AND role = 'admin'";
    } else {
        $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username' WHERE id = '$user_id' AND role = 'admin'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Profil berhasil diperbarui!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'profile.php';
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal mengupdate profil. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
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
    <title>Profil Admin</title>
    <link rel="stylesheet" href="../assets/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i>Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php"><i class="fas fa-user-shield"></i> Jabatan </a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Profil Admin</h1>
                <p>Perbarui data pribadi Anda di sini.</p>
            </header>
            <section class="content-profile">
                <div class="form-container">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo $admin['nama_lengkap']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $admin['username']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru (Opsional)</label>
                            <input type="password" id="password" name="password" placeholder="Masukkan password baru jika ingin mengubah">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Perbarui Profil</button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

