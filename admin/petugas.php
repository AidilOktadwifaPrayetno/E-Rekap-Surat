    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    include '../includes/db_connect.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo "<script>window.location.href = '../logout.php?expired=1';</script>";
        exit;
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $limit = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $query = "SELECT * FROM users WHERE role IN ('petugas', 'monitor')";
    if (!empty($search)) {
        $query .= " AND (nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR role LIKE '%$search%')";
    }
    $query .= " ORDER BY nama_lengkap ASC LIMIT $offset, $limit";

    $result = mysqli_query($conn, $query);

    $count_query = "SELECT COUNT(*) AS total FROM users WHERE role IN ('petugas', 'monitor')";
    if (!empty($search)) {
        $count_query .= " AND (nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR role LIKE '%$search%')";
    }
    $count_stmt = mysqli_query($conn, $count_query);
    $total_pages = 1;
    if ($count_row = mysqli_fetch_assoc($count_stmt)) {
        $total_records = $count_row['total'];
        $total_pages = ceil($total_records / $limit);
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Data Petugas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h5>DPRD <br>Provinsi Sumatera Barat</h5>
                    <p>E-REKAP SPT</p>
                </div>
                <ul class="menu">
                    <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="spt.php"><i class="fas fa-file-alt"></i> SPT</a></li>
                    <li><a href="karyawan.php"><i class="fas fa-users"></i> Pelaksana Tugas</a></li>
                    <li><a href="petugas.php" class="active"><i class="fas fa-user-shield"></i> Petugas</a></li>
                    <li><a href="jabatan.php"><i class="fas fa-briefcase"></i> Jabatan</a></li>
                    <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </aside>
            <main class="main-content">
                <header>
                    <h1>Daftar Petugas</h1>
                    <p>Berikut adalah daftar petugas yang terdaftar dalam sistem.</p>
                </header>
                <section class="content-data-petugas">
                    <div class="actions">
                        <a href="tambah_petugas.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Petugas</a>
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
                            <?php $no = ($page - 1) * $limit + 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $row['username'] ?></td>
                                    <td><?= $row['role'] ?></td>
                                    <td>
                                        <div class="button-container">
                                            <a href="edit_petugas.php?id=<?= $row['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>)"><i class="fas fa-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr><td colspan="5" style="text-align: center;">Petugas tidak ditemukan.</td></tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        <?php if ($page > 1) { ?>
                            <a href="?page=1&search=<?= urlencode($search) ?>">&laquo; First</a>
                        <?php } ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)) {
                                echo "<a href='?page=$i&search=" . urlencode($search) . "'" . ($i == $page ? " class='active'" : "") . ">$i</a>";
                            } elseif ($i == 2 || $i == $total_pages - 1) {
                                echo "<span>...</span>";
                            }
                        } ?>
                        <?php if ($page < $total_pages) { ?>
                            <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>">Last &raquo;</a>
                        <?php } ?>
                    </div>
                </section>
            </main>
        </div>

        <script>
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
