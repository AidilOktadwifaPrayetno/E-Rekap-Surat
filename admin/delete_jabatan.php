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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$alertTitle = '';
$alertMessage = '';
$alertIcon = '';
$redirectPage = 'jabatan.php';

// Validasi ID
if ($id <= 0) {
    $alertTitle = 'Gagal!';
    $alertMessage = 'ID jabatan tidak valid.';
    $alertIcon = 'error';
} else {
    // Cek apakah jabatan ada di database
    $checkJabatan = mysqli_query($conn, "SELECT * FROM jabatan WHERE id = $id");
    if (mysqli_num_rows($checkJabatan) == 0) {
        $alertTitle = 'Gagal!';
        $alertMessage = 'Data jabatan tidak ditemukan.';
        $alertIcon = 'error';
    } else {
        // Cek apakah jabatan masih digunakan oleh karyawan
        $checkRelation = mysqli_query($conn, "SELECT 1 FROM karyawan WHERE jabatan_id = $id LIMIT 1");
        if (!$checkRelation) {
            die('Query error: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($checkRelation) > 0) {
            $alertTitle = 'Gagal!';
            $alertMessage = 'Jabatan tidak bisa dihapus karena masih digunakan oleh pelaksana tugas.';
            $alertIcon = 'warning';
        } else {
            // Proses hapus jabatan
            $delete = mysqli_query($conn, "DELETE FROM jabatan WHERE id = $id");
            if ($delete) {
                $alertTitle = 'Berhasil!';
                $alertMessage = 'Jabatan berhasil dihapus!';
                $alertIcon = 'success';
            } else {
                $alertTitle = 'Gagal!';
                $alertMessage = 'Gagal menghapus jabatan: ' . mysqli_error($conn);
                $alertIcon = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Jabatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '<?= $alertTitle ?>',
                text: '<?= $alertMessage ?>',
                icon: '<?= $alertIcon ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '<?= $redirectPage ?>';
            });
        });
    </script>
</body>
</html>
