<?php
require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

// 🔥 TANPA id_alat (karena tidak ada di tabel)
$sql = "
    SELECT latitude, longitude, created_at
    FROM gps_logs
    ORDER BY created_at DESC
    LIMIT 1
";

$result = $conn->query($sql);

if (!$result) {
    die("SQL ERROR: " . $conn->error);
}

if ($result->num_rows === 0) {
    Response::json([
        "status" => true,
        "data" => [
            "latitude" => 0,
            "longitude" => 0,
            "created_at" => date("Y-m-d H:i:s")
        ]
    ]);
}

$row = $result->fetch_assoc();

Response::json([
    "status" => true,
    "data" => [
        "latitude"  => (float)$row['latitude'],
        "longitude" => (float)$row['longitude'],
        "created_at"=> $row['created_at']
    ]
]);

$conn->close();