<?php
include '../config/koneksi.php';
require_once __DIR__ . '/../core/WhatsApp.php';

date_default_timezone_set('Asia/Jakarta');

$nomor = $_POST['nomor_hp'] ?? '';

// normalisasi
$nomor = str_replace('-', '', $nomor);
if (substr($nomor, 0, 1) == "0") {
    $nomor = "62" . substr($nomor, 1);
}

// 🔴 VALIDASI
if (empty($nomor)) {
    echo "Nomor HP wajib diisi!";
    exit;
}

if (!preg_match('/^[0-9]+$/', $nomor)) {
    echo "Nomor HP hanya boleh angka!";
    exit;
}

if (!preg_match('/^(62|08)[0-9]{8,13}$/', $nomor)) {
    echo "Format nomor tidak valid!";
    exit;
}

if (strlen($nomor) < 10 || strlen($nomor) > 15) {
    echo "Panjang nomor tidak valid!";
    exit;
}

// 🔥 CEK USER
$query = mysqli_query($conn, "SELECT * FROM users WHERE nomor_hp='$nomor'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    mysqli_query($conn, "INSERT INTO users (nomor_hp) VALUES ('$nomor')");
}

// 🔥 OTP
$otp = rand(100000, 999999);
$expired = date("Y-m-d H:i:s", strtotime("+5 minutes"));

mysqli_query($conn, "
    UPDATE users 
    SET otp='$otp', otp_expired='$expired'
    WHERE nomor_hp='$nomor'
");

// 🔥 KIRIM WA
$message = "Kode OTP kamu: *$otp*\nBerlaku dalam 5 menit.";
$wa = WhatsApp::sendToNumber($nomor, $message);

if (!$wa['status']) {
        echo "Gagal mengirim OTP: " . $wa['message'];
        exit;
}

echo "success";


