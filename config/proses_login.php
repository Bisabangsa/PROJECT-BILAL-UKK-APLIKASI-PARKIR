<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM tb_user 
    WHERE username='$username' 
    AND password='$password'
    AND status_aktif=1");

$data = mysqli_fetch_assoc($query);

if ($data) {

    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama']    = $data['nama_lengkap'];
    $_SESSION['role']    = $data['role'];

    // simpan log
    mysqli_query($conn, "INSERT INTO tb_log_aktifitas 
        (id_user, aktivitas) 
        VALUES ('".$data['id_user']."', 'Login ".$data['role']."')");

    // Redirect kembali ke login dengan parameter sukses + role
    header("Location: ../index.php?success=".$data['role']);
    exit;

} else {

    header("Location: ../index.php?error=1");
    exit;
}
?>