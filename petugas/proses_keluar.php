<?php
session_start();
include "../config/koneksi.php";

$id_parkir = $_POST['id_parkir'];

$data = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT t.*, tf.tarif_per_jam
    FROM tb_transaksi t
    JOIN tb_tarif tf ON t.id_tarif=tf.id_tarif
    WHERE t.id_parkir='$id_parkir'
"));

$masuk = strtotime($data['waktu_masuk']);
$keluar = time();
$durasi = ceil(($keluar - $masuk)/3600);
if($durasi < 1) $durasi = 1;

$total = $durasi * $data['tarif_per_jam'];

mysqli_query($conn,"UPDATE tb_transaksi SET
    waktu_keluar=NOW(),
    durasi_jam='$durasi',
    biaya_total='$total',
    status='Selesai'
    WHERE id_parkir='$id_parkir'");

mysqli_query($conn,"UPDATE tb_area_parkir 
    SET terisi = terisi - 1
    WHERE id_area='".$data['id_area']."'");

echo json_encode([
    'success' => true,
    'total' => number_format($total)
]);