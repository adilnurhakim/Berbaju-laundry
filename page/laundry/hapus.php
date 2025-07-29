<?php 


$id = $_GET['id'];

// menghapus data transaksi laundry
$result = mysqli_query($conn, "DELETE FROM tb_laundry WHERE id_laundry = '$id'");

// hapus file invoice di Google Cloud Storage
require_once __DIR__ . '/../../vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
$bucketName = 'singgah'; // ganti dengan nama bucket kamu
$gcsFileName = "invoice_$id.png";
$storage = new StorageClient([
    'keyFilePath' => __DIR__ . '/../../calocare-eec8fdc4a3f0.json', // path ke credentials
]);
$bucket = $storage->bucket($bucketName);
$object = $bucket->object($gcsFileName);
if ($object->exists()) {
    $object->delete();
}

if ($result) {
  echo "
  <script>
    alert('Data Transaksi berhasil dihapus');
    window.location.href = '?page=laundry';
  </script>
";
}

?>