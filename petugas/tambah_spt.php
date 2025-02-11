<?php
include '../includes/db_connect.php';
session_start();

// Check if the petugas is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header('Location: ../login.php');
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
    <link rel="stylesheet" href="../assets/css/all.css">

    <!-- jQuery dan jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- SweetAlert2 -->
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
        z-index: 1050;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
        display: none;
    }

    .dropdown-item {
        padding: 10px;
        cursor: pointer;
        color: #333;
    }

    .dropdown-item:hover {
        background-color: #f5f5f5;
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
                <h1>Tambah SPT</h1>
                <p>Formulir untuk menambahkan Surat Perintah Tugas baru.</p>
            </header>
            <section class="content-spt">
                <form action="" method="POST" class="form-container">
                    <div class="form-group">
                        <label for="karyawan_nama">Nama Pelaksana Tugas</label>
                        <input 
                            type="text" 
                            name="karyawan_nama" 
                            id="karyawan_nama" 
                            placeholder="Ketik nama karyawan" 
                            autocomplete="off" 
                            required>
                        <ul id="karyawan_dropdown" class="dropdown-menu"></ul>
                        <input type="hidden" name="karyawan_id" id="karyawan_id"> <!-- ID disimpan di sini -->
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

     <!-- Script Autocomplete -->
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

                $("#karyawan_id").val(karyawanId); // Simpan ID di hidden input
                $karyawanInput.val(karyawanNama); // Tampilkan nama di input
                $dropdown.hide(); // Sembunyikan dropdown
            });

            // Sembunyikan dropdown jika klik di luar elemen
            $(document).on("click", function (e) {
                if (!$(e.target).closest(".form-group").length) {
                    $dropdown.hide();
                }
            });
        });
    </script>


</body>
</html>
