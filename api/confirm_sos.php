<?php
require_once "../config/database.php";
require_once "../core/Response.php";

$db = new Database();
$conn = $db->connect();

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    Response::json([
        "status" => false,
        "msg" => "ID SOS tidak valid"
    ], 400);
    exit;
}

// Perbarui hanya jika status masih aktif (1)
$sql = "UPDATE sos_logs SET is_active = 0 WHERE id = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    Response::json([
        "status" => false,
        "msg" => "Gagal mengeksekusi permintaan ke database"
    ], 500);
    $stmt->close();
    $conn->close();
    exit;
}

// Jika tidak ada baris yang diubah, periksa kenapa
if ($stmt->affected_rows > 0) {
    Response::json([
        "status" => true,
        "msg" => "SOS berhasil dikonfirmasi"
    ]);
} else {
    // Cek apakah ID ada dan statusnya sudah 0
    $chk = $conn->prepare("SELECT is_active FROM sos_logs WHERE id = ?");
    $chk->bind_param("i", $id);
    $chk->execute();
    $res = $chk->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ((int)$row['is_active'] === 0) {
            Response::json([
                "status" => false,
                "msg" => "SOS sudah dikonfirmasi sebelumnya"
            ], 200);
        } else {
            Response::json([
                "status" => false,
                "msg" => "Gagal mengubah status SOS"
            ], 500);
        }
    } else {
        Response::json([
            "status" => false,
            "msg" => "ID SOS tidak ditemukan"
        ], 404);
    }

    $chk->close();
}

$stmt->close();
$conn->close();
