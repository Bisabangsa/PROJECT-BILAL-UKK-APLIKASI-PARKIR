<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include "../config/koneksi.php";

$tanggal = $_GET['tanggal'];

$summary = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        COUNT(*) as total_kendaraan,
        SUM(biaya_total) as total_pendapatan
    FROM tb_transaksi
    WHERE status='Selesai'
    AND DATE(waktu_keluar) = '$tanggal'
"));

$transaksi = mysqli_query($conn,"
    SELECT t.*, k.plat_nomor, k.jenis_kendaraan
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.status='Selesai'
    AND DATE(t.waktu_keluar) = '$tanggal'
");

$html = "
<h2>Laporan Pendapatan Harian</h2>
<p>Tanggal : ".date('d-m-Y', strtotime($tanggal))."</p>
<p>Total Pendapatan : Rp ".number_format($summary['total_pendapatan'])."</p>
<p>Total Kendaraan : ".$summary['total_kendaraan']."</p>
<hr>
<table border='1' width='100%' cellpadding='5'>
<tr>
<th>No</th>
<th>Plat</th>
<th>Jenis</th>
<th>Durasi</th>
<th>Total</th>
</tr>
";

$no=1;
while($row=mysqli_fetch_assoc($transaksi)){
$html .= "
<tr>
<td>".$no++."</td>
<td>".$row['plat_nomor']."</td>
<td>".$row['jenis_kendaraan']."</td>
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