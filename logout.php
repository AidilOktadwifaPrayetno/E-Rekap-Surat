<?php 
session_start();
session_unset();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$expired = isset($_GET['expired']) && $_GET['expired'] == '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const expired = <?= $expired ? 'true' : 'false' ?>;
        
        if (expired) {
            Swal.fire({
                icon: 'warning',
                title: 'Sesi Telah Berakhir!',
                text: 'Silakan login kembali.',
                confirmButtonText: 'Login'
            }).then(() => {
                window.location.href = 'index.php';
            });
        } else {
            Swal.fire({
                title: 'Logout Berhasil',
                text: 'Anda telah keluar dari sistem.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }
    });
</script>
</body>
</html>
