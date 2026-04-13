<style>
.custom-progress {
    height: 9px !important;              /* tinggi lebih kecil */
    border-radius: 50px !important;       /* rounded full */
    background-color: #e9ecef; /* warna background soft */
    overflow: hidden !important;
}

.custom-progress .progress-bar {       /* rounded ujung */
    font-size: 10px;
    font-weight: 600;
    line-height: 12px;
    transition: width 0.8s ease-in-out;
}
</style>

<?php include "layout/header.php";

include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
}

if (isset($_POST['hapus'])) {

    $id = $_POST['id_area'];

    // cek apakah slot masih terisi
    $cek = mysqli_query($conn,"SELECT terisi FROM tb_area_parkir WHERE id_area='$id'");
    $data = mysqli_fetch_assoc($cek);

    if($data['terisi'] > 0){

        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Tidak Bisa Dihapus!',
            text: 'Slot sedang terisi tidak bisa dihapus.',
        }).then(() => {
            window.location='data_area.php';
        });
        </script>";

    }else{

        mysqli_query($conn, "DELETE FROM tb_area_parkir WHERE id_area='$id'");

        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Data area berhasil dihapus',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location='data_area.php';
        });
        </script>";

    }
}

if (isset($_POST['update'])) {

    $id        = $_POST['id_area'];
    $nama      = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];

    mysqli_query($conn, "UPDATE tb_area_parkir SET
        nama_area='$nama',
        kapasitas='$kapasitas'
        WHERE id_area='$id'
    ");

    echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data area berhasil diupdate',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location='data_area.php';
    });
    </script>";
}

// Tambah Area
if (isset($_POST['simpan'])) {

    $nama      = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];

    mysqli_query($conn, "INSERT INTO tb_area_parkir 
        (nama_area, kapasitas, terisi)
        VALUES
        ('$nama','$kapasitas', 0)
    ");

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data area berhasil ditambahkan',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_area.php';
      });
      </script>";
}

// Ambil Data
$data = mysqli_query($conn, "SELECT * FROM tb_area_parkir ORDER BY id_area DESC");
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
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah Area
        </button>
      </div>

      <div class="card-body">
        <table id="datatable" class="table table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Area</th>
              <th>Kapasitas</th>
              <th>Terisi</th>
              <th>Sisa Slot</th>
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
              <td><?= $row['nama_area']; ?></td>
              <td><?= $row['kapasitas']; ?></td>
              <?php
                  $kapasitas = $row['kapasitas'];
                  $terisi    = $row['terisi'];
                  $sisa      = $kapasitas - $terisi;

                  $persen = ($kapasitas > 0) ? ($terisi / $kapasitas) * 100 : 0;

                  // Tentukan warna
                  if ($persen <= 50) {
                      $warna = "bg-success";
                  } elseif ($persen <= 80) {
                      $warna = "bg-warning";
                  } else {
                      $warna = "bg-danger";
                  }
                  ?>

                  <td style="width:220px;">
                    
                    <!-- Angka di atas -->
                    <div class="mb-1" style="font-weight:600; font-size:14px;">
                      <?= $terisi ?>
                    </div>

                    <!-- Progress bar di bawah -->
                    <div class="progress custom-progress">
                      <div class="progress-bar <?= $warna ?>" 
                          role="progressbar" 
                          style="width: <?= $persen ?>%;">
                      </div>
                    </div>

                  </td>
              <td>
                <?php if($sisa > 0): ?>
                    <span class="badge badge-success px-3 py-2">
                        <?= $sisa ?> Slot Tersedia
                    </span>
                <?php else: ?>
                    <span class="badge badge-danger px-3 py-2">
                        Penuh
                    </span>
                <?php endif; ?>
              </td>
              <td>
                <button 
                    class="btn btn-warning btn-sm btn-edit"
                    data-id="<?= $row['id_area']; ?>"
                    data-nama="<?= $row['nama_area']; ?>"
                    data-kapasitas="<?= $row['kapasitas']; ?>"
                    data-toggle="modal"
                    data-target="#modalEdit">
                    <i class="fas fa-edit"></i>
                </button>

                <button 
                    class="btn btn-danger btn-sm btn-hapus"
                    data-id="<?= $row['id_area']; ?>"
                    data-nama="<?= $row['nama_area']; ?>"
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
          <h4 class="modal-title text-white">Tambah Area Parkir</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="form-group">
            <label>Nama Area</label>
            <input type="text" name="nama_area" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Kapasitas</label>
            <input type="number" name="kapasitas" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus Area -->
<div class="modal fade" id="modalHapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-danger">
          <h4 class="modal-title">Hapus Area</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_area" id="hapus_id">
          <p>Yakin ingin menghapus area <strong id="hapus_nama"></strong> ?</p>
        </div>

        <div class="modal-footer">
          <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Area -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">

        <div class="modal-header bg-warning">
          <h4 class="modal-title text-white">Edit Area Parkir</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id_area" id="edit_id">

          <div class="form-group">
            <label>Nama Area</label>
            <input type="text" name="nama_area" id="edit_nama" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Kapasitas</label>
            <input type="number" name="kapasitas" id="edit_kapasitas" class="form-control" required>
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
    $("#hapus_nama").text($(this).data("nama"));
});
</script>

<script>
$(document).on("click", ".btn-edit", function () {

    $("#edit_id").val($(this).data("id"));
    $("#edit_nama").val($(this).data("nama"));
    $("#edit_kapasitas").val($(this).data("kapasitas"));

});
</script>

<!-- script paging -->
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true
    });
});
</script>