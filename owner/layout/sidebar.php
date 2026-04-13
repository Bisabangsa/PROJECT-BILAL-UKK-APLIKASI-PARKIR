<?php
// Ambil nama file saat ini
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link d-flex justify-content-center align-items-center">
    <i class="fas fa-parking fa-2x mr-2"></i>
    <span class="brand-text font-weight-light ml-2">E-Parkir</span>
</a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel -->
    

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="dashboard_owner.php" class="nav-link <?= ($currentPage == 'dashboard_owner.php') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="data_laporan.php" class="nav-link <?= ($currentPage == 'data_laporan.php') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Laporan</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="laporan_harian_owner.php" class="nav-link <?= ($currentPage == 'laporan_harian_owner.php') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-money-bill-wave"></i>
            <p>Laporan Pendapatan</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="logout.php" class="nav-link text-danger <?= ($currentPage == 'logout.php') ? 'active' : ''; ?>">
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