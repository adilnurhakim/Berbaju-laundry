<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
include '../../../include/koneksi.php';

$id_laundry = $_GET['id_laundry'];
$query = mysqli_query($conn, "SELECT l.*, p.pelanggannama, p.pelanggantelp, j.jenis_laundry 
  FROM tb_laundry l
  JOIN tb_pelanggan p ON l.pelangganid = p.pelangganid
  JOIN tb_jenis j ON l.kd_jenis = j.kd_jenis
  WHERE l.id_laundry = '$id_laundry'");
$data = mysqli_fetch_assoc($query);

// Format nomor WA
$wa_number = $data['pelanggantelp'];
if (substr($wa_number, 0, 1) == '0') {
  $wa_number = '+62' . substr($wa_number, 1);
}

// Format pesan
$pesan = "Halo {$data['pelanggannama']},\nTransaksi Laundry Anda:\nID: {$data['id_laundry']}\nJenis: {$data['jenis_laundry']}\nJumlah: {$data['jml_kilo']} Kg\nTotal: Rp " . number_format($data['totalbayar'],0,',','.') . "\nTerima kasih.";

// URL gambar invoice di GCS
$image_url = "https://storage.googleapis.com/singgah/invoice_{$id_laundry}.png";

// Kirim ke API Sidobe
$api_url = "https://api.sidobe.com/wa/v1/send-message-image";
$secret_key = "odhRvxRmfcWcOJJjYgVAoaMmcJLpRbGUhOWirteCOCWsZjclAh";

$payload = [
  "phone" => $wa_number,
  "message" => $pesan,
  "image_url" => $image_url
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "X-Secret-Key: $secret_key",
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Eksekusi cURL dan cek error
$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
  echo json_encode([
    'success' => false,
    'message' => 'Gagal menghubungi API WhatsApp: ' . $curl_error,
    'error_location' => 'cURL Error'
  ]);
  exit;
}

// Cek HTTP code dan response dari Sidobe
$api_result = json_decode($response, true);
if ($http_code != 200 || (isset($api_result['status']) && $api_result['status'] != 'success')) {
  $error_msg = isset($api_result['message']) ? $api_result['message'] : 'HTTP Code: ' . $http_code;
  echo json_encode([
    'success' => false,
    'message' => 'API WhatsApp gagal: ' . $error_msg,
    'error_location' => 'API Response'
  ]);
  exit;
}

// Sukses
echo json_encode([
  'success' => true,
  'message' => 'Pesan WhatsApp telah dikirim!'
]);
?>