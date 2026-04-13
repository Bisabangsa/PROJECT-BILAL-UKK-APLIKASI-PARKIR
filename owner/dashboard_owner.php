<?php
include '../config/koneksi.php'; // koneksi database

// Hitung total data laporan (misal dari tb_log_aktifitas)
$data_laporan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_transaksi"));
$data_motor = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as total
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
WHERE k.jenis_kendaraan='Motor'
AND DATE(t.waktu_masuk)=CURDATE()
AND t.waktu_keluar IS NULL
"));

// Mobil yang sedang parkir hari ini
$data_mobil = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as total
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
WHERE k.jenis_kendaraan='Mobil'
AND DATE(t.waktu_masuk)=CURDATE()
AND t.waktu_keluar IS NULL
"));

// Total kendaraan parkir hari ini (motor + mobil)
$data_total_parkir = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) as total
FROM tb_transaksi
WHERE DATE(waktu_masuk) = CURDATE()
"));

// Grafik perbandingan motor vs mobil hari ini
$data_kendaraan = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT 
SUM(CASE WHEN k.jenis_kendaraan='Motor' THEN 1 ELSE 0 END) as motor,
SUM(CASE WHEN k.jenis_kendaraan='Mobil' THEN 1 ELSE 0 END) as mobil
FROM tb_transaksi t
JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
WHERE DATE(t.waktu_masuk) = CURDATE()
"));
?>

  <!-- /.navbar -->
<?php include 'layout/header.php';?>
<?php include 'layout/sidebar.php';?>
  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
               <h3><?= $data_laporan['total']; ?></h3>

                <p>Data Laporan</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
               <h3><?= $data_laporan['total']; ?></h3>

                <p>Data Laporan Pendapatan</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <a href="laporan_harian_owner.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $data_motor['total']; ?></h3>
                <p>Motor Parkir Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-motorcycle"></i>
              </div>
              <a href="#" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?= $data_mobil['total']; ?></h3>
                <p>Mobil Parkir Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-car"></i>
              </div>
              <a href="#" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $data_total_parkir['total']; ?></h3>
                <p>Total Kendaraan Parkir Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-car"></i>
              </div>
              <a href="#" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

        </div>
        <div class="card card-info">
  <div class="card-header">
    <h3 class="card-title">Perbandingan Motor vs Mobil Hari Ini</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="card-body">
    <canvas id="chartKendaraan" style="min-height:250px"></canvas>
  </div>
</div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            
            
          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
  <?php include 'layout/footer.php';?>
  <script>

var ctx = document.getElementById('chartKendaraan').getContext('2d');

var chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Motor', 'Mobil'],
        datasets: [{
            data: [
                <?= $data_kendaraan['motor'] ?? 0 ?>,
                <?= $data_kendaraan['mobil'] ?? 0 ?>
            ],
            backgroundColor: [
                '#28a745',
                '#007bff'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>