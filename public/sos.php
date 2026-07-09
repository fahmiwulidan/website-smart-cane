<?php
session_start(); 

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

    header("Location: login.php"); 
    exit; 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log SOS Darurat</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #EBF4FF 0%, #C3DAFE 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
}

/* Card umum */
.card {
    background: #fff;
    border-radius: 20px;
    padding: 22px;
    margin-bottom: 18px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
}

/* ================= HEADER ================= */
.header-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    gap: 18px;
}

.back-btn {
    width: 46px;
    height: 46px;
    min-width: 46px;
    min-height: 46px;

    background: #2563EB;
    border-radius: 50%;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    text-decoration: none;
    flex-shrink: 0; /* 🔥 INI KUNCI */
}

.back-btn i {
    color: #fff;
    font-size: 18px;
    line-height: 1;
}

.header-content h1 {
    font-size: 26px;
    color: #1F2937;
    font-weight: 700;
    margin-right: 600px;
}

.header-content p {
    color: #6B7280;
    font-size: 14px;
}

/* ================= SOS CARD ================= */
.sos-card {
    border-left: 6px solid #DC2626;
    background: linear-gradient(135deg, #FFF5F5, #FFFFFF);
}

.sos-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #DC2626;
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 14px;
}

.sos-title i {
    font-size: 20px;
}

.sos-row {
    font-size: 15px;
    color: #1F2937;
    margin-bottom: 10px;
    line-height: 1.6;
}

.sos-row strong {
    color: #374151;
    font-weight: 600;
}

/* ================= BUTTON ================= */
.btn-map {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    background: #2563EB;
    color: #fff;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
}

.btn-map:hover {
    background: #1D4ED8;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(37,99,235,0.4);
}

/* ================= EMPTY ================= */
.empty-state {
    text-align: center;
    padding: 50px 20px;
    background: linear-gradient(135deg, #F0FDF4, #FFFFFF);
    border: 2px dashed #A7F3D0;
}

.empty-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 14px;
    background: #D1FAE5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon i {
    font-size: 32px;
    color: #059669;
}

.empty-state h3 {
    font-size: 18px;
    color: #065F46;
    margin-bottom: 6px;
    font-weight: 700;
}

.empty-state p {
    font-size: 14px;
    color: #047857;
    line-height: 1.6;
}

/* ================= RESPONSIVE TABLET ================= */
@media (max-width: 768px) {

    body {
        padding: 14px;
    }

    .card {
        padding: 18px;
    }

    .header-content h1 {
        font-size: 22px;
        margin-right: 10px;
    }

    .sos-title {
        font-size: 16px;
    }

    .sos-row {
        font-size: 14px;
    }

    .btn-map {
        width: 100%;
        justify-content: center;
        padding: 12px;
    }
}

/* ================= RESPONSIVE HANDPHONE ================= */
@media (max-width: 480px) {

    body {
        padding: 14px;
    }

    .card {
        padding: 18px;
    }

    .header-content h1 {
        font-size: 22px;
        margin-right: 10px;
    }

    .sos-title {
        font-size: 16px;
    }

    .sos-row {
        font-size: 14px;
    }

    .btn-map {
        width: 100%;
        justify-content: center;
        padding: 12px;
    }
}

</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="card header-card">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="header-content">
            <h1>Log SOS Darurat</h1>
            <p>Riwayat panggilan darurat terakhir dari tongkat pintar</p>
        </div>

        <a href="settings.php" class="back-btn">
            <i class="fas fa-gear"></i>
        </a>
    </div>

        <div id="sosContainer"></div>

        <div id="emptyState" class="empty" style="display:none;">
            <i class="fas fa-clipboard-list" style="font-size:32px;"></i>
            <p>Belum ada data SOS</p>
        </div>
    </div>

</div>

<script>
const BASE_URL = "http://localhost/Website IoT Tongkat Pintar/";
const SOS_API_URL = BASE_URL + "api/get_sos_logs.php";

async function loadSOS() {
    const container = document.getElementById("sosContainer");
    container.innerHTML = `<div class="card">Memuat data SOS...</div>`;

    try {
        const res = await fetch(SOS_API_URL);
        if (!res.ok) throw new Error("HTTP " + res.status);

        const json = await res.json();

        if (json.status && json.data) {
            const d = json.data;

            container.innerHTML = `
                <div class="card sos-card">
                    <div class="sos-title">
                        <i class="fas fa-triangle-exclamation"></i> SOS Aktif
                    </div>
                    <div class="sos-row">
                        <strong>Waktu:</strong><br>${formatTime(d.time)}
                    </div>
                    <div class="sos-row">
                        <strong>Lokasi:</strong><br>
                        ${d.lat.toFixed(6)}, ${d.lng.toFixed(6)}
                    </div>
                    <a class="btn-map"
                       href="https://www.google.com/maps?q=${d.lat},${d.lng}"
                       target="_blank">
                       <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
                    </a>
                </div>
            `;
        } else {
            container.innerHTML = `
            <div class="card empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shield-heart"></i>
                </div>
                <h3>Tidak Ada SOS Aktif</h3>
                <p>Sistem dalam kondisi aman. Belum ada panggilan darurat yang terdeteksi.</p>
            </div>
            `;
        }

    } catch (err) {
        container.innerHTML = `
            <div class="card empty" style="color:#DC2626;">
                Gagal memuat data SOS
            </div>
        `;
        console.error(err);
    }
}

function formatTime(ts) {
    const d = new Date(ts.replace(" ", "T"));
    return d.toLocaleString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });
}

loadSOS();
setInterval(loadSOS, 30000);
</script>

</body>
</html>
