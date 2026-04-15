<?php
require_once  '../../vendor/autoload.php';

//use nusoap_client;

$hasil = null;
$error = null;

// Cek apakah ada input dari form
if (isset($_POST['angka1']) && isset($_POST['angka2'])) {
    $a = (int)$_POST['angka1'];
    $b = (int)$_POST['angka2'];
    $operasi = isset($_POST['operasi']) ? $_POST['operasi'] : (isset($_POST['operation']) ? $_POST['operation'] : 'kali');

    // URL mengarah ke api.php di port 8080
    // Kita tambahkan ?wsdl agar client otomatis mempelajari fungsi yang ada
    $url = "http://localhost/kalkulator-saya/web/server/api.php?wsdl";
    
    $client = new nusoap_client($url, true);

    // Cek error konstruktor
    $err = $client->getError();
    if ($err) {
        $error = "Constructor Error: " . $err;
    }

    // Panggil fungsi sesuai operasi yang dipilih
    switch ($operasi) {
        case 'tambah':
            $result = $client->call('tambah', array('a' => $a, 'b' => $b));
            break;
        case 'kurang':
            $result = $client->call('kurang', array('a' => $a, 'b' => $b));
            break;
        case 'bagi':
            $result = $client->call('bagi', array('a' => $a, 'b' => $b));
            break;
        case 'kali':
        default:
            $result = $client->call('kali', array('a' => $a, 'b' => $b));
            break;
    }


    // Cek apakah ada fault (kesalahan logika server) atau error (kesalahan koneksi)
    if ($client->fault) {
        $error = "Fault: " . print_r($result, true);
    } else {
        $err = $client->getError();
        if ($err) {
            $error = "Error: " . $err;
        } else {
            $hasil = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>NuSOAP Calculator Client</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; padding-top: 50px; background-color: #f4f4f9; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .result { margin-top: 20px; padding: 10px; background-color: #e9f7ef; border-left: 5px solid #28a745; }
        .error { margin-top: 20px; padding: 10px; background-color: #fdeded; border-left: 5px solid #d32f2f; color: #d32f2f; }
    </style>
</head>
<body>

<div class="card">
    <h2>Kalkulator SOAP</h2>
    <form method="POST">
        <input type="number" name="angka1" placeholder="Angka pertama" required>
        <input type="number" name="angka2" placeholder="Angka kedua" required>
        <button type="submit" name="operasi" value="kali">Hitung (Kali)</button>
        <button type="submit" name="operasi" value="tambah">Hitung (Tambah)</button>
        <button type="submit" name="operasi" value="kurang">Hitung (Kurang)</button>
        <button type="submit" name="operasi" value="bagi">Hitung (Bagi)</button>      
    </form>

    <?php if ($hasil !== null): ?>
        <div class="result">
            <strong>Hasil <?= $operasi ?> :</strong> <?= $hasil ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <strong>Terjadi Kesalahan:</strong><br>
            <small><?= $error ?></small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>