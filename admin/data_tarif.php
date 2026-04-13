<?php include "layout/header.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
}

if (isset($_POST['hapus'])) {

    $id = $_POST['id_tarif'];

    mysqli_query($conn, "DELETE FROM tb_tarif WHERE id_tarif='$id'");

    echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Terhapus!',
        text: 'Data tarif berhasil dihapus',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location='data_tarif.php';
    });
    </script>";
}

if (isset($_POST['update'])) {

    $id    = $_POST['id_tarif'];
    $jenis = $_POST['jenis_kendaraan'];
    $tarif = $_POST['tarif_per_jam'];

    mysqli_query($conn, "UPDATE tb_tarif SET
        jenis_kendaraan='$jenis',
        tarif_per_jam='$tarif'
        WHERE id_tarif='$id'
    ");

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data tarif berhasil diupdate',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_tarif.php';
      });
      </script>";
}

// Tambah Data
if (isset($_POST['simpan'])) {

    $jenis = $_POST['jenis_kendaraan'];
    $tarif = $_POST['tarif_per_jam'];

    // CEK DUPLIKAT
    $cek = mysqli_query($conn, "SELECT * FROM tb_tarif 
    WHERE jenis_kendaraan='$jenis'");

    if (mysqli_num_rows($cek) > 0) {

        echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Data Sudah Ada!',
            text: 'Jenis kendaraan dan tarif ini sudah terdaftar',
            confirmButtonText: 'OK'
        });
        </script>";

    } else {

        mysqli_query($conn, "INSERT INTO tb_tarif 
            (jenis_kendaraan, tarif_per_jam)
            VALUES
            ('$jenis','$tarif')
        ");

        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data tarif berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location='data_tarif.php';
        });
        </script>";
    }
}

// Ambil Data
$data = mysqli_query($conn, "SELECT * FROM tb_tarif ORDER BY id_tarif DESC");
?>



<?php include "layout/sidebar.php"; ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Data Tarif Parkir</h1>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah Tarif
        </button>
      </div>

      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Jenis Kendaraan</th>
              <th>Tarif Per Jam</th>
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
              <td><?= $row['jenis_kendaraan']; ?></td>
              <td>Rp <?= number_format($row['tarif_per_jam'],0,',','.'); ?></td>
              <td>
                <button 
                    class="btn btn-warning btn-sm btn-edit"
                    data-id="<?= $row['id_tarif']; ?>"
                    data-jenis="<?= $row['jenis_kendaraan']; ?>"
                    data-tarif="<?= $row['tarif_per_jam']; ?>"
                    data-toggle="modal"
                    data-target="#modalEdit">
                    <i class="fas fa-edit"></i>
                </button>

                <button 
                    class="btn btn-danger btn-sm btn-hapus"
                    data-id="<?= $row['id_tarif']; ?>"
                    data-jenis="<?= $row['jenis_kendaraan']; ?>"
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
          <h4 class="modal-title text-white">Tambah Tarif</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label>Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="Motor">Motor</option>
              <option value="Mobil">Mobil</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tarif Per Jam</label>
            <input type="number" name="tarif_per_jam" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Tarif -->
<div class="modal fade" id="modalHapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-danger">
          <h4 class="modal-title">Hapus Tarif</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_tarif" id="hapus_id">
          <p>Yakin ingin menghapus tarif <strong id="hapus_jenis"></strong> ?</p>
        </div>

        <div class="modal-footer">
          <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Tarif -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">

        <div class="modal-header bg-warning">
          <h4 class="modal-title text-white">Edit Tarif</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id_tarif" id="edit_id">

          <div class="form-group">
            <label>Jenis Kendaraan</label>
            <select name="jenis_kendaraan" id="edit_jenis" class="form-control" required>
              <option value="Motor">Motor</option>
              <option value="Mobil">Mobil</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tarif Per Jam</label>
            <input type="number" name="tarif_per_jam" id="edit_tarif" class="form-control" required>
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
    $("#hapus_jenis").text($(this).data("jenis"));
});
</script>

<script>
$(document).on("click", ".btn-edit", function () {

    $("#edit_id").val($(this).data("id"));
    $("#edit_jenis").val($(this).data("jenis"));
    $("#edit_tarif").val($(this).data("tarif"));

});
</script>