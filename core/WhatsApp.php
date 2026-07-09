<?php

class WhatsApp
{
    /**
     * Konfigurasi WhatsApp Gateway
     */
    protected static $config = [];

    /**
     * Load konfigurasi sekali saja
     */
    protected static function loadConfig()
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../config/whatsapp.php';
        }
    }

    protected static function getTargetNumber()
    {
        self::loadConfig();
        include __DIR__ . '/../config/koneksi.php';

        $query = mysqli_query($conn, "SELECT value FROM settings WHERE nama_setting='nomor_sos'");
        $data = mysqli_fetch_assoc($query);

        if ($data && !empty($data['value'])) {
            return $data['value'];
        }

        // ❌ kalau tidak ada → STOP sistem
        die("Nomor SOS belum diatur di database!");
    }

    /**
     * Kirim notifikasi SOS (dengan koordinat)
     */
    public static function sendSOS($lat, $lng)
    {
        date_default_timezone_set('Asia/Jakarta');
        self::loadConfig();

        if (!self::$config['enabled']) {
            return self::response(false, 'WhatsApp gateway disabled');
        }

        $message = self::buildSOSMessage($lat, $lng);

        return self::sendMessage(
            self::getTargetNumber(),
            $message
        );
    }

    /**
     * Kirim pesan manual (tanpa koordinat GPS)
     */
    public static function sendManualMessage($message)
    {
        date_default_timezone_set('Asia/Jakarta');
        self::loadConfig();

        if (!self::$config['enabled']) {
            return self::response(false, 'WhatsApp gateway disabled');
        }

        return self::sendMessage(
            self::getTargetNumber(),
            $message
        );
    }

    /**
     * Kirim pesan ke nomor tertentu
     */
    public static function sendToNumber($target, $message)
    {
        date_default_timezone_set('Asia/Jakarta');
        self::loadConfig();

        if (!self::$config['enabled']) {
            return self::response(false, 'WhatsApp gateway disabled');
        }

        return self::sendMessage($target, $message);
    }

    /**
     * Format pesan SOS dengan koordinat
     */
    protected static function buildSOSMessage($lat, $lng)
    {
        $time = date('d-m-Y H:i:s');

        $mapsLink = "https://maps.google.com/?q={$lat},{$lng}";

        return
            "⚠️ *DARURAT (SOS)*\n\n" .
            "📅 Waktu:\n{$time}\n\n" .
            "📍 Lokasi:\n" .
            "Latitude : {$lat}\n" .
            "Longitude: {$lng}\n\n" .
            "🗺️ Google Maps:\n{$mapsLink}\n\n" .
            "_Pesan ini dikirim otomatis oleh sistem._";
    }

    /**
     * Kirim pesan ke WhatsApp Gateway
     */
    protected static function sendMessage($target, $message)
    {
        $payload = [
            'target'  => $target,
            'message' => $message
        ];

        if (function_exists('curl_init')) {
            $curl = curl_init(self::$config['api_url']);

            curl_setopt_array($curl, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: ' . self::$config['api_key']
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => self::$config['timeout']
            ]);

            $response = curl_exec($curl);
            $error    = curl_error($curl);

            curl_close($curl);

            if ($error) {
                return self::response(false, $error);
            }

            return self::response(true, 'WhatsApp terkirim', json_decode($response, true));
        }

        $httpOptions = [
            'http' => [
                'method'  => 'POST',
                'header'  => [
                    'Authorization: ' . self::$config['api_key'],
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                'content' => http_build_query($payload),
                'timeout' => self::$config['timeout']
            ]
        ];

        $context = stream_context_create($httpOptions);
        $response = @file_get_contents(self::$config['api_url'], false, $context);

        if ($response === false) {
            $error = error_get_last();
            return self::response(false, $error['message'] ?? 'Gagal mengirim WhatsApp');
        }

        return self::response(true, 'WhatsApp terkirim', json_decode($response, true));
    }

    /**
     * Helper response standar
     */
    protected static function response($status, $message, $data = [])
    {
        return [
            'status'   => $status,
            'message'  => $message,
            'response' => $data
        ];
    }
}