<?php
session_start();
include '../config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');

// ambil input
$nomor = $_POST['nomor_hp'] ?? '';
$otp = $_POST['otp'] ?? '';

// normalisasi nomor
$nomor = str_replace('-', '', $nomor);

if (substr($nomor, 0, 1) == "0") {
    $nomor = "62" . substr($nomor, 1);
}

// query cek OTP
$query = mysqli_query($conn, "
    SELECT * FROM users 
    WHERE nomor_hp='$nomor'
    AND otp='$otp'
    AND otp_expired >= NOW()
");

$user = mysqli_fetch_assoc($query);

if ($user) {

    // ✅ SESSION LOGIN
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];

    // 🔥 TAMBAHAN PENTING
    $_SESSION['nomor_hp'] = $nomor;

    // 🔥 AUTO UPDATE KE SETTINGS
    mysqli_query($conn, "
        UPDATE settings 
        SET value='$nomor' 
        WHERE nama_setting='nomor_sos'
    ");

    echo "success";

} else {
    echo "error";
}
?>