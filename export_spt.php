<?php
require 'includes/db_connect.php';
require 'vendor/autoload.php'; // Pastikan sudah menginstal PhpSpreadsheet dengan Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header('Location: ../login.php');
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
$filter_year = isset($_GET['filter_year']) ? $_GET['filter_year'] : '';

$query = "
    SELECT spt.id, karyawan.nama_lengkap AS nama_karyawan, 
           users.nama_lengkap AS nama_petugas, 
           spt.tanggal_pergi, spt.tanggal_pulang, spt.keterangan
    FROM spt
    JOIN karyawan ON spt.karyawan_id = karyawan.id
    JOIN users ON spt.petugas_id = users.id
    WHERE (
        karyawan.nama_lengkap LIKE ? OR 
        users.nama_lengkap LIKE ? OR 
        spt.keterangan LIKE ? OR 
        spt.tanggal_pergi LIKE ? OR 
        spt.tanggal_pulang LIKE ?
    )";

if ($filter_month && $filter_year) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month' AND YEAR(spt.tanggal_pergi) = '$filter_year'";
} elseif ($filter_month) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month'";
} elseif ($filter_year) {
    $query .= " AND YEAR(spt.tanggal_pergi) = '$filter_year'";
}

$query .= " ORDER BY spt.id DESC";

$stmt = mysqli_prepare($conn, $query);
$search_term = "%$search%";
mysqli_stmt_bind_param($stmt, "sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Nama Pelaksana');
$sheet->setCellValue('C1', 'Nama Petugas');
$sheet->setCellValue('D1', 'Tanggal Pergi');
$sheet->setCellValue('E1', 'Tanggal Pulang');
$sheet->setCellValue('F1', 'Keterangan');

$no = 1;
$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['nama_karyawan']);
    $sheet->setCellValue('C' . $rowNumber, $row['nama_petugas']);
    $sheet->setCellValue('D' . $rowNumber, $row['tanggal_pergi']);
    $sheet->setCellValue('E' . $rowNumber, $row['tanggal_pulang']);
    $sheet->setCellValue('F' . $rowNumber, $row['keterangan']);
    $rowNumber++;
}

$writer = new Xlsx($spreadsheet);
$filename = "Rekap_Surat_Perintah_Tugas_" . date('YmdHis') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
