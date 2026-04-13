<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../config/koneksi.php";
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;

$id_parkir = $_GET['id'];

// Ambil transaksi
$data = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT t.*, tf.tarif_per_jam, k.plat_nomor, 
           a.nama_area, u.username
    FROM tb_transaksi t
    JOIN tb_tarif tf ON t.id_tarif=tf.id_tarif
    JOIN tb_kendaraan k ON t.id_kendaraan=k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area=a.id_area
    JOIN tb_user u ON t.id_user=u.id_user
    WHERE t.id_parkir='$id_parkir'
"));

// Hitung durasi
$masuk  = strtotime($data['waktu_masuk']);
$keluar = time();

$durasi = ceil(($keluar - $masuk)/3600);
if($durasi < 1) $durasi = 1;

$total = $durasi * $data['tarif_per_jam'];

$waktu_keluar = date('Y-m-d H:i:s', $keluar);

// UPDATE DATABASE (INI YANG  BELUM ADA)
mysqli_query($conn,"
UPDATE tb_transaksi SET
    waktu_keluar='$waktu_keluar',
    durasi_jam='$durasi',
    biaya_total='$total',
    status='Selesai'
WHERE id_parkir='$id_parkir'
");

// Generate struk
$dompdf = new Dompdf();
$html = "
<style>
body{font-family: monospace;font-size:12px;}
.center{text-align:center;}
.line{text-align:center;margin:6px 0;}
table{width:100%;}
td{padding:2px 0;}
.label{width:70px;}
</style>

<div class='center'>
<h3 style='margin:0;'>E-PARKING SYSTEM</h3>
<div>Jl. Raya Lokasi ({$data['nama_area']})</div>
</div>

<div class='line'>================================</div>

<table>
<tr>
<td class='label'>Petugas</td>
<td>: {$data['username']}</td>
</tr>
<tr>
<td class='label'>Plat</td>
<td>: {$data['plat_nomor']}</td>
</tr>
<tr>
<td class='label'>Masuk</td>
<td>: ".date('d-m-Y H:i', $masuk)."</td>
</tr>
<tr>
<td class='label'>Keluar</td>
<td>: ".date('d-m-Y H:i', $keluar)."</td>
</tr>
</table>

<div class='line'>================================</div>

<table>
<tr>
<td class='label'>Durasi</td>
<td>: {$durasi} Jam</td>
</tr>
<tr>
<td class='label'><b>Total</b></td>
<td>: <b>Rp ".number_format($total)."</b></td>
</tr>
</table>

<div class='line'>================================</div>

<div class='center'>
Terimakasih atas kunjungan anda<br>
------- simpan struk ini -------
</div>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A6', 'portrait');
$dompdf->render();
$dompdf->stream("struk_{$data['id_parkir']}.pdf", ["Attachment" => false]);
exit;
?>