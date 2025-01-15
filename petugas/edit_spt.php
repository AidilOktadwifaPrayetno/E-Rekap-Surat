<?php
include '../includes/db_connect.php';
session_start();

// Check if the petugas is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

// Ambil data SPT berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM spt WHERE id = '$id'");
$spt = mysqli_fetch_assoc($result);

// Ambil data karyawan dan petugas
$karyawan = mysqli_query($conn, "SELECT * FROM karyawan");
$petugas = mysqli_query($conn, "SELECT * FROM users WHERE role = 'petugas'");

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_POST['karyawan_id'];
    $petugas_id = $_POST['petugas_id'];
    $tanggal_pergi = $_POST['tanggal_pergi'];
    $tanggal_pulang = $_POST['tanggal_pulang'];
    $keterangan = $_POST['keterangan'];

    $query = "UPDATE spt SET 
              karyawan_id = '$karyawan_id', 
              petugas_id = '$petugas_id', 
              tanggal_pergi = '$tanggal_pergi', 
              tanggal_pulang = '$tanggal_pulang', 
              keterangan = '$keterangan'
              WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $message = "Data SPT berhasil diperbarui!";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: '$message',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'spt.php';
                    }
                });
            });
        </script>";
    } else {
        $errorMessage = "Gagal memperbarui data SPT: " . mysqli_error($conn);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: '$errorMessage',
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
    <title>Edit SPT</title>
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
                <h2>Petugas Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_petugas.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php" class="active"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Edit SPT</h1>
                <p>Formulir untuk mengedit Surat Perintah Tugas.</p>
            </header>
            <section class="content-spt">
                <form action="" method="POST" class="form-container">
                    <div class="form-group">
                        <label for="karyawan_id">Nama Karyawan</label>
                        <select name="karyawan_id" id="karyawan_id" required>
                            <?php while ($row = mysqli_fetch_assoc($karyawan)) { ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $spt['karyawan_id'] ? 'selected' : ''; ?>>
                                    <?php echo $row['nama_lengkap']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="petugas_id">Nama Petugas</label>
                        <select name="petugas_id" id="petugas_id" required>
                            <?php while ($row = mysqli_fetch_assoc($petugas)) { ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $spt['petugas_id'] ? 'selected' : ''; ?>>
                                    <?php echo $row['nama_lengkap']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pergi">Tanggal Pergi</label>
                        <input type="date" name="tanggal_pergi" id="tanggal_pergi" value="<?php echo $spt['tanggal_pergi']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pulang">Tanggal Pulang</label>
                        <input type="date" name="tanggal_pulang" id="tanggal_pulang" value="<?php echo $spt['tanggal_pulang']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="5" required><?php echo $spt['keterangan']; ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <a href="spt.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
