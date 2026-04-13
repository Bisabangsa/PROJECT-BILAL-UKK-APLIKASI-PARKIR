<?php
include '../config/koneksi.php'; // koneksi database

// Hitung jumlah record transaksi
$data_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_transaksi"));
$kendaraan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM tb_transaksi 
    WHERE DATE(waktu_masuk) = CURDATE()
"));
// Hitung kendaraan keluar hari ini
$kendaraan_keluar = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM tb_transaksi 
    WHERE DATE(waktu_keluar) = CURDATE()
    AND waktu_keluar IS NOT NULL
"));
// Hitung kendaraan keluar hari ini
$kendaraan_keluar = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM tb_transaksi 
    WHERE DATE(waktu_keluar) = CURDATE()
    AND waktu_keluar IS NOT NULL
"));
// Kendaraan sedang parkir
$sedang_parkir = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM tb_transaksi
    WHERE waktu_keluar IS NULL
"));

$jam = [];
$data_motor = [];
$data_mobil = [];

for ($i=0; $i<24; $i++) {

    $motor = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) as jumlah
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
        WHERE DATE(t.waktu_masuk)=CURDATE()
        AND HOUR(t.waktu_masuk)='$i'
        AND k.jenis_kendaraan='Motor'
    "));

    $mobil = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) as jumlah
        FROM tb_transaksi t
        JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
        WHERE DATE(t.waktu_masuk)=CURDATE()
        AND HOUR(t.waktu_masuk)='$i'
        AND k.jenis_kendaraan='Mobil'
    "));

    $jam[] = $i . ":00";
    $data_motor[] = $motor['jumlah'];
    $data_mobil[] = $mobil['jumlah'];
}
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
                 <h3><?= $data_transaksi['total']; ?></h3>

                <p>Data Transaksi</p>
              </div>
              <div class="icon">
                <i class="fas fa-ticket-alt"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $kendaraan_hari_ini['total']; ?></h3>
                <p>Kendaraan Masuk Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-car"></i>
              </div>
              <a href="data_transaksi.php" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $kendaraan_keluar['total']; ?></h3>
                <p>Kendaraan Keluar Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-sign-out-alt"></i>
              </div>
              <a href="data_transaksi.php" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- Kendaraan Sedang Parkir -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $sedang_parkir['total']; ?></h3>
                <p>Sedang Parkir</p>
              </div>
              <div class="icon">
                <i class="fas fa-parking"></i>
              </div>
              <a href="data_transaksi.php" class="small-box-footer">
                Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>


        </div>

         <!-- STACKED BAR CHART -->
            <div class="card card-success">
  <div class="card-header">
    <h3 class="card-title">Jumlah Kendaraan Masuk Per Jam (Hari Ini)</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="card-body">
    <div class="chart">
      <canvas id="barChart" style="min-height:250px"></canvas>
    </div>
  </div>
</div>
              <!-- /.card-body -->
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

var areaChartData = {
  labels  : <?= json_encode($jam) ?>,
  datasets: [
    {
      label               : 'Motor',
      backgroundColor     : 'rgba(40,167,69,0.9)',
      borderColor         : 'rgba(40,167,69,0.8)',
      data                : <?= json_encode($data_motor) ?>
    },
    {
      label               : 'Mobil',
      backgroundColor     : 'rgba(0,123,255,0.9)',
      borderColor         : 'rgba(0,123,255,0.8)',
      data                : <?= json_encode($data_mobil) ?>
    }
  ]
}

var barChartCanvas = $('#barChart').get(0).getContext('2d')
var barChartData = $.extend(true, {}, areaChartData)

var barChartOptions = {
  responsive              : true,
  maintainAspectRatio     : false,
  datasetFill             : false
}

new Chart(barChartCanvas, {
  type: 'bar',
  data: barChartData,
  options: barChartOptions
})

</script>