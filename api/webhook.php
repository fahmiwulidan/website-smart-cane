<?php

require_once __DIR__ . '/../core/WhatsApp.php';

date_default_timezone_set('Asia/Jakarta');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(200);
    exit("No Data");
}

$sender  = $data['sender'] ?? '';
$message = strtolower(trim($data['message'] ?? ''));

if ($sender == '') {
    exit("Sender kosong");
}

$reply = "👋 Halo, selamat datang di *SmartCane!*

Terima kasih telah menghubungi kami.

Apabila Anda mengalami kendala, memiliki pertanyaan, atau memerlukan bantuan lebih lanjut, silakan hubungi 📞 *Admin SmartCane* melalui WhatsApp di *0857-1991-6327*.

Kami siap membantu Anda secepat mungkin. Terima kasih.";

// ==========================
// SEMENTARA JANGAN KIRIM WA
// ==========================

$result = WhatsApp::sendToNumber($sender, $reply);

http_response_code(200);
echo "OK";
exit;
