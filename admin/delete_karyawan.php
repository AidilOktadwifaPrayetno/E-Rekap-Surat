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

$alertType = '';
$alertMessage = '';
$alertTitle = '';
$alertIcon = '';
$redirectPage = 'karyawan.php';

// Cek apakah ID valid
if ($id <= 0) {
    $alertType = 'INVALID_ID';
    $alertTitle = 'Error!';
    $alertMessage = 'ID karyawan tidak valid.';
    $alertIcon = 'error';
} else {
    // Cek apakah karyawan masih terhubung ke data di SPT
    $checkSpt = mysqli_query($conn, "SELECT 1 FROM spt WHERE karyawan_id = $id LIMIT 1");
    if (mysqli_num_rows($checkSpt) > 0) {
        $alertType = 'SPT_EXISTS';
        $alertTitle = 'Gagal!';
        $alertMessage = 'Tidak bisa menghapus karyawan karena masih memiliki data SPT.';
        $alertIcon = 'warning';
    } else {
        // Lanjutkan hapus karyawan
        $deleteQuery = "DELETE FROM karyawan WHERE id = $id";
        if (mysqli_query($conn, $deleteQuery)) {
            $alertType = 'SUCCESS';
            $alertTitle = 'Berhasil!';
            $alertMessage = 'Data karyawan berhasil dihapus!';
            $alertIcon = 'success';
        } else {
            $alertType = 'FAILED';
            $alertTitle = 'Gagal!';
            $alertMessage = 'Terjadi kesalahan saat menghapus data.';
            $alertIcon = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom Styles (Opsional) -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Favicon -->
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
