<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Set session message for SweetAlert
        $_SESSION['login_success'] = "Selamat datang, " . $user['username'] . "!";

        if ($user['role'] == 'admin') {
            header('Location: admin/dashboard_admin.php');
        } elseif ($user['role'] == 'petugas') {
            header('Location: petugas/dashboard_petugas.php');
        } elseif ($user['role'] == 'monitor') {
            header('Location: ketua/dashboard_ketua.php');
        }
    } else {
        $_SESSION['login_message'] = "Login gagal. Username atau password salah.";
        header('Location: index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Arsip SPT</title>
    
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Favicon -->
     <link rel="icon" type="image/png" href="assets/images/logo.png">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (<?php echo isset($_SESSION['login_message']) ? 'true' : 'false'; ?>) {
                Swal.fire({
                    title: 'Notification',
                    text: "<?php echo $_SESSION['login_message']; ?>",
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</head>
<body>
    <div class="login-box">
        <h1>E-Arsip SPT <br> DPRD Provinsi Sumatera Barat</h1>
        <img src="assets/images/logo.png" alt="Logo" class="login-logo">
        <h2>LOGIN</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="login-links">
            <p>Lupa Password? <a href="https://api.whatsapp.com/send/?phone=6281371444187&text&type=phone_number&app_absent=0">Contact Admin</a></p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
