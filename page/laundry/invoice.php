<?php
include '../../include/koneksi.php'; // sesuaikan path koneksi

if (!isset($_GET['id_laundry'])) {
  echo "ID Laundry tidak ditemukan!";
  exit;
}

$id_laundry = $_GET['id_laundry'];
$query = mysqli_query($conn, "SELECT l.*, p.pelanggannama, p.pelanggantelp, j.jenis_laundry 
  FROM tb_laundry l
  JOIN tb_pelanggan p ON l.pelangganid = p.pelangganid
  JOIN tb_jenis j ON l.kd_jenis = j.kd_jenis
  WHERE l.id_laundry = '$id_laundry'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
  echo "Data laundry tidak ditemukan!";
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Invoice Laundry - <?= $data['id_laundry']; ?></title>
  <style>
    body { font-family: Arial, sans-serif; }
    .invoice-box {
      max-width: 500px;
      margin: auto;
      padding: 30px;
      border: 1px solid #eee;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    h2 { text-align: center; }
    table { width: 100%; margin-top: 20px; }
    td { padding: 5px; }
    .total { font-weight: bold; }
  </style>
</head>
<body onload="window.print()">
  <div class="invoice-box">
    <h2>INVOICE LAUNDRY</h2>
    <hr>
    <table>
      <tr>
        <td>ID Laundry</td>
        <td>: <?= $data['id_laundry']; ?></td>
      </tr>
      <tr>
        <td>Nama Pelanggan</td>
        <td>: <?= $data['pelanggannama']; ?></td>
      </tr>
      <tr>
        <td>No. Telp</td>
        <td>: <?= $data['pelanggantelp']; ?></td>
      </tr>
      <tr>
        <td>Jenis Laundry</td>
        <td>: <?= $data['jenis_laundry']; ?></td>
      </tr>
      <tr>
        <td>Tanggal Terima</td>
        <td>: <?= $data['tgl_terima']; ?></td>
      </tr>
      <tr>
        <td>Tanggal Selesai</td>
        <td>: <?= $data['tgl_selesai']; ?></td>
      </tr>
      <tr>
        <td>Jumlah (Kg)</td>
        <td>: <?= $data['jml_kilo']; ?></td>
      </tr>
      <tr>
        <td>Catatan</td>
        <td>: <?= $data['catatan']; ?></td>
      </tr>
      <tr>
        <td class="total">Total Bayar</td>
        <td class="total">: Rp <?= number_format($data['totalbayar'],0,',','.'); ?></td>
      </tr>
      <tr>
        <td>Status</td>
        <td>: <?= $data['status_pembayaran'] == 1 ? 'Lunas' : 'Belum Lunas'; ?></td>
      </tr>
    </table>
    <hr>
    <p style="text-align:center;">Terima kasih telah menggunakan layanan kami!</p>
  </div>
</body>
</html>