<?php include "layout/header.php";

include "../config/koneksi.php";

if ($_SESSION['role'] != 'petugas') {
    header("Location: ../../login.php");
    exit;
}

/* =====================================
   PROSES TRANSAKSI MASUK
===================================== */
if (isset($_POST['masuk'])) {

    $id_kendaraan = $_POST['id_kendaraan'];
    $id_area      = $_POST['id_area'];
    $id_user      = $_SESSION['id_user'];
    $waktu_masuk  = date('Y-m-d H:i:s');

    // Ambil jenis kendaraan
    $k = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT jenis_kendaraan FROM tb_kendaraan 
         WHERE id_kendaraan='$id_kendaraan'"
    ));

    // Ambil tarif
    $t = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM tb_tarif 
         WHERE jenis_kendaraan='".$k['jenis_kendaraan']."'"
    ));

    mysqli_query($conn,"
        INSERT INTO tb_transaksi
        (id_kendaraan, waktu_masuk, id_tarif, status, id_user, id_area)
        VALUES
        ('$id_kendaraan','$waktu_masuk','".$t['id_tarif']."','Masuk','$id_user','$id_area')
    ");

    mysqli_query($conn,"
        UPDATE tb_area_parkir 
        SET terisi = terisi + 1
        WHERE id_area='$id_area'
    ");

    echo "<script>
        alert('Transaksi Masuk Berhasil');
        window.location='transaksi.php';
    </script>";
}


/* =====================================
   PROSES TRANSAKSI KELUAR
===================================== */
if (isset($_POST['keluar'])) {

    $id_parkir = $_POST['id_parkir'];
    $id_area   = $_POST['id_area'];

    $data = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT t.*, tf.tarif_perjam
        FROM tb_transaksi t
        JOIN tb_tarif tf ON t.id_tarif=tf.id_tarif
        WHERE t.id_parkir='$id_parkir'
    "));

    $masuk  = strtotime($data['waktu_masuk']);
    $keluar = time();

    $durasi = ceil(($keluar - $masuk)/3600);
    if ($durasi < 1) $durasi = 1;

    $total = $durasi * $data['tarif_perjam'];

    mysqli_query($conn,"
        UPDATE tb_transaksi SET
            waktu_keluar = NOW(),
            durasi_jam   = '$durasi',
            biaya_total  = '$total',
            status       = 'Selesai'
        WHERE id_parkir='$id_parkir'
    ");

    mysqli_query($conn,"
        UPDATE tb_area_parkir 
        SET terisi = terisi - 1
        WHERE id_area='$id_area'
    ");

    echo "<script>
        alert('Total Bayar: Rp $total');
        window.location='transaksi.php';
    </script>";
}

// Ambil data transaksi
$data = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, a.nama_area
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan=k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area=a.id_area
    ORDER BY t.id_parkir DESC
");
?>




<?php include "layout/sidebar.php"; ?>


<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Data Area Parkir</h1>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
                <a href="kendaraan_masuk.php" class="btn btn-primary">
    <i class="fas fa-plus"></i> Transaksi Kendaraan Masuk
</a>
      </div>

      <div class="card-body">
        <table id="datatable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Plat</th>
                <th>Area</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Status</th>
                <th>Biaya</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($data)) :
            ?>
            <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['plat_nomor']; ?></td>
            <td><?= $row['nama_area']; ?></td>
            <td><?= $row['waktu_masuk']; ?></td>
            <td><?= $row['waktu_keluar'] ?? '-'; ?></td>
            <td>
                <?php if($row['status']=='Masuk'){ ?>
                <span class="badge badge-warning">Masuk</span>
                <?php } else { ?>
                <span class="badge badge-success">Selesai</span>
                <?php } ?>
            </td>
            <td><?= $row['biaya_total'] ? "Rp ".$row['biaya_total'] : "-"; ?></td>
            <td>
                <?php if($row['status']=='Masuk'){ ?>
                    <button 
                        class="btn btn-danger btn-sm btn-keluar" 
                        data-id="<?= $row['id_parkir']; ?>">
                        Keluar
                    </button>
                <?php } else { ?>
                    <a href="kendaraan_keluar.php?id=<?= $row['id_parkir']; ?>" 
                    target="_blank"
                    class="btn btn-success btn-sm">
                    Cetak Struk
                    </a>
                <?php } ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include "layout/footer.php"; ?>
<script>
$(document).on('click', '.btn-keluar', function(e){
    e.preventDefault();

    let id = $(this).data('id');

    Swal.fire({
        title: 'Proses kendaraan keluar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if(result.isConfirmed){

            $.post('proses_keluar.php', {id_parkir: id}, function(response){

                let res = JSON.parse(response);

                if(res.success){

                    Swal.fire({
                        title: 'Berhasil!',
                        html: `
                            <p>Kendaraan berhasil keluar</p>
                            <h3>Total Bayar</h3>
                            <h2>Rp ${res.total}</h2>
                        `,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });

                }

            });

        }

    });

});
</script>

<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "paging": true,        // aktifkan paging
        "lengthChange": true,  // dropdown jumlah record
        "searching": true,     // aktifkan search
        "ordering": true,      // aktifkan sort
        "info": true,          // info record
        "autoWidth": false     // auto width sesuai container
    });
});
</script>