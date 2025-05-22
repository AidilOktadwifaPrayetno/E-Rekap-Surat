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
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'petugas') {
    session_unset();
    session_destroy();
    echo "<script>
        sessionStorage.setItem('session_expired', 'true');
        window.location.href = '../index.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id']; // Ambil ID petugas dari session

// Ambil data karyawan
$karyawan = mysqli_query($conn, "SELECT * FROM karyawan");

// Proses tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_POST['karyawan_id'];
    $tanggal_pergi = $_POST['tanggal_pergi'];
    $tanggal_pulang = $_POST['tanggal_pulang'];
    $keterangan = $_POST['keterangan'];

    // Validasi untuk menghindari duplikasi jadwal karyawan
    $check_query = "
        SELECT tanggal_pergi, tanggal_pulang FROM spt 
        WHERE karyawan_id = '$karyawan_id' 
          AND (
              ('$tanggal_pergi' BETWEEN tanggal_pergi AND tanggal_pulang) OR 
              ('$tanggal_pulang' BETWEEN tanggal_pergi AND tanggal_pulang) OR
              (tanggal_pergi BETWEEN '$tanggal_pergi' AND '$tanggal_pulang') OR
              (tanggal_pulang BETWEEN '$tanggal_pergi' AND '$tanggal_pulang')
          )
    ";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $conflicts = [];
        while ($row = mysqli_fetch_assoc($check_result)) {
            $conflicts[] = "Tanggal pergi: " . $row['tanggal_pergi'] . " - Tanggal pulang: " . $row['tanggal_pulang'];
        }
        $error_message = "Jadwal karyawan ini sudah ada pada rentang tanggal yang dipilih:\n" . implode("\n", $conflicts);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: `$error_message`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    } else {
        // Jika validasi berhasil, simpan data
        $query = "INSERT INTO spt (karyawan_id, petugas_id, tanggal_pergi, tanggal_pulang, keterangan) 
                  VALUES ('$karyawan_id', '$user_id', '$tanggal_pergi', '$tanggal_pulang', '$keterangan')";
        if (mysqli_query($conn, $query)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data SPT berhasil ditambahkan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'spt.php';
                        }
                    });
                });
            </script>";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Gagal!',
                        text: '$error_message',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah SPT</title>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <!-- jQuery dan jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Favicon -->
     <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        .ui-autocomplete {
            background-color: #ffffff; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            font-family: 'Arial', sans-serif; 
            font-size: 0.9rem; 
            max-height: 200px;
            overflow-y: auto; 
            overflow-x: hidden; 
            z-index: 1050; 
        }
        .ui-menu-item {
            padding: 10px;
            cursor: pointer;
            color: #2c3e50;
        }
        .ui-menu-item:hover, .ui-state-focus {
            color: #ffffff; 
        }
        .ui-menu {
            padding: 0; 
            margin: 0; 
            list-style: none; 
        }
        .logo {
            width: 100px;
            height: 100px;
            margin: 5px;
        }

        .dropdown-menu {
            position: absolute;
            z-index: 9999;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            padding: 0;
            margin: 0;
            list-style: none;
            display: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .dropdown-item {
            padding: 10px;
            cursor: pointer;
            color: #333;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background-color: #f0f0f0;
        }


        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
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
                <li><a href="dashboard_petugas.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="spt.php" class="active"><i class="fas fa-file-alt"></i>SPT</a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header>
                <h1>Tambah SPT</h1>
                <p>Formulir untuk menambahkan Surat Perintah Tugas baru.</p>
            </header>
            <section class="content-spt">
                <form action="" method="POST" class="form-container">
                    <div class="form-group" style="position: relative;">
                        <label for="karyawan_nama">Nama Pelaksana Tugas</label>
                        <input 
                            type="text" 
                            name="karyawan_nama" 
                            id="karyawan_nama" 
                            placeholder="Ketik nama karyawan" 
                            autocomplete="off" 
                            required>
                        <ul id="karyawan_dropdown" class="dropdown-menu"></ul>
                        <input type="hidden" name="karyawan_id" id="karyawan_id">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pergi">Tanggal Pergi</label>
                        <input type="date" name="tanggal_pergi" id="tanggal_pergi" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pulang">Tanggal Pulang</label>
                        <input type="date" name="tanggal_pulang" id="tanggal_pulang" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="5" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Simpan</button>
                        <a href="spt.php" class="btn-secondary">Kembali</a>
                    </div>
                </form>
            </section>

        </main>
    </div>
    <script>
         $(document).ready(function () {
            const $karyawanInput = $("#karyawan_nama");
            const $dropdown = $("#karyawan_dropdown");

            // Fungsi untuk memuat dropdown
            function loadDropdown(term = "") {
                $.ajax({
                    url: "autocomplete.php", // Path ke API
                    method: "GET",
                    data: { term: term },
                    dataType: "json",
                    success: function (data) {
                        $dropdown.empty(); // Kosongkan dropdown sebelumnya
                        if (data.length > 0) {
                            data.forEach(item => {
                                $dropdown.append(
                                    `<li class="dropdown-item" data-id="${item.id}">${item.label}</li>`
                                );
                            });
                            $dropdown.show();
                        } else {
                            $dropdown.hide();
                        }
                    },
                    error: function () {
                        console.error("Gagal memuat data karyawan.");
                    }
                });
            }

            // Tampilkan semua nama ketika input kosong
            $karyawanInput.on("focus", function () {
                if ($(this).val() === "") {
                    loadDropdown();
                }
            });

            // Filter nama saat mengetik
            $karyawanInput.on("input", function () {
                const term = $(this).val();
                loadDropdown(term);
            });

            // Pilih nama dari dropdown
            $dropdown.on("click", ".dropdown-item", function () {
                const karyawanId = $(this).data("id");
                const karyawanNama = $(this).text();

                $("#karyawan_id").val(karyawanId);
                $karyawanInput.val(karyawanNama);
                $dropdown.hide();
            });

            // Sembunyikan dropdown jika klik di luar elemen
            $(document).on("click", function (e) {
                if (!$(e.target).closest(".form-group").length) {
                    $dropdown.hide();
                }
            });
        });
        
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
