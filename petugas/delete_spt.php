<?php
session_start();
include '../includes/db_connect.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../logout.php?expired=1");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'ID tidak valid.'];
    header("Location: spt.php");
    exit;
}

$id = intval($_GET['id']);

// Query delete menggunakan prepared statement
$query = "DELETE FROM spt WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Data SPT berhasil dihapus!'];
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Gagal menghapus data SPT: ' . mysqli_error($conn)];
}

mysqli_stmt_close($stmt);

header("Location: spt.php");
exit;
