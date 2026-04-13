<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include "../config/koneksi.php";

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$query = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, a.nama_area
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area = a.id_area
    WHERE DATE(t.waktu_keluar) = '$tanggal'
    AND t.waktu_keluar IS NOT NULL
");

$total = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(biaya_total) as total_pendapatan
    FROM tb_transaksi
    WHERE DATE(waktu_keluar) = '$tanggal'
    AND waktu_keluar IS NOT NULL
"));

$html = "
<style>
body { font-family: Arial; font-size:12px; }
h2 { text-align:center; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #000; padding:5px; text-align:center; }
</style>

<h2>LAPORAN PARKIR HARIAN</h2>
<p>Tanggal : ".date('d F Y', strtotime($tanggal))."</p>
<p>Total Pendapatan : Rp ".number_format($total['total_pendapatan'] ?? 0)."</p>

<table>
<tr>
<th>No</th>
<th>Plat</th>
<th>Area</th>
<th>Masuk</th>
<th>Keluar</th>
<th>Durasi</th>
<th>Total</th>
</tr>
";

$no = 1;
while($row = mysqli_fetch_assoc($query)){
$html .= "
<tr>
<td>".$no++."</td>
<td>".$row['plat_nomor']."</td>
<td>".$row['nama_area']."</td>
<td>".date('d-m-Y H:i', strtotime($row['waktu_masuk']))."</td>
<td>".date('d-m-Y H:i', strtotime($row['waktu_keluar']))."</td>
<td>".$row['durasi_jam']." Jam</td>
<td>Rp ".number_format($row['biaya_total'])."</td>
</tr>
";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("Laporan_Harian_$tanggal.pdf", ["Attachment"=>0]);