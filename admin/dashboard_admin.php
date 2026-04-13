<?php
include '../config/koneksi.php'; 
$data7hari = [];
$label7hari = [];

for ($i = 6; $i >= 0; $i--) {

    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $label7hari[] = date('d M', strtotime($tanggal));

    $query = mysqli_query($conn,"
        SELECT SUM(biaya_total) as total
        FROM tb_transaksi
        WHERE status='Selesai'
        AND DATE(waktu_keluar) = '$tanggal'
    ");

    $result = mysqli_fetch_assoc($query);
    $data7hari[] = $result['total'] ?? 0;
}

// Hitung jumlah record di masing-masing tabel
$data_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_user"));
$data_tarif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_tarif"));
$data_area  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_area_parkir"));
$data_kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_kendaraan"));


$labels = [];
$data_masuk = [];
$data_keluar = [];

for ($i = 6; $i >= 0; $i--) {

    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($tanggal));

    // kendaraan masuk
    $masuk = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) as total
        FROM tb_transaksi
        WHERE DATE(waktu_masuk)='$tanggal'
    "));

    // kendaraan keluar
    $keluar = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) as total
        FROM tb_transaksi
        WHERE DATE(waktu_keluar)='$tanggal'
    "));

    $data_masuk[] = $masuk['total'];
    $data_keluar[] = $keluar['total'];
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
                <h3><?= $data_user['total']; ?></h3>

                <p>Data User</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $data_tarif['total']; ?></h3>

                <p>Data Tarif</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                 <h3><?= $data_area['total']; ?></h3>

                <p>Data Area Parkir</p>
              </div>
              <div class="icon">
                <i class="fas fa-warehouse"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $data_kendaraan['total']; ?></h3>
                <p>Data Kendaraan</p>
              </div>
              <div class="icon">
                <i class="fas fa-car"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3><?= $data_kendaraan['total']; ?></h3>
                <p>Pendapatan</p>
              </div>
              <div class="icon">
                <i class="fas fa-car"></i>
              </div>
              <a href="laporan_bulanan.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->

        <!-- interactive chart hiasan aja -->
            <div class="card-body">
                <div id="interactive" style="height: 100px; width: 900px;"></div>
              </div>
         </div>

        <!-- /.row -->
        <!-- Main row -->
        <div class="row">

  <!-- Chart Pendapatan -->
  <div class="col-lg-6">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">Pendapatan 7 Hari Terakhir</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="chart">
          <canvas id="stackedBarChart" style="min-height:250px"></canvas>
        </div>
      </div>
    </div>
  </div>


  <!-- Chart Kendaraan -->
  <div class="col-lg-6">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">Grafik Kendaraan Masuk & Keluar</h3>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="chart">
          <canvas id="lineChart" style="min-height:250px"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  
  
  <?php include 'layout/footer.php';?>
  <script>
var ctx = document.getElementById('stackedBarChart').getContext('2d');

var stackedBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($label7hari); ?>,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: <?= json_encode($data7hari); ?>,
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>

<script>

var lineChartCanvas = $('#lineChart').get(0).getContext('2d')

var lineChartData = {
  labels: <?= json_encode($labels); ?>,
  datasets: [

    {
      label: 'Kendaraan Masuk',
      backgroundColor: 'transparent',
      borderColor: '#28a745',
      pointRadius: 4,
      pointBackgroundColor: '#28a745',
      data: <?= json_encode($data_masuk); ?>
    },

    {
      label: 'Kendaraan Keluar',
      backgroundColor: 'transparent',
      borderColor: '#dc3545',
      pointRadius: 4,
      pointBackgroundColor: '#dc3545',
      data: <?= json_encode($data_keluar); ?>
    }

  ]
}

var lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false
}

new Chart(lineChartCanvas, {
  type: 'line',
  data: lineChartData,
  options: lineChartOptions
})

</script>



<!-- java untuk interactive flot chart -->
<script>
$(function () {

var data = [],
    totalPoints = 100;

function getRandomData() {

  if (data.length > 0) {
    data = data.slice(1);
  }

  while (data.length < totalPoints) {

    var prev = data.length > 0 ? data[data.length - 1] : 50;
    var y = prev + Math.random() * 10 - 5;

    if (y < 0) y = 0;
    if (y > 100) y = 100;

    data.push(y);
  }

  var res = [];
  for (var i = 0; i < data.length; ++i) {
    res.push([i, data[i]]);
  }

  return res;
}

var plot = $.plot("#interactive", [{
  data: getRandomData()
}], {
  series: {
    lines: {
      show: true,
      fill: true
    }
  },
  yaxis: {
    min: 0,
    max: 100,
    show: false   // menghilangkan angka Y
  },
  xaxis: {
    show: false   // menghilangkan angka X
  },
  grid: {
    borderWidth: 0
  }
});

function update() {
  plot.setData([getRandomData()]);
  plot.draw();
  setTimeout(update, 500);
}

update();

});
</script>