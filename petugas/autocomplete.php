<?php
include '../includes/db_connect.php';

if (isset($_GET['term'])) {
    $search = $_GET['term']; // Ambil term dari parameter GET
    $data = [];

    $query = "SELECT id, nama_lengkap FROM karyawan WHERE nama_lengkap LIKE '%$search%'";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id' => $row['id'],
            'label' => $row['nama_lengkap'], // Ini yang akan ditampilkan di dropdown
            'value' => $row['nama_lengkap']  // Nilai yang dimasukkan ke input
        ];
    }

    // Jika tidak ada hasil, tambahkan placeholder
    if (empty($data)) {
        $data[] = [
            'label' => 'Tidak ditemukan',
            'value' => ''
        ];
    }

    echo json_encode($data);
}
?>
