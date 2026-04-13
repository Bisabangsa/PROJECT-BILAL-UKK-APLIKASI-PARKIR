<?php include "layout/header.php"; 
include "../config/koneksi.php";

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$query = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, a.nama_area
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area = a.id_area
    WHERE DATE(t.waktu_keluar) = '$tanggal'
    AND t.waktu_keluar IS NOT NULL
");

$total = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(biaya_total) as total_pendapatan
    FROM tb_transaksi
    WHERE DATE(waktu_keluar) = '$tanggal'
    AND waktu_keluar IS NOT NULL
"));
?>



<?php include "layout/sidebar.php"; ?>


<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Data Laporan Parkir</h1>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
                </div>
                <div class="col-md-1 mr-4">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
                 <div class="col-md-4">
    <a href="cetak_laporan_harian.php?tanggal=<?= $tanggal ?>" 
       target="_blank"
       class="btn btn-success">
       <i class="fas fa-print"></i> Cetak PDF
    </a>
</div>
            </div>
        </form>
      </div>

      <div class="card-body">
        <table id="tabelLaporan" class="table table-bordered table-striped">
          <thead>
            <tr>
                <th>No</th>
                <th>Plat</th>
                <th>Area</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Durasi</th>
                <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($query)){ 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['plat_nomor'] ?></td>
                <td><?= $row['nama_area'] ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['waktu_masuk'])) ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['waktu_keluar'])) ?></td>
                <td><?= $row['durasi_jam'] ?> Jam</td>
                <td>Rp <?= number_format($row['biaya_total']) ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

  


<?php include "layout/footer.php"; ?>

<script>
$(document).ready(function() {
    $('#tabelLaporan').DataTable({
        pageLength: 10,
        ordering: true,
        responsive: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            search: "Cari:",
            paginate: {
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
