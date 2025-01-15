<?php
session_start();
session_destroy();
echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Anda berhasil log out.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php';
            }
        });
    });
</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .logout-box {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logout-header {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .logout-text {
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-box">
            <div class="logout-header">Konfirmasi Log Out</div>
            <div class="logout-text">Yakin ingin log out?</div>
            <button class="logout-button" onclick="confirmLogout()">Ya, log out!</button>
        </div>
    </div>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Log Out',
                text: 'Yakin ingin log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, log out!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    <?php session_destroy(); ?>
                    window.location.href = 'index.php';
                }
            });
        }
    </script>
</body>
</html>
