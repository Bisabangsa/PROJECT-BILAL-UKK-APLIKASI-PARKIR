<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<style>
.login-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-box {
    width: 400px;
}
</style>

<body style="background-color:#016160;">
<div class="login-wrapper">
    <div class="login-box">
  <div class="login-logo">
    <!-- <a href="index2.html" class="text-primary">Parking <b>Retribution</b></a> -->
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body" style="border-radius:50px;">
      <div style="display:flex; justify-content:center;">
        <img src="dist/img/e-parkir.png" alt="" >
      </div>
      <p class="login-box-msg">E-Parkir Login</p>
      <div style="text-align:center; margin-bottom:15px;">
        <h3 id="jam"></h3>
        <small id="tanggal"></small>
</div>

      <form action="config/proses_login.php" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            
          </div>
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-success" style="width:100%">Login Sekarang</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
</div>
    <!-- /.login-card-body -->
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(isset($_GET['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: 'Username atau Password salah / akun tidak aktif!'
});
</script>
<?php endif; ?>


<?php if(isset($_GET['success'])): ?>
<script>
let role = "<?= $_GET['success']; ?>";

Swal.fire({
    icon: 'success',
    title: 'Login Berhasil',
    text: 'Selamat datang!',
    timer: 1500,
    showConfirmButton: false
}).then(() => {

    if(role === "admin"){
        window.location.href = "admin/dashboard_admin.php";
    } 
    else if(role === "petugas"){
        window.location.href = "petugas/dashboard_petugas.php";
    } 
    else if(role === "owner"){
        window.location.href = "owner/dashboard_owner.php";
    }

});
</script>
<?php endif; ?>

<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- /.script jam di login -->
<script>
function updateJam() {
    const now = new Date();

    const waktu = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    const tanggal = now.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    document.getElementById("jam").innerHTML = waktu;
    document.getElementById("tanggal").innerHTML = tanggal;
}

setInterval(updateJam,1000);
updateJam();
</script>

</body>
</html>
