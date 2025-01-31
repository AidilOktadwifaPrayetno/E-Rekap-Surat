<?php
include '../includes/db_connect.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get the search keyword
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch karyawan data with jabatan join and pagination
$query = "SELECT k.*, j.nama_jabatan 
          FROM karyawan k 
          LEFT JOIN jabatan j ON k.jabatan_id = j.id ";
if (!empty($search)) {
    $query .= "WHERE k.nama_lengkap LIKE '%$search%' 
               OR k.no_hp LIKE '%$search%' 
               OR j.nama_jabatan LIKE '%$search%' ";
}
$query .= "ORDER BY k.nama_lengkap ASC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

// Count total records for pagination
$count_query = "SELECT COUNT(*) AS total FROM karyawan k 
                LEFT JOIN jabatan j ON k.jabatan_id = j.id";
if (!empty($search)) {
    $count_query .= " WHERE k.nama_lengkap LIKE '%$search%' 
                      OR k.no_hp LIKE '%$search%' 
                      OR j.nama_jabatan LIKE '%$search%'";
}
$count_stmt = mysqli_query($conn, $count_query);
if ($count_row = mysqli_fetch_assoc($count_stmt)) {
    $total_records = $count_row['total'];
    $total_pages = ceil($total_records / $limit);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelaksana Tugas</title>
    <link rel="stylesheet" href="../assets/css/all.css">
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
                <h2>Admin Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php" class="active"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas</a></li>
                <li><a href="jabatan.php"><i class="fas fa-user-shield"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Daftar Pelaksana Tugas</h1>
                <p>Berikut adalah daftar Pelaksana Tugas yang terdaftar dalam sistem.</p>
            </header>
            <section class="content-data-karyawan">
                <div class="actions">
                    <a href="tambah_karyawan.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Pelaksana </a>
                    <!-- Search Form -->
                    <form method="get" action="" class="search-form">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama Pelaksana Tugas...">
                        <button type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>No HP</th>
                                <th>Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo empty($row['no_hp']) || $row['no_hp'] == '0' ? '-' : htmlspecialchars($row['no_hp']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_jabatan']); ?></td>
                                    <td>
                                        <a href="edit_karyawan.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                        <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">Hapus</a>
                                        <script>
                                            function confirmDelete(id) {
                                                Swal.fire({
                                                    title: 'Hapus Karyawan',
                                                    text: 'Yakin ingin menghapus karyawan ini?',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ya, hapus!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = 'delete_karyawan.php?id=' + id;
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                    <td colspan="5">Data tidak ditemukan.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php if ($page > 1) { ?>
                        <a href="?page=1&search=<?php echo urlencode($search); ?>">&laquo; First</a>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo; Prev</a>
                    <?php } ?>
                    
                    <a href="?page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>" class="active"><?php echo $page; ?></a>
                    
                    <?php if ($page < $total_pages) { ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next &raquo;</a>
                        <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>">Last &raquo;</a>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
