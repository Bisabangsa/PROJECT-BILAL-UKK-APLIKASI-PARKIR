<?php
session_start();
include "../config/koneksi.php";

// simpan log sebelum logout
if (isset($_SESSION['id_user'])) {
    mysqli_query($conn, "INSERT INTO tb_log_aktifitas 
        (id_user, aktivitas) 
        VALUES ('".$_SESSION['id_user']."', 'Logout')");
}

// hapus semua session
session_unset();
session_destroy();

// arahkan ke login
header("Location: ../index.php");
exit;
?>