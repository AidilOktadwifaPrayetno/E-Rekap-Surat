<?php
include '../includes/db_connect.php';
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

// First, delete any related records in the 'spt' table
$sptQuery = "DELETE FROM spt WHERE petugas_id = $id";
if (!mysqli_query($conn, $sptQuery)) {
    $errorMessage = mysqli_real_escape_string($conn, "Error: Sistem sedang bermasalah " . mysqli_error($conn));
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal!',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'petugas.php';
            });
        });
    </script>";
    exit;
}

// Then, delete the user from the 'users' table
$query = "DELETE FROM users WHERE id = $id AND (role = 'petugas' OR role = 'ketua')";
if (mysqli_query($conn, $query)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'User berhasil dihapus.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'petugas.php';
            });
        });
    </script>";
} else {
    $errorMessage = mysqli_real_escape_string($conn, "Error deleting user: " . mysqli_error($conn));
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal!',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'petugas.php';
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
</head>
<body>
    <!-- This page handles deletion logic only -->
</body>
</html>
