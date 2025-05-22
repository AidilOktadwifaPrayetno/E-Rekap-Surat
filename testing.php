<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin DPRD</title>

  <!-- Font Awesome CDN -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    /* Sidebar for Desktop */
    .sidebar {
      background-color: #2c3e50;
      color: white;
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .sidebar.hidden-desktop {
      transform: translateX(-100%);
    }

    .sidebar-header {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid #34495e;
    }

    .sidebar-header img {
      width: 60px;
    }

    .sidebar-header h2 {
      margin: 10px 0 0;
      font-size: 16px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar ul li {
      padding: 15px 20px;
      border-bottom: 1px solid #34495e;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .sidebar ul li:hover {
      background-color: #34495e;
    }

    .sidebar ul li i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }

    /* Toggle Button */
    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 10px;
      background-color: #34495e;
      color: white;
      border: none;
      padding: 10px 12px;
      border-radius: 5px;
      cursor: pointer;
      z-index: 1101;
    }

    .toggle-btn:hover {
      background-color: #34495e;
    }

    /* Main Content */
    .main-content {
      margin-left: 270px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }

    .sidebar.hidden-desktop ~ .main-content {
      margin-left: 60px;
    }

    .card {
      background-color: white;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    /* Mobile/Tablet View */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: absolute;
        left: 0;
        top: 0;
        transform: translateY(0);
      }

      .sidebar.hidden-mobile {
        transform: translateY(-100%);
      }

      .main-content {
        margin-left: 0;
        padding-top: 80px;
      }

      .sidebar ul {
        display: flex;
        flex-direction: column;
      }

      .sidebar ul li {
        justify-content: center;
        text-align: center;
        border-bottom: 1px solid #34495e;
      }

      .toggle-btn {
        top: 10px;
        left: 10px;
      }
    }
  </style>
</head>
<body>

  <!-- Toggle Button -->
  <button class="toggle-btn" onclick="toggleMenu()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar / Top Menu -->
  <div class="sidebar hidden-desktop hidden-mobile" id="sidebar">
    <div class="sidebar-header">
      <img src="assets/images/logo.png" alt="Logo">
      <h2>DPRD<br>Sumatera Barat</h2>
    </div>
    <ul>
      <li><i class="fas fa-home"></i> Dashboard</li>
      <li><i class="fas fa-file-alt"></i> SPT</li>
      <li><i class="fas fa-tasks"></i> Pelaksana Tugas</li>
      <li><i class="fas fa-user"></i> Petugas</li>
      <li><i class="fas fa-briefcase"></i> Jabatan</li>
      <li><i class="fas fa-user-cog"></i> Profile</li>
      <li><i class="fas fa-sign-out-alt"></i> Logout</li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="card">
      <h1>Dashboard Admin</h1>
      <p>Selamat Datang, <strong>Admin DPRD Provinsi Sumatera Barat!</strong></p>
    </div>

    <div class="card">
      <h2>SPT</h2>
      <p>Jumlah SPT: <strong>3</strong></p>
      <button>Lihat SPT</button>
    </div>

    <div class="card">
      <h2>Manage Pelaksana Tugas</h2>
      <p>Jumlah Pelaksana Tugas: <strong>7</strong></p>
      <button>Kelola Karyawan</button>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    function toggleMenu() {
      const sidebar = document.getElementById('sidebar');
      const isMobile = window.innerWidth <= 768;

      if (isMobile) {
        sidebar.classList.toggle('hidden-mobile');
      } else {
        sidebar.classList.toggle('hidden-desktop');
      }
    }

    // Optional: Show menu by default on desktop
    window.addEventListener('load', () => {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth > 768) {
        sidebar.classList.remove('hidden-desktop');
      }
    });
  </script>

</body>
</html>
