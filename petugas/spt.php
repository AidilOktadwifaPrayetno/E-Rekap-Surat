<?php
include '../includes/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
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

$petugas_id = $_SESSION['user_id'];

// Get the total number of records
$count_query = "
    SELECT COUNT(*) AS total
    FROM spt
    JOIN karyawan ON spt.karyawan_id = karyawan.id
    JOIN users ON spt.petugas_id = users.id
    WHERE (
        karyawan.nama_lengkap LIKE ? OR 
        users.nama_lengkap LIKE ? OR 
        spt.keterangan LIKE ? OR 
        spt.tanggal_pergi LIKE ? OR 
        spt.tanggal_pulang LIKE ?
    )
";

if ($filter_month && $filter_year) {
    $count_query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month' AND YEAR(spt.tanggal_pergi) = '$filter_year'";
} elseif ($filter_month) {
    $count_query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month'";
} elseif ($filter_year) {
    $count_query .= " AND YEAR(spt.tanggal_pergi) = '$filter_year'";
}

$stmt = mysqli_prepare($conn, $count_query);
$search_term = "%$search%";
mysqli_stmt_bind_param($stmt, "sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total_records);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$total_pages = ceil($total_records / $limit);

// Fetch paginated results
$query = "
    SELECT spt.id, karyawan.nama_lengkap AS nama_karyawan, 
           users.nama_lengkap AS nama_petugas, 
           spt.tanggal_pergi, spt.tanggal_pulang, spt.keterangan, spt.petugas_id
    FROM spt
    JOIN karyawan ON spt.karyawan_id = karyawan.id
    JOIN users ON spt.petugas_id = users.id
    WHERE (
        karyawan.nama_lengkap LIKE ? OR 
        users.nama_lengkap LIKE ? OR 
        spt.keterangan LIKE ? OR 
        spt.tanggal_pergi LIKE ? OR 
        spt.tanggal_pulang LIKE ?
    )
";

if ($filter_month && $filter_year) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month' AND YEAR(spt.tanggal_pergi) = '$filter_year'";
} elseif ($filter_month) {
    $query .= " AND MONTH(spt.tanggal_pergi) = '$filter_month'";
} elseif ($filter_year) {
    $query .= " AND YEAR(spt.tanggal_pergi) = '$filter_year'";
}

$query .= " ORDER BY spt.id DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssssi", $search_term, $search_term, $search_term, $search_term, $search_term, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data SPT</title>
    <link rel="stylesheet" href="../assets/css/spt_petugas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4C+Xv2wU8W6vFJXD4RoKxR95ERIVnvBoG6M0KVE60JXAOFLnUBp8R/bcS7y7zFsh0B5AA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
        .pagination .active {
            background-color: #007bff;
            color: white;
            pointer-events: none;
        }
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
                <h2>Petugas Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_petugas.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php" class="active"><i class="fas fa-file-alt"></i>SPT</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Data Surat Perintah Tugas (SPT)</h1>
                <p>Kelola Data Surat Perintah Tugas (SPT) yang terdaftar di sistem.</p>
            </header>
            <section class="content">
                <div class="actions">
                    <a href="tambah_spt.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah SPT</a>
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
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama Pelaksana, keterangan, tanggal...">
                        
                        <button type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelaksana</th>
                                <th>Nama Petugas</th>
                                <th>Tanggal Pergi</th>
                                <th>Tanggal Pulang</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_karyawan']; ?></td>
                                    <td><?php echo $row['nama_petugas']; ?></td>
                                    <td><?php echo $row['tanggal_pergi']; ?></td>
                                    <td><?php echo $row['tanggal_pulang']; ?></td>
                                    <td><?php echo $row['keterangan']; ?></td>
                                    <td>
                                        <?php if ($row['petugas_id'] == $petugas_id) { ?>
                                            <a href="edit_spt.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                            <script>
                                                function confirmDelete(id) {
                                                    Swal.fire({
                                                        title: 'Hapus SPT',
                                                        text: 'Yakin ingin menghapus SPT ini?',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#d33',
                                                        cancelButtonColor: '#3085d6',
                                                        confirmButtonText: 'Ya, hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            window.location.href = 'delete_spt.php?id=' + id;
                                                        }
                                                    });
                                                }
                                            </script>
                                        <?php } else { ?>
                                            <a href="#" class="btn-success" onclick="return Swal.fire({
                                                title: 'Tidak memiliki Akses',
                                                text: 'Anda tidak memiliki akses untuk mengedit atau menghapus SPT ini.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            })"><i class="fas fa-edit"></i> Tidak memiliki Akses</a>
                                        <?php } ?>
                                    </td>   
                                </tr>
                                
                            <?php } ?>
                            <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">Data tidak ditemukan</td>
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
