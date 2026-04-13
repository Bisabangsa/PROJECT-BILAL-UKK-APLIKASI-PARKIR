<?php include "layout/header.php";
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    $id_kendaraan = mysqli_real_escape_string($conn, $_POST['id_kendaraan']);
    $id_area      = mysqli_real_escape_string($conn, $_POST['id_area']);
    $id_user      = $_SESSION['id_user'];
    $waktu_masuk  = date('Y-m-d H:i:s');

    // 🔎 Ambil jenis kendaraan
    $queryKendaraan = mysqli_query($conn,
        "SELECT jenis_kendaraan FROM tb_kendaraan 
         WHERE id_kendaraan='$id_kendaraan'"
    );

    if(mysqli_num_rows($queryKendaraan) == 0){
        die("Data kendaraan tidak ditemukan!");
    }

    $k = mysqli_fetch_assoc($queryKendaraan);

    // 🔎 Ambil tarif sesuai jenis kendaraan
    $queryTarif = mysqli_query($conn,
        "SELECT * FROM tb_tarif 
         WHERE jenis_kendaraan='".$k['jenis_kendaraan']."'"
    );

    if(mysqli_num_rows($queryTarif) == 0){
        die("Tarif untuk jenis kendaraan <b>".$k['jenis_kendaraan']."</b> belum tersedia!");
    }

    $t = mysqli_fetch_assoc($queryTarif);

    // 🔎 Cek area masih kosong
    $cekArea = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT kapasitas, terisi 
    FROM tb_area_parkir 
    WHERE id_area='$id_area'
"));

if($cekArea['terisi'] >= $cekArea['kapasitas']){
    die("Area parkir sudah penuh!");
}

    // ✅ Insert transaksi
    $insert = mysqli_query($conn,"
    INSERT INTO tb_transaksi
    (id_kendaraan, waktu_masuk, id_tarif, status, id_user, id_area)
    VALUES
    ('$id_kendaraan', NOW(), '".$t['id_tarif']."', 'Masuk', '$id_user', '$id_area')
    ");

    if(!$insert){
        die("Gagal insert transaksi: ".mysqli_error($conn));
    }

    // ✅ Update area jadi terisi
    mysqli_query($conn,"
    UPDATE tb_area_parkir 
    SET terisi = terisi + 1
    WHERE id_area='$id_area'
");

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data transaksi berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location='data_transaksi.php';
        });
        </script>";
}
?>


<?php include "layout/sidebar.php"; ?>

<div class="content-wrapper">
<section class="content-header">
<div class="container-fluid">
<h1>Transaksi Kendaraan Masuk</h1>
</div>
</section>

<section class="content">
<div class="card">
<div class="card-body">

<form method="POST">

<div class="form-group">
<label>Kendaraan</label>
<!-- BARU-->
<select name="id_kendaraan" class="form-control select2">
<option value="">-- Pilih Kendaraan --</option>
<?php
$kendaraan = mysqli_query($conn,"SELECT * FROM tb_kendaraan ORDER BY plat_nomor ASC");
while($k = mysqli_fetch_assoc($kendaraan)){
    echo "<option value='".$k['id_kendaraan']."'>".$k['plat_nomor']." - ".$k['jenis_kendaraan']."</option>";
}
?>
</select>
</div>

<div class="form-group">
<label>Area Parkir</label>
<select name="id_area" class="form-control" required>
<option value="">-- Pilih Area Kosong --</option>
<?php
$area = mysqli_query($conn,"SELECT * FROM tb_area_parkir");
while($a = mysqli_fetch_assoc($area)){
?>
    <option value="<?= $a['id_area'] ?>">
        <?= $a['nama_area'] ?> (Sisa: <?= $a['kapasitas'] - $a['terisi'] ?>)
    </option>
<?php } ?>
</select>
</div>

<button type="submit" name="simpan" class="btn btn-primary">
Simpan
</button>

<a href="data_transaksi.php" class="btn btn-secondary">
Kembali
</a>

</form>

</div>
</div>
</section>
</div>



<?php include "layout/footer.php"; ?>


<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>