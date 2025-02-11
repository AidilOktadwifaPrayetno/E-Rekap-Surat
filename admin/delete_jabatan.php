<?php
include '../includes/db_connect.php';
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil ID jabatan dari parameter URL
$id = $_GET['id'];

// Cek apakah ID jabatan valid
$result = mysqli_query($conn, "SELECT * FROM jabatan WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal!',
                text: 'Data jabatan tidak ditemukan.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'jabatan.php';
            });
        });
    </script>";
    exit;
}

// Hapus jabatan dari tabel
$query = "DELETE FROM jabatan WHERE id = $id";
if (mysqli_query($conn, $query)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Jabatan berhasil dihapus!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'jabatan.php';
            });
        });
    </script>";
} else {
    $errorMessage = mysqli_real_escape_string($conn, "Error: " . mysqli_error($conn));
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal!',
                text: 'Gagal menghapus jabatan. $errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'jabatan.php';
            });
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Petugas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Favicon -->
     <link rel="icon" type="image/png" href="../assets/images/logo.png">
</head>
<body>
    <!-- This page handles deletion logic only -->
</body>
</html>