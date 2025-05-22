<?php
include '../includes/db_connect.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../logout.php");
    exit;
}

// Cek apakah user sudah login dan adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$alertType = '';
$alertMessage = '';
$alertTitle = '';
$alertIcon = '';
$redirectPage = 'petugas.php';

// Cek apakah ID valid
if ($id <= 0) {
    $alertType = 'INVALID_ID';
    $alertTitle = 'Error!';
    $alertMessage = 'ID petugas tidak valid.';
    $alertIcon = 'error';
} else {
    // Cek apakah petugas masih terhubung ke data di SPT
    $checkSpt = mysqli_query($conn, "SELECT 1 FROM spt WHERE petugas_id = $id LIMIT 1");
    if (mysqli_num_rows($checkSpt) > 0) {
        $alertType = 'SPT_EXISTS';
        $alertTitle = 'Gagal!';
        $alertMessage = 'Tidak bisa menghapus petugas karena masih memiliki data SPT.';
        $alertIcon = 'warning';
    } else {
        // Lanjutkan hapus petugas
        $deleteQuery = "DELETE FROM users WHERE id = $id AND (role = 'petugas' OR role = 'monitor')";
        if (mysqli_query($conn, $deleteQuery)) {
            $alertType = 'SUCCESS';
            $alertTitle = 'Berhasil!';
            $alertMessage = 'Data petugas berhasil dihapus!';
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
    <title>Hapus Petugas</title>
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
