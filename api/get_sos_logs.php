<?php

require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "clear") {
    $delete = $conn->query("DELETE FROM sos_logs");

    if ($delete) {
        Response::json([
            "status" => true,
            "message" => "History SOS berhasil dibersihkan"
        ]);
    }

    Response::json([
        "status" => false,
        "message" => "Gagal membersihkan history SOS"
    ]);
    $conn->close();
    exit;
}

$sql = "SELECT id, latitude, longitude, sos_time, is_active
        FROM sos_logs
        ORDER BY sos_time DESC, id DESC
        LIMIT 10";

$q = $conn->query($sql);

$logs = [];

if ($q && $q->num_rows > 0) {
    while ($row = $q->fetch_assoc()) {
        $logs[] = [
            "id" => (int)$row["id"],
            "lat" => (float)$row["latitude"],
            "lng" => (float)$row["longitude"],
            "time" => $row["sos_time"],
            "is_active" => (bool)$row["is_active"]
        ];
    }
}

Response::json([
    "status" => true,
    "data" => $logs
]);

$conn->close();
