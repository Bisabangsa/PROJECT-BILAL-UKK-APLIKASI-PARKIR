<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include "../config/koneksi.php";

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Query summary
$summary = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        COUNT(*) as total_kendaraan,
        SUM(biaya_total) as total_pendapatan,
        SUM(CASE WHEN k.jenis_kendaraan='Motor' THEN 1 ELSE 0 END) as total_motor,
        SUM(CASE WHEN k.jenis_kendaraan='Mobil' THEN 1 ELSE 0 END) as total_mobil
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='Selesai'
    AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$bulan'
"));

// Query detail
$transaksi = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area = a.id_area
    WHERE t.status='Selesai'
    AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$bulan'
");

$html = "
<style>
body { font-family: Arial; font-size:12px; }
h2 { text-align:center; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #000; padding:5px; text-align:center; }
.summary { margin-top:10px; }
</style>

<h2>LAPORAN PENDAPATAN PARKIR</h2>
<p>Periode : $bulan</p>

<div class='summary'>
<b>Total Pendapatan :</b> Rp ".number_format($summary['total_pendapatan'] ?? 0)."<br>
<b>Total Kendaraan :</b> ".$summary['total_kendaraan']."<br>
<b>Total Motor :</b> ".$summary['total_motor']."<br>
<b>Total Mobil :</b> ".$summary['total_mobil']."
</div>

<table>
<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Plat</th>
<th>Jenis</th>
<th>Area</th>
<th>Durasi</th>
<th>Total</th>
</tr>
</thead>
<tbody>
";

$no = 1;
while($row = mysqli_fetch_assoc($transaksi)){
$html .= "
<tr>
<td>".$no++."</td>
<td>".date('d-m-Y H:i', strtotime($row['waktu_keluar']))."</td>
<td>".$row['plat_nomor']."</td>
<td>".$row['jenis_kendaraan']."</td>
<td>".$row['nama_area']."</td>
<td>".$row['durasi_jam']." Jam</td>
<td>Rp ".number_format($row['biaya_total'])."</td>
</tr>
";
}

$html .= "</tbody></table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Pendapatan_$bulan.pdf", array("Attachment"=>0));