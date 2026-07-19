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

.header-content {
    flex: 1;
}

.header-content h1 {
    font-size: 26px;
    color: #1F2937;
    font-weight: 700;
    margin-right: 0;
    white-space: nowrap;
}

.header-content p {
    color: #6B7280;
    font-size: 14px;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.action-btn {
    width: 46px;
    height: 46px;
    min-width: 46px;
    min-height: 46px;
    border: none;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}

.action-btn i {
    color: #fff;
    font-size: 18px;
    line-height: 1;
}

.action-btn.settings {
    background: #2563EB;
}

.action-btn.clear {
    background: #DC2626;
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
    margin-bottom: 0;
}

.sos-title i {
    font-size: 20px;
}

.sos-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}

.sos-badge-modern {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    background: linear-gradient(135deg, #EEF2FF, #DBEAFE);
    color: #1D4ED8;
    border: 1px solid #BFDBFE;
    white-space: nowrap;
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

.sos-row-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 15px;
    color: #1F2937;
    margin-bottom: 10px;
    line-height: 1.6;
}

.sos-row-inline strong {
    color: #374151;
    font-weight: 600;
}

.sos-meta {
    color: #6B7280;
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

    .header-actions {
        gap: 8px;
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

        <div class="header-actions">
            <button class="action-btn clear" onclick="clearHistory()" title="Bersihkan History SOS">
                <i class="fas fa-trash"></i>
            </button>

            <a href="settings.php" class="action-btn settings" title="Pengaturan">
                <i class="fas fa-gear"></i>
            </a>
        </div>
    </div>

    <div id="sosContainer"></div>
</div>

<script>
const BASE_URL = "http://localhost/Website IoT Tongkat Pintar/";
const SOS_API_URL = BASE_URL + "api/get_sos_logs.php";

async function loadSOS() {
    const container = document.getElementById("sosContainer");
    container.innerHTML = `<div class="card">Memuat data SOS...</div>`;

    try {
        const res = await fetch(SOS_API_URL, { cache: "no-store" });
        if (!res.ok) throw new Error("HTTP " + res.status);

        const json = await res.json();
        const logs = Array.isArray(json.data) ? json.data : [];

        if (json.status && logs.length > 0) {
            container.innerHTML = logs.map((d, index) => {
                const lat = parseFloat(d.lat);
                const lng = parseFloat(d.lng);
                const locationText = (!isNaN(lat) && !isNaN(lng))
                    ? `${lat.toFixed(6)}, ${lng.toFixed(6)}`
                    : "-";

                return `
                    <div class="card sos-card">
                        <div class="sos-head">
                            <div class="sos-title">
                                <i class="fas fa-triangle-exclamation"></i>
                                Peringatan Darurat ${index + 1}
                            </div>
                        </div>

                        <div class="sos-row-inline">
                            <strong>Waktu:</strong>
                            <span class="sos-meta">${formatTime(d.time)}</span>
                        </div>

                        <div class="sos-row-inline">
                            <strong>Lokasi:</strong>
                            <span class="sos-meta">${locationText}</span>
                        </div>

                        <a class="btn-map"
                           href="https://www.google.com/maps?q=${lat},${lng}"
                           target="_blank" rel="noopener">
                           <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
                        </a>
                    </div>
                `;
            }).join("");
        } else {
            container.innerHTML = `
                <div class="card empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-shield-heart"></i>
                    </div>
                    <h3>Tidak Ada Riwayat SOS</h3>
                    <p>Belum ada panggilan darurat yang tersimpan.</p>
                </div>
            `;
        }
    } catch (err) {
        container.innerHTML = `
            <div class="card empty-state" style="border-color:#FCA5A5;">
                <div class="empty-icon" style="background:#FEE2E2;">
                    <i class="fas fa-triangle-exclamation" style="color:#DC2626;"></i>
                </div>
                <h3>Gagal memuat data SOS</h3>
                <p>Periksa koneksi atau endpoint API.</p>
            </div>
        `;
        console.error(err);
    }
}

async function clearHistory() {
    if (!confirm("Yakin ingin menghapus semua history SOS?")) return;

    try {
        const res = await fetch(SOS_API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "action=clear"
        });

        if (!res.ok) throw new Error("HTTP " + res.status);

        const json = await res.json();

        if (json.status) {
            alert(json.message || "History SOS berhasil dibersihkan.");
            loadSOS();
        } else {
            alert(json.message || "Gagal membersihkan history SOS.");
        }
    } catch (err) {
        alert("Gagal membersihkan history SOS.");
        console.error(err);
    }
}

function formatTime(ts) {
    if (!ts) return "-";
    const d = new Date(String(ts).replace(" ", "T"));
    if (isNaN(d.getTime())) return ts;
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
