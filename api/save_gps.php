<?php
// api/save_gps.php

require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

/* ==========================
   API KEY
========================== */
$API_KEY_EXPECTED = "12345-KUNCI-AMAN-7890";

$received_key = $_POST['api_key'] ?? null;

if ($received_key !== $API_KEY_EXPECTED) {
    Response::json([
        "status" => false,
        "msg" => "Unauthorized: Invalid API Key"
    ], 401);
    exit;
}

/* ==========================
   AMBIL DATA GPS
========================== */
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;

/* ==========================
   VALIDASI DATA
========================== */
if ($lat === null || $lng === null) {
    Response::json([
        "status" => false,
        "msg" => "Data GPS tidak lengkap."
    ], 400);
    exit;
}

if (!is_numeric($lat) || !is_numeric($lng)) {
    Response::json([
        "status" => false,
        "msg" => "Latitude dan Longitude harus berupa angka."
    ], 400);
    exit;
}

$lat = (float)$lat;
$lng = (float)$lng;

/* ==========================
   VALIDASI GPS
========================== */

// GPS belum mendapatkan koordinat
if ($lat == 0 && $lng == 0) {
    Response::json([
        "status" => false,
        "msg" => "GPS belum valid (0,0)."
    ]);
    exit;
}

// Validasi range koordinat
if (
    $lat < -90 || $lat > 90 ||
    $lng < -180 || $lng > 180
) {
    Response::json([
        "status" => false,
        "msg" => "Koordinat GPS tidak valid."
    ], 400);
    exit;
}

/* ==========================
   CEK DATA TERAKHIR
========================== */

$q = $conn->query("
    SELECT created_at
    FROM gps_logs
    ORDER BY created_at DESC
    LIMIT 1
");

if ($q && $q->num_rows > 0) {

    $row = $q->fetch_assoc();

    $lastTime = strtotime($row['created_at']);
    $nowTime  = time();

    // Jika belum 5 menit sejak penyimpanan terakhir
    if (($nowTime - $lastTime) < 300) {

        Response::json([
            "status" => true,
            "msg" => "Belum 5 menit, GPS tidak disimpan."
        ]);

        $conn->close();
        exit;
    }
}

/* ==========================
   SIMPAN GPS
========================== */

$sql = "
INSERT INTO gps_logs
(latitude, longitude, created_at)
VALUES (?, ?, NOW())
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    Response::json([
        "status" => false,
        "msg" => "Prepare statement gagal.",
        "error" => $conn->error
    ], 500);
    exit;
}

$stmt->bind_param("dd", $lat, $lng);

if ($stmt->execute()) {

    Response::json([
        "status" => true,
        "msg" => "GPS berhasil disimpan.",
        "id" => $conn->insert_id
    ]);

} else {

    Response::json([
        "status" => false,
        "msg" => "Gagal menyimpan GPS.",
        "error" => $stmt->error
    ], 500);
}

$stmt->close();
$conn->close();
?>