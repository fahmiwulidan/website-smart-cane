<?php
// api/save_gps.php

require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

// Ambil data dari POST
$lat = (float)$_POST['lat'];
$lng = (float)$_POST['lng'];

// 🚨 FILTER DATA INVALID
if ($lat == 0 && $lng == 0) {
    echo json_encode([
        "status" => false,
        "msg" => "GPS belum valid (0,0)"
    ]);
    exit;
}

// Tambahkan di awal file API (sebelum validasi $lat dan $lng)
$API_KEY_EXPECTED = "12345-KUNCI-AMAN-7890"; // Ambil dari config/database.php atau env

$received_key = $_POST['api_key'] ?? null;

if ($received_key !== $API_KEY_EXPECTED) {
    Response::json(["status" => false, "msg" => "Unauthorized: Invalid API Key"], 401);
}

// 1. Validasi Data
if (is_null($lat) || is_null($lng)) {
    Response::json(["status" => false, "msg" => "Data (lat/lng) tidak lengkap."], 400);
}

// Tambahkan validasi tipe data (Opsional tapi disarankan)
if (!is_numeric($lat) || !is_numeric($lng)) {
    Response::json(["status" => false, "msg" => "Data GPS harus berupa angka."], 400);
}

// 2. Keamanan: Gunakan Prepared Statements (Wajib!)
$sql = "INSERT INTO gps_logs (latitude, longitude, created_at) VALUES (?, ?, NOW())";

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameter: "dd" berarti dua parameter adalah tipe 'double' (float/decimal)
$stmt->bind_param("dd", $lat, $lng);

// Eksekusi
if ($stmt->execute()) {
    Response::json(["status" => true, "msg" => "GPS berhasil disimpan.", "id" => $conn->insert_id]);
} else {
    // Tampilkan error jika gagal
    Response::json(["status" => false, "msg" => "Gagal menyimpan data GPS: " . $stmt->error], 500);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();

?>