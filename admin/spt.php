<?php
include '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';
$filter_year = isset($_GET['filter_year']) ? $_GET['filter_year'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query to count total records
$count_query = "SELECT COUNT(*) AS total FROM spt 
                LEFT JOIN karyawan ON spt.karyawan_id = karyawan.id 
                LEFT JOIN users AS petugas ON spt.petugas_id = petugas.id 
                WHERE (karyawan.nama_lengkap LIKE '%$search%' OR petugas.nama_lengkap LIKE '%$search%' 
                OR spt.tanggal_pergi LIKE '%$search%' OR spt.tanggal_pulang LIKE '%$search%' 
                OR spt.keterangan LIKE '%$search%')";

if ($filter_month && $filter_year) {
    $count_query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month' AND YEAR(spt.tanggal_pergi) = '$filter_year'";
} elseif ($filter_month) {
    $count_query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month'";
} elseif ($filter_year) {
    $count_query .= " AND YEAR(spt.tanggal_pergi) = '$filter_year'";
}

$count_stmt = mysqli_query($conn, $count_query);
if ($count_row = mysqli_fetch_assoc($count_stmt)) {
    $total_records = $count_row['total'];
    $total_pages = ceil($total_records / $limit);
}

// Query to fetch records
$query = "SELECT spt.*, karyawan.nama_lengkap AS karyawan, petugas.nama_lengkap AS petugas FROM spt 
          LEFT JOIN karyawan ON spt.karyawan_id = karyawan.id 
          LEFT JOIN users AS petugas ON spt.petugas_id = petugas.id 
          WHERE (karyawan.nama_lengkap LIKE '%$search%' OR petugas.nama_lengkap LIKE '%$search%' 
          OR spt.tanggal_pergi LIKE '%$search%' OR spt.tanggal_pulang LIKE '%$search%' 
          OR spt.keterangan LIKE '%$search%')";

if ($filter_month && $filter_year) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month' AND YEAR(spt.tanggal_pergi) = '$filter_year'";
} elseif ($filter_month) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month'";
} elseif ($filter_year) {
    $query .= " AND YEAR(spt.tanggal_pergi) = '$filter_year'";
}

$query .= " ORDER BY spt.tanggal_pergi DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data SPT</title>
    <link rel="stylesheet" href="../assets/css/spt_petugas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .pagination { display: flex; justify-content: center; margin: 20px 0; }
        .pagination a { margin: 0 5px; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; transition: background-color 0.3s, color 0.3s; }
        .pagination a:hover { background-color: #007bff; color: white; }
        .pagination .active { background-color: #007bff; color: white; pointer-events: none; }
        .logo {
            width: 100px;
            height: 100px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/logo.png" alt="Logo" class="logo">
                <h2>Admin Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php" class="active"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Karyawan</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas & Ketua</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Daftar Surat Perintah Tugas (SPT)</h1>
                <p>Data seluruh Surat Perintah Tugas (SPT) yang terdaftar di sistem.</p>
            </header>
            <section class="content-spt">
                <form method="get" action="" class="search-form">
                    <select name="filter_month">
                        <option value="">Pilih Bulan</option>
                        <?php for ($m = 1; $m <= 12; $m++) { ?>
                            <option value="<?php echo $m; ?>" <?php echo ($filter_month == $m) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <select name="filter_year">
                        <option value="">Pilih Tahun</option>
                        <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++) { ?>
                            <option value="<?php echo $y; ?>" <?php echo ($filter_year == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama karyawan, nama petugas, tanggal pergi, tanggal pulang, keterangan...">
                    
                    <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Nama Petugas</th>
                                <th>Tanggal Pergi</th>
                                <th>Tanggal Pulang</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = $offset + 1; 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['karyawan']; ?></td>
                                        <td><?php echo $row['petugas']; ?></td>
                                        <td><?php echo $row['tanggal_pergi']; ?></td>
                                        <td><?php echo $row['tanggal_pulang']; ?></td>
                                        <td><?php echo $row['keterangan']; ?></td>
                                    </tr>
                                <?php } 
                            } else { ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Data tidak ditemukan</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter_month=<?php echo $filter_month; ?>&filter_year=<?php echo $filter_year; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
