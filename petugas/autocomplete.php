<?php
include '../includes/db_connect.php';

// Ambil term (jika ada)
$term = isset($_GET['term']) ? $_GET['term'] : "";

// Query untuk mengambil nama karyawan berdasarkan input, dengan batasan 5 hasil
$query = "SELECT id, nama_lengkap FROM karyawan";
if ($term !== "") {
    $query .= " WHERE nama_lengkap LIKE '%" . mysqli_real_escape_string($conn, $term) . "%'";
}
$query .= " LIMIT 5"; // Batasi hasil hanya 5 nama karyawan

$result = mysqli_query($conn, $query);

// Format hasil sebagai JSON
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => $row['id'],
        'label' => $row['nama_lengkap']
    ];
}

echo json_encode($data);
