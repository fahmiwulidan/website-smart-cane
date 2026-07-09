<?php
require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

// Ambil parameter (opsional)
$id_alat = $_GET['id_alat'] ?? 1;
$limit   = $_GET['limit'] ?? 100; // default 100 data terakhir

// 🔒 Batasi limit biar tidak berat
$limit = (int)$limit;
if ($limit <= 0 || $limit > 1000) {
    $limit = 100;
}

// 🔥 Query ambil history dari gps_logs
$sql = "
    SELECT latitude, longitude, created_at
    FROM gps_logs
    WHERE id_alat = ?
    ORDER BY created_at DESC
    LIMIT ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    Response::json([
        "status" => false,
        "msg" => "Prepare failed",
        "error" => $conn->error
    ], 500);
}

// bind_param: i = integer
$stmt->bind_param("ii", $id_alat, $limit);

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "latitude"  => (float)$row['latitude'],
        "longitude" => (float)$row['longitude'],
        "created_at"=> $row['created_at']
    ];
}

// 🔄 Balik urutan biar dari lama → baru (penting untuk garis map)
$data = array_reverse($data);

Response::json([
    "status" => true,
    "total"  => count($data),
    "data"   => $data
]);

$stmt->close();
$conn->close();