<?php

require_once __DIR__ . '/../core/WhatsApp.php';

// Ambil payload
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Jika bukan JSON, coba POST
if (!$data) {
    $data = $_POST;
}

// Ambil data penting
$sender    = $data['sender'] ?? '';
$timestamp = $data['timestamp'] ?? '';

if (empty($sender) || empty($timestamp)) {
    http_response_code(200);
    exit("OK");
}

/*
|--------------------------------------------------------------------------
| CEGAH DUPLIKAT WEBHOOK
|--------------------------------------------------------------------------
| Fonnte terkadang mengirim event yang sama beberapa kali.
| Kita simpan sender + timestamp agar hanya diproses sekali.
|
*/

$cacheDir = __DIR__ . '/cache';

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$eventFile = $cacheDir . '/' . md5($sender . '_' . $timestamp);

// Jika event sudah pernah diproses
if (file_exists($eventFile)) {
    http_response_code(200);
    exit("OK");
}

// Tandai event sudah diproses
file_put_contents($eventFile, time());

// Pesan balasan
$pesan = "👋 Halo, selamat datang di *SmartCane*!

Terima kasih telah menghubungi kami.

Apabila Anda mengalami kendala, memiliki pertanyaan, atau memerlukan bantuan lebih lanjut, silakan hubungi 📞 *Admin SmartCane* melalui WhatsApp di *0857-1991-6327*.

Kami siap membantu Anda secepat mungkin. Terima kasih.";

// Kirim balasan
WhatsApp::sendToNumber($sender, $pesan);

// Hapus cache yang lebih dari 1 hari agar folder tidak penuh
foreach (glob($cacheDir . '/*') as $file) {
    if (is_file($file) && filemtime($file) < (time() - 86400)) {
        @unlink($file);
    }
}

http_response_code(200);
echo "OK";