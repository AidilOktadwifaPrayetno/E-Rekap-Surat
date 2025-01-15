<?php
include '../includes/db_connect.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'ketua') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Ambil ID ketua dari session

// Ambil data ketua berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id' AND role = 'ketua'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data ketua tidak ditemukan.";
    exit();
}
$ketua = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($password)) {
        $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username', password = '$password' WHERE id = '$user_id' AND role = 'ketua'";
    } else {
        $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username' WHERE id = '$user_id' AND role = 'ketua'";
    }

    if (mysqli_query($conn, $query)) {
        $message = "Profil berhasil diperbarui!";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: '$message',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'profile.php';
                });
            });
        </script>";
    } else {
        $message = "Terjadi kesalahan: " . mysqli_error($conn);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: '$message',
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
    <title>Profile Ketua</title>
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
                <h2>Ketua Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_ketua.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Profile Ketua</h1>
                <p>Perbarui data pribadi Anda di sini.</p>
            </header>
            <section class="content-profile">
                <div class="form-container">
                    <?php if (isset($message)) { echo "<p style='color: green;'>$message</p>"; } ?>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo $ketua['nama_lengkap']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $ketua['username']; ?>">
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
