<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../config/database.php";
require_once "../core/Response.php";
require_once "../core/WhatsApp.php";

date_default_timezone_set('Asia/Jakarta');

/* =========================
   AMBIL INPUT
========================= */
$lat    = $_POST['lat'] ?? 0;
$lng    = $_POST['lng'] ?? 0;
$source = $_POST['source'] ?? 'manual';
$force  = isset($_POST['force']) && (int)$_POST['force'] === 1;

/* source hanya boleh manual / fall */
if (!in_array($source, ['manual', 'fall'], true)) {
    $source = 'manual';
}

/* =========================
   VALIDASI GPS
========================= */
$gpsValid = true;

if (!is_numeric($lat) || !is_numeric($lng)) {
    $gpsValid = false;
    $lat = 0;
    $lng = 0;
} else {
    $lat = (float)$lat;
    $lng = (float)$lng;
}

if ($lat == 0 && $lng == 0) {
    $gpsValid = false;
}

if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    $gpsValid = false;
}

/* =========================
   DATABASE
========================= */
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    Response::json([
        "status" => false,
        "msg" => "Koneksi database gagal"
    ], 500);
    exit;
}

/* =========================
   NONAKTIFKAN SOS LAMA
   supaya history tetap tersimpan
   dan hanya 1 SOS aktif terakhir
========================= */
$deactivate = $conn->prepare("UPDATE sos_logs SET is_active = 0 WHERE is_active = 1");
if ($deactivate) {
    $deactivate->execute();
    $deactivate->close();
}

/* =========================
   INSERT SOS BARU
========================= */
$insert = $conn->prepare("
    INSERT INTO sos_logs (latitude, longitude, sos_time, is_active, source)
    VALUES (?, ?, NOW(), 1, ?)
");

if (!$insert) {
    Response::json([
        "status" => false,
        "msg" => "Prepare insert gagal",
        "error" => $conn->error
    ], 500);
    exit;
}

$insert->bind_param("dds", $lat, $lng, $source);

if (!$insert->execute()) {
    Response::json([
        "status" => false,
        "msg" => "Gagal menyimpan SOS",
        "error" => $insert->error
    ], 500);
    exit;
}

$sosId = $conn->insert_id;
$insert->close();

/* =========================
   PESAN WHATSAPP
========================= */
$time = date('d-m-Y H:i:s');

if ($source === 'fall') {
    if ($gpsValid) {
        $message =
            "🚨 *TERDETEKSI JATUH*\n\n" .
            "📅 Waktu:\n{$time}\n\n" .
            "📍 Lokasi:\nhttps://maps.google.com/?q={$lat},{$lng}\n\n" .
            "_Pesan ini dikirim otomatis oleh sistem._";
    } else {
        $message =
            "🚨 *TERDETEKSI JATUH*\n\n" .
            "📅 Waktu:\n{$time}\n\n" .
            "📡 Status GPS:\nTIDAK AKTIF / Belum mendapatkan lokasi.\n\n" .
            "_Pesan ini dikirim otomatis oleh sistem._";
    }
} else {
    if ($gpsValid) {
        $message =
            "⚠️ *DARURAT (SOS)*\n\n" .
            "📅 Waktu:\n{$time}\n\n" .
            "📍 Lokasi:\nhttps://maps.google.com/?q={$lat},{$lng}\n\n" .
            "_Pesan ini dikirim otomatis oleh sistem._";
    } else {
        $message =
            "⚠️ *DARURAT (SOS)*\n\n" .
            "📅 Waktu:\n{$time}\n\n" .
            "📡 Status GPS:\nTIDAK AKTIF / Belum mendapatkan lokasi.\n\n" .
            "_Pesan ini dikirim otomatis oleh sistem._";
    }
}

/* =========================
   KIRIM WHATSAPP
========================= */
$wa = null;

try {
    $wa = WhatsApp::sendManualMessage($message);
} catch (Throwable $e) {
    $wa = [
        "status" => false,
        "error" => $e->getMessage()
    ];
}

/* =========================
   RESPONSE
========================= */
Response::json([
    "status" => true,
    "msg" => "SOS baru berhasil disimpan",
    "id" => $sosId,
    "source" => $source,
    "gps_valid" => $gpsValid,
    "whatsapp_sent" => true,
    "whatsapp" => $wa
]);

$conn->close();
exit;