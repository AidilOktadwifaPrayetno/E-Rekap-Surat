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
    <link rel="stylesheet" href="../assets/css/spt.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><!-- Favicon -->
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
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            z-index: 1000;
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
                <li><a href="spt.php" class="active"><i class="fas fa-file-alt"></i> SPT</a></li>
                <li><a href="karyawan.php"><i class="fas fa-users"></i>Pelaksana Tugas</a></li>
                <li><a href="petugas.php"><i class="fas fa-user-shield"></i> Petugas </a></li>
                <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Jabatan </a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Daftar Surat Perintah Tugas (SPT)</h1>
                <p>Data seluruh Surat Perintah Tugas (SPT) yang terdaftar di sistem.</p>
            </header>
            <section class="content">
            <div class="actions">
                    <a href="../export_spt.php?search=<?php echo urlencode($search); ?>&filter_month=<?php echo $filter_month; ?>&filter_year=<?php echo $filter_year; ?>" class="btn-primary">
                        <i class="fas fa-file-excel"></i> Export ke Excel
                    </a>
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

        
        // Blok tombol back
history.pushState(null, null, location.href);
window.addEventListener('popstate', function () {
    history.pushState(null, null, location.href);
    Swal.fire({
        icon: 'warning',
        title: 'Sesi Telah Berakhir!',
        text: 'Silakan login kembali.',
        confirmButtonText: 'Login'
    }).then(() => {
        window.location.href = '../index.php';
    });
});

// Deteksi ketika kembali ke tab (tab visibility)
document.addEventListener('visibilitychange', function () {
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
});
    </script>
</body>
</html>
