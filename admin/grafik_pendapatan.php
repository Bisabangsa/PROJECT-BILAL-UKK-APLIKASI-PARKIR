<?php
include "../config/koneksi.php";
include "layout/header.php";
include "layout/sidebar.php";

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Query grafik per tanggal (harian)
$dataChart = [];

$query = mysqli_query($conn, "
    SELECT 
        DAY(t.waktu_keluar) as tanggal,
        SUM(t.biaya_total) as total
    FROM tb_transaksi t
    WHERE t.status='Selesai'
    AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$bulan'
    GROUP BY tanggal
    ORDER BY tanggal ASC
");

while($row = mysqli_fetch_assoc($query)){
    $dataChart[$row['tanggal']] = $row['total'];
}
?>

<div class="content-wrapper">
<section class="content">
<div class="container-fluid">

<h3 class="mb-3">Grafik Pendapatan Bulanan</h3>

<form method="GET" class="mb-3">
    <div class="input-group" style="max-width:300px;">
        <input type="month" name="bulan" class="form-control" value="<?= $bulan ?>">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

<!-- AREA CHART -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Grafik Pendapatan Harian</h3>

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
      <canvas id="areaChart"
        style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;">
      </canvas>
    </div>
  </div>
</div>

</div>
</section>
</div>

<?php include "layout/footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('areaChart');

    if (ctx) {

        const areaChartData = {
            labels: [
                <?php for($i=1; $i<=31; $i++){ echo "'$i',"; } ?>
            ],
            datasets: [{
                label: 'Pendapatan Harian',
                backgroundColor: 'rgba(60,141,188,0.2)',
                borderColor: 'rgba(60,141,188,1)',
                pointRadius: 5,
                pointBackgroundColor: '#3b8bba',
                fill: true,
                data: [
                    <?php for($i=1; $i<=31; $i++){ echo ($dataChart[$i] ?? 0) . ","; } ?>
                ]
            }]
        };

        const areaChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            }
        };

        new Chart(ctx, {
            type: 'line',
            data: areaChartData,
            options: areaChartOptions
        });
    }

});
</script>
