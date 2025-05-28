<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah caching total
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include '../includes/db_connect.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    session_unset();
    session_destroy();
    echo "<script>
        sessionStorage.setItem('session_expired', 'true');
        window.location.href = '../index.php';
    </script>";
    exit;
}

// Get the search keyword if available
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query to fetch jabatan data with search functionality and pagination
if (!empty($search)) {
    $result = mysqli_query($conn, "SELECT * FROM jabatan WHERE nama_jabatan LIKE '%$search%' ORDER BY nama_jabatan ASC LIMIT $offset, $limit");
} else {
    $result = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan ASC LIMIT $offset, $limit");
}

// Count total records
$count_stmt = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jabatan");
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
    <title>Data Jabatan</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
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
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header" style="border-bottom: 1px solid #ccc; padding-bottom: 0.5px;">
                <img src="../assets/images/logo.png" alt="Logo" class="logo">
                <h5>DPRD <br>Provinsi Sumatera Barat</h5    >
                <p>E-REKAP SPT</p>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas</a></li>
                <li><a href="jabatan.php" class="active"><i class="fas fa-briefcase"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Daftar Jabatan</h1>
                <p>Berikut adalah daftar jabatan yang terdaftar dalam sistem.</p>
            </header>
            <section class="content-data-jabatan">
                <div class="actions">
                    <a href="tambah_jabatan.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Jabatan</a>
                    <!-- Search Form -->
                    <form method="get" action="" class="search-form">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama jabatan...">
                        <button type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Jabatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = ($page - 1) * $limit + 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama_jabatan']; ?></td>
                                    <td>
                                        <div class="button-container">
                                            <a href="edit_jabatan.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fas fa-trash"></i> Delete</a>
                                            <script>
                                                function confirmDelete(id) {
                                                    Swal.fire({
                                                        title: 'Hapus Jabatan',
                                                        text: 'Yakin ingin menghapus jabatan ini?',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#d33',
                                                        cancelButtonColor: '#3085d6',
                                                        confirmButtonText: 'Ya, hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            window.location.href = 'delete_jabatan.php?id=' + id;
                                                        }
                                                    });
                                                }
                                            </script>
                                        </div>    
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) == 0) { ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center;">Jabatan tidak ditemukan.</td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php if ($page > 1) { ?>
                        <a href="?page=1&search=<?php echo urlencode($search); ?>">&laquo; First</a>
                    <?php } ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)) { ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php } elseif ($i == 2 || $i == $total_pages - 1) { ?>
                            <span>...</span>
                        <?php } ?>
                    <?php } ?>
                    
                    <?php if ($page < $total_pages) { ?>
                        <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>">Last &raquo;</a>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const menuLinks = document.querySelectorAll('.menu a');
            const toggleButton = document.querySelector('.toggle-btn');

            // Fungsi untuk menutup sidebar dengan efek delay
            function closeSidebar() {
                setTimeout(function() {
                    sidebar.classList.add('collapsed'); // Menambahkan kelas collapsed setelah delay
                }, 300); // Delay 300ms (sama dengan durasi transisi)
            }

            // Tutup sidebar otomatis saat klik menu di layar kecil
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Pastikan sidebar tertutup saat klik menu pada layar kecil
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                });
            });

            // Toggle sidebar pada button toggle
            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });

            // Tutup sidebar secara default saat halaman pertama kali dimuat di mobile
            if (window.innerWidth < 768) {
                sidebar.classList.add('collapsed');
            }
        });

            function handlePopState() {
                history.pushState(null, null, location.href);
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Telah Berakhir!',
                    text: 'Silakan login kembali.',
                    confirmButtonText: 'Login'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            }

            function handleVisibilityChange() {
                if (document.visibilityState === 'visible') {
                    fetch('../check_session.php')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'expired') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sesi Anda Telah Berakhir',
                                    text: 'Silakan login kembali.',
                                    confirmButtonText: 'Login'
                                }).then(() => {
                                    window.location.href = '../index.php';
                                });
                            }
                        });
                }
            }

            window.addEventListener('popstate', handlePopState);
            document.addEventListener('visibilitychange', handleVisibilityChange);

            function confirmDelete(id) {
                // Nonaktifkan event listener SEBELUM swal muncul
                window.removeEventListener('popstate', handlePopState);
                document.removeEventListener('visibilitychange', handleVisibilityChange);

                Swal.fire({
                    title: 'Hapus Jabatan',
                    text: 'Yakin ingin menghapus Jabatan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete_jabatan.php?id=' + id;
                    } else {
                        // Jika batal, aktifkan kembali listener
                        window.addEventListener('popstate', handlePopState);
                        document.addEventListener('visibilitychange', handleVisibilityChange);
                    }
                });
            }


            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.querySelector('.sidebar');
                const menuLinks = document.querySelectorAll('.menu a');
                const toggleButton = document.querySelector('.toggle-btn');

                function closeSidebar() {
                    setTimeout(() => {
                        sidebar.classList.add('collapsed');
                    }, 300);
                }

                menuLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 768) {
                            closeSidebar();
                        }
                    });
                });

                toggleButton.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                });

                if (window.innerWidth < 768) {
                    sidebar.classList.add('collapsed');
                }
            });

            history.pushState(null, null, location.href);
      
        

    </script>
</body>
</html>
