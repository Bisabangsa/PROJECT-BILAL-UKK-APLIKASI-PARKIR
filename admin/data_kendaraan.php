<?php include "layout/header.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
}

if (isset($_POST['hapus'])) {

    $id = $_POST['id_kendaraan'];

    mysqli_query($conn, "DELETE FROM tb_kendaraan WHERE id_kendaraan='$id'");

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Terhapus!',
          text: 'Data kendaraan berhasil dihapus',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_kendaraan.php';
      });
      </script>";
}

if (isset($_POST['update'])) {

    $id     = $_POST['id_kendaraan'];
    $plat   = $_POST['plat_nomor'];
    $jenis  = $_POST['jenis_kendaraan'];
    $warna  = $_POST['warna'];
    $pemilik= $_POST['pemilik'];

    mysqli_query($conn, "UPDATE tb_kendaraan SET
        plat_nomor='$plat',
        jenis_kendaraan='$jenis',
        warna='$warna',
        pemilik='$pemilik'
        WHERE id_kendaraan='$id'
    ");

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data kendaraan berhasil diupdate',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_kendaraan.php';
      });
      </script>";
}

// Tambah Kendaraan
if (isset($_POST['simpan'])) {

    $plat   = strtoupper($_POST['plat_nomor']);
    $jenis  = $_POST['jenis_kendaraan'];
    $warna  = $_POST['warna'];
    $pemilik= $_POST['pemilik'];

    // CEK DUPLIKAT
    $cek = mysqli_query($conn, "SELECT * FROM tb_kendaraan WHERE plat_nomor='$plat'");

    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Plat nomor sudah terdaftar!'
        });
        </script>";

    } else {

        mysqli_query($conn, "INSERT INTO tb_kendaraan
            (plat_nomor, jenis_kendaraan, warna, pemilik)
            VALUES
            ('$plat','$jenis','$warna','$pemilik')
        ");

        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data kendaraan berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location='data_kendaraan.php';
        });
        </script>";
    }
}

// Ambil Data
$data = mysqli_query($conn, "SELECT * FROM tb_kendaraan ORDER BY id_kendaraan DESC");
?>

<?php include "layout/sidebar.php"; ?>


<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Data Kendaraan</h1>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah Kendaraan
        </button>
      </div>

      <div class="card-body">
        <table id="tabelKendaraan" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Plat Nomor</th>
              <th>Jenis</th>
              <th>Warna</th>
              <th>Pemilik</th>
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
              <td><?= strtoupper($row['plat_nomor']); ?></td>
              <td><?= $row['jenis_kendaraan']; ?></td>
              <td><?= $row['warna']; ?></td>
              <td><?= $row['pemilik']; ?></td>
              <td>
                <button 
                    class="btn btn-warning btn-sm btn-edit"
                    data-id="<?= $row['id_kendaraan']; ?>"
                    data-plat="<?= $row['plat_nomor']; ?>"
                    data-jenis="<?= $row['jenis_kendaraan']; ?>"
                    data-warna="<?= $row['warna']; ?>"
                    data-pemilik="<?= $row['pemilik']; ?>"
                    data-toggle="modal"
                    data-target="#modalEdit">
                    <i class="fas fa-edit"></i>
                </button>

                <button 
                    class="btn btn-danger btn-sm btn-hapus"
                    data-id="<?= $row['id_kendaraan']; ?>"
                    data-plat="<?= $row['plat_nomor']; ?>"
                    data-toggle="modal"
                    data-target="#modalHapus">
                    <i class="fas fa-trash"></i>
                </button>
                </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

   <div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">

        <div class="modal-header bg-primary">
          <h4 class="modal-title text-white">Tambah Kendaraan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label>Plat Nomor</label>
            <input type="text" name="plat_nomor" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="Motor">Motor</option>
              <option value="Mobil">Mobil</option>
            </select>
          </div>

          <div class="form-group">
            <label>Warna</label>
            <input type="text" name="warna" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Pemilik</label>
            <input type="text" name="pemilik" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Kendaraan -->
<div class="modal fade" id="modalHapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">

        <div class="modal-header bg-danger">
          <h4 class="modal-title">Hapus Kendaraan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_kendaraan" id="hapus_id">
          <p>Yakin ingin menghapus kendaraan <strong id="hapus_plat"></strong> ?</p>
        </div>

        <div class="modal-footer">
          <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Kendaraan -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">

        <div class="modal-header bg-warning">
          <h4 class="modal-title text-white">Edit Kendaraan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id_kendaraan" id="edit_id">

          <div class="form-group">
            <label>Plat Nomor</label>
            <input type="text" name="plat_nomor" id="edit_plat" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Jenis Kendaraan</label>
            <select name="jenis_kendaraan" id="edit_jenis" class="form-control" required>
              <option value="Motor">Motor</option>
              <option value="Mobil">Mobil</option>
            </select>
          </div>

          <div class="form-group">
            <label>Warna</label>
            <input type="text" name="warna" id="edit_warna" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Pemilik</label>
            <input type="text" name="pemilik" id="edit_pemilik" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-warning">Update</button>
        </div>

      </form>
    </div>
  </div>
</div>


<?php include "layout/footer.php"; ?>

<script>
$(document).on("click", ".btn-hapus", function () {
    $("#hapus_id").val($(this).data("id"));
    $("#hapus_plat").text($(this).data("plat"));
});
</script>

<script>
$(document).on("click", ".btn-edit", function () {

    $("#edit_id").val($(this).data("id"));
    $("#edit_plat").val($(this).data("plat"));
    $("#edit_jenis").val($(this).data("jenis"));
    $("#edit_warna").val($(this).data("warna"));
    $("#edit_pemilik").val($(this).data("pemilik"));

});
</script>
<!-- script paging-->
<script>
$(function () {
  $("#tabelKendaraan").DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
  });
});
</script>