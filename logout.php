<?php
session_start();
session_unset(); // Hapus semua variabel session
session_destroy(); // Hancurkan session

// Cegah caching halaman logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="60;url=index.php"> <!-- Redirect otomatis jika ditinggal -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Blokir halaman ini agar tidak bisa diakses lewat tombol "Back"
        if (performance.navigation.type === 2) {
            // Jika user tekan tombol Back
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
<script>
    // Tampilkan notifikasi logout berhasil
    Swal.fire({
        title: 'Logout Berhasil',
        text: 'Anda telah keluar dari sistem.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        // Redirect ke halaman login setelah klik OK
        window.location.href = 'index.php';
    });
</script>
</body>
</html>
