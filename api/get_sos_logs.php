<?php
require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

$sql = "SELECT * FROM sos_logs 
        WHERE is_active = 1 
        ORDER BY id DESC 
        LIMIT 1";

$q = $conn->query($sql);

if ($q && $q->num_rows > 0) {
    $row = $q->fetch_assoc();

    Response::json([
        "status" => true,
        "is_active" => true,
        "data" => [
            "id" => $row["id"],
            "lat" => (float)$row["latitude"],
            "lng" => (float)$row["longitude"],
            "time" => $row["sos_time"]
        ]
    ]);
} else {
    Response::json([
        "status" => true,
        "is_active" => false
    ]);
}

$conn->close();
