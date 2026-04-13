<?php
include "../config/koneksi.php";
include 'layout/header.php';
include 'layout/sidebar.php';


$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Query detail transaksi
$transaksi = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area = a.id_area
    WHERE t.status='Selesai'
    AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$bulan'
");

// Query summary
$summary = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        COUNT(*) as total_kendaraan,
        SUM(biaya_total) as total_pendapatan,
        SUM(CASE WHEN k.jenis_kendaraan='Motor' THEN 1 ELSE 0 END) as total_motor,
        SUM(CASE WHEN k.jenis_kendaraan='Mobil' THEN 1 ELSE 0 END) as total_mobil
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='Selesai'
    AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$bulan'
"));

$bulanAngka = date('m', strtotime($bulan));
$tahun = date('Y', strtotime($bulan));

$namaBulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

$bulanFormat = $namaBulan[$bulanAngka] . " " . $tahun;
?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Laporan Pendapatan Bulanan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">

          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>


    <section class="content">
      <div class="container-fluid">
        <div class="row mb-3">
    <div class="col-md-4">
        <form method="GET">
            <div class="input-group">
                <input type="month" name="bulan" 
                       class="form-control" 
                       value="<?= $bulan ?>">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <a href="cetak_laporan.php?bulan=<?= $bulan ?>" 
           class="btn btn-success">
           Cetak PDF
        </a>
    </div>
</div>

      <div class="row mt-3">

<div class="col-md-3">
<div class="card bg-success text-white">
<div class="card-body">
<h5>Total Pendapatan</h5>
<h4>Rp <?= number_format($summary['total_pendapatan'] ?? 0) ?></h4>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-primary text-white">
<div class="card-body">
<h5>Total Kendaraan</h5>
<h4><?= $summary['total_kendaraan'] ?? 0 ?></h4>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning text-white">
<div class="card-body">
<h5>Total Motor</h5>
<h4><?= $summary['total_motor'] ?? 0 ?></h4>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-info text-white">
<div class="card-body">
<h5>Total Mobil</h5>
<h4><?= $summary['total_mobil'] ?? 0 ?></h4>
</div>
</div>
</div>

</div>

<hr>
<h4>Detail Transaksi Bulan <?= $bulanFormat ?></h4>

<table id="tabelLaporan" class="table table-bordered table-striped">
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Plat</th>
<th>Jenis</th>
<th>Area</th>
<th>Durasi</th>
<th>Total</th>
</tr>
</thead>
<tbody>

<?php 
$no = 1;
while($row = mysqli_fetch_assoc($transaksi)) :
?>

<tr>
<td><?= $no++ ?></td>
<td><?= date('d-m-Y H:i', strtotime($row['waktu_keluar'])) ?></td>
<td><?= $row['plat_nomor'] ?></td>
<td><?= $row['jenis_kendaraan'] ?></td>
<td><?= $row['nama_area'] ?></td>
<td><?= $row['durasi_jam'] ?> Jam</td>
<td>Rp <?= number_format($row['biaya_total']) ?></td>
</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

</section>



<?php include 'layout/footer.php';?>
<script>
$(document).ready(function() {
    $('#tabelLaporan').DataTable({
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "search": "Cari:",
            "paginate": {
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
});
</script>
