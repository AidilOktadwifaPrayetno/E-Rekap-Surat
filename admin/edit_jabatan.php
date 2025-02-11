<?php
include '../includes/db_connect.php';
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get jabatan ID from URL
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM jabatan WHERE id = $id");
$jabatan = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_jabatan = $_POST['nama_jabatan'];

    // Update query for jabatan
    $query = "UPDATE jabatan SET nama_jabatan = '$nama_jabatan' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data jabatan berhasil diperbarui!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'jabatan.php';
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal memperbarui data jabatan. Silakan coba lagi.',
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
    <title>Edit Jabatan</title>
    <link rel="stylesheet" href="../assets/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
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
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php" class="active"><i class="fas fa-user-shield"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Edit Jabatan</h1>
                <p>Perbarui data jabatan di bawah ini.</p>
            </header>
            <section class="content-edit">
                <div class="form-container">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="nama_jabatan">Nama Jabatan</label>
                            <input type="text" id="nama_jabatan" name="nama_jabatan" value="<?php echo $jabatan['nama_jabatan']; ?>" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update</button>
                            <a href="jabatan.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
