<?php
include "../config/koneksi.php";


$id = $_GET['id'];

$query = mysqli_query($conn,"
    SELECT t.*, 
           k.plat_nomor, 
           a.nama_area, 
           tf.tarif_per_jam,
           u.nama_user
    FROM tb_transaksi t
    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN tb_area_parkir a ON t.id_area = a.id_area
    JOIN tb_tarif tf ON t.id_tarif = tf.id_tarif
    JOIN tb_user u ON t.id_user = u.id_user
    WHERE t.id_parkir='$id'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Struk</title>
    <style>
        body{
            font-family: monospace;
        }
        .struk{
            width:300px;
            margin:auto;
            padding:10px;
        }
        .center{
            text-align:center;
        }
        .line{
            text-align:center;
            margin:5px 0;
        }
    </style>
</head>
<body onload="window.print()">

<div class="struk">

    <div class="center">
        <h3>E-PARKING SYSTEM</h3>
        <p>Jl. Raya Lokasi (<?= $data['nama_area'] ?>)</p>
    </div>

    <div class="line">================================</div>

    Petugas : <?= $data['username'] ?><br>
    Plat    : <?= $data['plat_nomor'] ?><br>
    Masuk   : <?= date('d-m-Y H:i', strtotime($data['waktu_masuk'])) ?><br>
    Keluar  : <?= date('d-m-Y H:i', strtotime($data['waktu_keluar'])) ?><br>

    <div class="line">================================</div>

    Durasi  : <?= $data['durasi_jam'] ?> Jam<br>
    Total   : Rp <?= number_format($data['biaya_total']) ?><br>

    <div class="line">================================</div>

    <div class="center">
        Terimakasih atas kunjungan anda<br>
        ------- simpan struk ini -------
    </div>

</div>

</body>
</html>