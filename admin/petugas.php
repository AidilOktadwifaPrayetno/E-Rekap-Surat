<?php
include '../includes/db_connect.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get the search keyword if available
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query to fetch petugas data with search functionality and pagination
if (!empty($search)) {
    $result = mysqli_query($conn, "SELECT * FROM users WHERE role IN ('petugas', 'monitor') AND nama_lengkap LIKE '%$search%' ORDER BY nama_lengkap ASC LIMIT $offset, $limit");
} else {
    $result = mysqli_query($conn, "SELECT * FROM users WHERE role IN ('petugas', 'monitor') ORDER BY nama_lengkap ASC LIMIT $offset, $limit");
}

// Count total records
$count_stmt = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role IN ('petugas', 'monitor')");
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
    <title>Data Petugas </title>
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
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Pelaksana Tugas </a></li>
                <li><a href="petugas.php" class="active"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php"><i class="fas fa-user-shield"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Daftar Petugas </h1>
                <p>Berikut adalah daftar petugas  yang terdaftar dalam sistem.</p>
            </header>
            <section class="content-data-petugas">
                <div class="actions">
                    <a href="tambah_petugas.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Petugas</a>
                    <!-- Search Form -->
                    <form method="get" action="" class="search-form">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama Petugas atau Ketua...">
                        <button type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td>
                                        <a href="edit_petugas.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fas fa-trash"></i> Delete</a>
                                        <script>
                                            function confirmDelete(id) {
                                                Swal.fire({
                                                    title: 'Hapus Petugas',
                                                    text: 'Yakin ingin menghapus petugas ini?',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ya, hapus!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location.href = 'delete_petugas.php?id=' + id;
                                                    }
                                                });
                                            }
                                        </script>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) == 0) { ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Petugas tidak ditemukan.</td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
