<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link d-flex justify-content-center align-items-center">
    <i class="fas fa-parking fa-2x mr-2"></i>
    <span class="brand-text font-weight-light ml-2">E-Parkir</span>
</a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      

      <!-- Sidebar Menu -->
      <?php
// Ambil nama file saat ini
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
      <a href="dashboard_admin.php" class="nav-link <?php echo ($currentPage == 'dashboard_admin.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="data_user.php" class="nav-link <?php echo ($currentPage == 'data_user.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-users"></i>
        <p>Kelola User</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="data_tarif.php" class="nav-link <?php echo ($currentPage == 'data_tarif.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-money-bill-wave"></i>
        <p>Data Tarif</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="data_area.php" class="nav-link <?php echo ($currentPage == 'data_area.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-warehouse"></i>
        <p>Area Parkir</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="data_kendaraan.php" class="nav-link <?php echo ($currentPage == 'data_kendaraan.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-car"></i>
        <p>Data Kendaraan</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="laporan_bulanan.php" class="nav-link <?php echo ($currentPage == 'laporan_bulanan.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-money-bill"></i>
        <p>Data Pendapatan</p>
      </a>
    </li>
    
    <li class="nav-item">
  <a href="grafik_pendapatan.php" class="nav-link">
    <i class="nav-icon fas fa-chart-line"></i>
    <p>Grafik Pendapatan </p>
  </a>
</li>

    <li class="nav-item">
      <a href="logout.php" class="nav-link text-danger">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
    </li>

  </ul>
</nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>