<?php
include '../includes/db_connect.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../logout.php");
    exit;
}

// Check if the petugas is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header('Location: ../index.php');
    exit;
}

$id = $_GET['id'];

// Hapus data SPT
$query = "DELETE FROM spt WHERE id = '$id'";
if (mysqli_query($conn, $query)) {
    $errorMessage = "Data SPT berhasil dihapus!";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: '$errorMessage',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'spt.php';
            });
        });
    </script>";
} else {
    $errorMessage = "Gagal menghapus data SPT: " . mysqli_error($conn);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal!',
                text: '$errorMessage',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'spt.php';
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
    <title>Hapus Surat Perintah Tugas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <!-- Favicon -->
     <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- This page handles deletion logic only -->
</body>
</html>