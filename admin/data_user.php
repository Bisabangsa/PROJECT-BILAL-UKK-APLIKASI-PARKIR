<?php include "layout/header.php";
include "../config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
}

if (isset($_POST['hapus'])) {

    $id = $_POST['id_user'];

    mysqli_query($conn, "DELETE FROM tb_user WHERE id_user='$id'");

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Terhapus!',
          text: 'Data user berhasil dihapus',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_user.php';
      });
      </script>";
}

if (isset($_POST['update'])) {

    $id     = $_POST['id_user'];
    $nama   = $_POST['nama_lengkap'];
    $user   = $_POST['username'];
    $role   = $_POST['role'];
    $status = $_POST['status_aktif'];
    $pass   = $_POST['password'];

    if (!empty($pass)) {
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE tb_user SET
            nama_lengkap='$nama',
            username='$user',
            password='$pass_hash',
            role='$role',
            status_aktif='$status'
            WHERE id_user='$id'
        ");
    } else {
        mysqli_query($conn, "UPDATE tb_user SET
            nama_lengkap='$nama',
            username='$user',
            role='$role',
            status_aktif='$status'
            WHERE id_user='$id'
        ");
    }

    echo "<script>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data user berhasil diupdate',
          timer: 2000,
          showConfirmButton: false
      }).then(() => {
          window.location='data_user.php';
      });
      </script>";
}


if (isset($_POST['simpan'])) {

    $nama   = $_POST['nama_lengkap'];
    $user   = $_POST['username'];
    $pass   = $_POST['password'];
    $role   = $_POST['role'];
    $status = $_POST['status_aktif'];

    mysqli_query($conn, "INSERT INTO tb_user 
        (nama_lengkap, username, password, role, status_aktif) 
        VALUES 
        ('$nama','$user','$pass','$role','$status')");

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data user berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location='data_user.php';
        });
        </script>";
        }

$data = mysqli_query($conn, "SELECT * FROM tb_user ORDER BY id_user DESC");
?>



<?php include "layout/sidebar.php"; ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Data User</h1>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah User
        </button>
      </div>
      <div class="card-body">
       <table id="datatable" class="table table-bordered table-striped">
       
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
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
              <td><?= $row['nama_lengkap']; ?></td>
              <td><?= $row['username']; ?></td>
              <td><?= ucfirst($row['role']); ?></td>
              <td>
                <?php if ($row['status_aktif'] == 1) { ?>
                  <span class="badge badge-success">Aktif</span>
                <?php } else { ?>
                  <span class="badge badge-danger">Non Aktif</span>
                <?php } ?>
              </td>
              <td>
                <button 
                  class="btn btn-warning btn-sm btn-edit"
                  data-id="<?= $row['id_user']; ?>"
                  data-nama="<?= $row['nama_lengkap']; ?>"
                  data-username="<?= $row['username']; ?>"
                  data-role="<?= $row['role']; ?>"
                  data-status="<?= $row['status_aktif']; ?>"
                  data-toggle="modal" 
                  data-target="#modalEdit">
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  class="btn btn-danger btn-sm btn-hapus"
                  data-id="<?= $row['id_user']; ?>"
                  data-nama="<?= $row['nama_lengkap']; ?>"
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

    <!-- Modal Tambah User -->
<div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <form method="POST">
        <div class="modal-header bg-primary">
          <h4 class="modal-title ">Tambah User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="">-- Pilih Role --</option>
              <option value="admin">Admin</option>
              <option value="petugas">Petugas</option>
              <option value="owner">Owner</option>
            </select>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status_aktif" class="form-control" required>
              <option value="1">Aktif</option>
              <option value="0">Non Aktif</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>
  </section>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-danger">
          <h4 class="modal-title">Hapus User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_user" id="id_user_hapus">
          <p>Yakin ingin menghapus user <strong id="nama_user"></strong> ?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <form method="POST">
        <div class="modal-header bg-warning">
          <h4 class="modal-title text-white">Edit User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id_user" id="edit_id">

          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="edit_username" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Password (kosongkan jika tidak diganti)</label>
            <input type="password" name="password" class="form-control">
          </div>

          <div class="form-group">
            <label>Role</label>
            <select name="role" id="edit_role" class="form-control" required>
              <option value="admin">Admin</option>
              <option value="petugas">Petugas</option>
              <option value="owner">Owner</option>
            </select>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status_aktif" id="edit_status" class="form-control" required>
              <option value="1">Aktif</option>
              <option value="0">Non Aktif</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" name="update" class="btn btn-warning">Update</button>
        </div>

      </form>

    </div>
  </div>
</div>





<?php include "layout/footer.php"; ?>

<!-- script baru -->
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });
});
</script>