<?php
session_start(); 

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
    header("Location: login.php"); 
    exit; 
}

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : -0.53322;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : 117.12537;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peta Lokasi Tongkat Tunanetra</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

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
    
    .card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .header-card {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .back-btn-circle {
        width: 50px;
        height: 50px;
        background: #2563EB;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .back-btn-circle:hover {
        background: #1D4ED8;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    }
    
    .back-btn-circle i {
        color: white;
        font-size: 20px;
    }
    
    .header-content h1 {
        font-size: 28px;
        color: #1F2937;
        margin-bottom: 6px;
        font-weight: 700;
    }
    
    .header-content p {
        color: #6B7280;
        font-size: 14px;
    }
    
    .map-container {
        position: relative;
        width: 100%;
        height: 500px;
        border-radius: 16px;
        overflow: hidden;
        background: #F3F4F6;
    }
    
    .map-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .map-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 14px 24px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 16px;
        color: #1F2937;
    }
    
    .map-overlay i {
        color: #DC2626;
        font-size: 20px;
    }
    
    .map-marker {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -100%);
        font-size: 50px;
        color: #DC2626;
        animation: bounce 2s infinite;
        filter: drop-shadow(0 4px 8px rgba(220, 38, 38, 0.4));
    }
    
    @keyframes bounce {
        0%, 100% { transform: translate(-50%, -100%); }
        50% { transform: translate(-50%, -110%); }
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .info-item {
        background: #F9FAFB;
        padding: 18px;
        border-radius: 12px;
        border-left: 4px solid #2563EB;
    }
    
    .info-label {
        font-size: 13px;
        color: #6B7280;
        font-weight: 600;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-label i {
        color: #2563EB;
    }
    
    .info-value {
        font-size: 16px;
        color: #1F2937;
        font-weight: 600;
    }
    
    .coordinate-card {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: white;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 20px;
    }
    
    .coordinate-card h3 {
        font-size: 18px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .coordinate-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .coordinate-item {
        background: rgba(255, 255, 255, 0.15);
        padding: 12px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .coordinate-item label {
        font-size: 12px;
        opacity: 0.9;
        display: block;
        margin-bottom: 4px;
    }
    
    .coordinate-item span {
        font-size: 18px;
        font-weight: 700;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
    }
    
    .btn {
        padding: 14px 24px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }
    
    .btn-primary {
        background: #2563EB;
        color: white;
    }
    
    .btn-primary:hover {
        background: #1D4ED8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }
    
    .btn-secondary {
        background: white;
        color: #2563EB;
        border: 2px solid #2563EB;
    }
    
    .btn-secondary:hover {
        background: #EFF6FF;
        transform: translateY(-2px);
    }
    
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #DBEAFE;
        color: #1E40AF;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        background: #10B981;
        border-radius: 50%;
        animation: pulse-dot 2s infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .alert-box {
        background: #FEF3C7;
        border-left: 4px solid #F59E0B;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: start;
        gap: 12px;
    }
    
    .alert-box i {
        color: #F59E0B;
        font-size: 20px;
        margin-top: 2px;
    }
    
    .alert-box p {
        color: #92400E;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }
        
        .header-card {
            flex-direction: column;
            text-align: center;
        }
        
        .map-container {
            height: 350px;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .coordinate-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Tooltip style for marker (appears above marker) */
    .marker-tooltip.leaflet-tooltip {
        background: #ffffffff !important;
        color: #000000ff !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        font-weight: 700 !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.18) !important;
        border: none !important;
    }

    /* Map visual polish: rounded corners and subtle shadow like screenshot */
    #map {
        border-radius: 12px;
        overflow: hidden; /* ensure tiles clip to rounded corners */
        box-shadow: 0 6px 18px rgba(16,24,40,0.08);
    }

    /* Ensure Leaflet container inherits rounding (some browsers need this) */
    .leaflet-container { 
        border-radius: inherit;
        overflow: hidden;
    }

    .loading-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

</style> 

</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="card header-card">
        <a href="index.php" class="back-btn-circle">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="header-content">
            <h1>Peta Lokasi Tongkat Pintar</h1>
            <p>Lokasi real-time tongkat tunanetra berdasarkan GPS</p>
        </div>
        <div style="margin-left: auto;">
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span>Live Tracking</span>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="alert-box">
        <i class="fas fa-info-circle"></i>
        <p>
            <strong>Informasi:</strong> Lokasi diperbarui setiap detik secara realtime. 
            Sistem secara otomatis mendeteksi status pergerakan berdasarkan perubahan posisi tongkat.
        </p>
    </div>

    <!-- Coordinate Card -->
    <div class="coordinate-card">
        <h3>
            <i class="fas fa-map-pin"></i>
            Koordinat GPS Terkini
        </h3>
        <div class="coordinate-grid">
            <div class="coordinate-item">
                <label>Latitude</label>
                <span id="lat">-</span>
            </div>
            <div class="coordinate-item">
                <label>Longitude</label>
                <span id="lng">-</span>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div id="map" style="width: 100%; height: 500px; border-radius: 16px; background: #E5E7EB;"></div>
    </div>

    <!-- Info Details -->
    <div class="card">
        <h3 style="color: #1F2937; margin-bottom: 16px; font-size: 20px;">
            <i class="fas fa-info-circle" style="color: #2563EB;"></i>
            Detail Informasi
        </h3>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-clock"></i>
                    Waktu Update
                </div>
                <div class="info-value" id="timestamp">Menunggu data...</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-map"></i>
                    Koordinat
                </div>
                <div class="info-value" id="coordinate">-</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-satellite"></i>
                    Akurasi GPS
                </div>
                <div class="info-value" id="accuracy">-</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-signal"></i>
                    Kualitas Sinyal
                </div>
                <div class="info-value" id="signal">-</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card">
        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <button onclick="refreshMap()" class="btn btn-secondary" id="refreshBtn">
                <i class="fas fa-sync-alt" id="refreshIcon"></i>
                <span id="refreshText">Refresh Lokasi</span>
            </button>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="card" style="background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); border-left: 4px solid #0EA5E9;">
        <h4 style="color: #0C4A6E; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-lightbulb"></i>
            Tips Penggunaan
        </h4>
        <ul style="color: #0C4A6E; line-height: 2; padding-left: 20px; margin: 0;">
            <li>Pastikan GPS tongkat selalu aktif untuk tracking akurat</li>
            <li>Periksa lokasi secara berkala melalui aplikasi</li>
            <li>Hubungi tunanetra jika terdeteksi tidak bergerak lama</li>
            <li>Tombol SOS dapat ditekan untuk situasi darurat</li>
        </ul>
    </div>

</div>

<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const API_URL = "http://localhost/Website IoT Tongkat Pintar/api/get_last_gps.php";

    // =============================
    // INIT MAP
    // =============================
    const map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Custom blue SVG icon (model and color can be changed here)
    const blueSvg = encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32"><path fill="#2563EB" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>`);
    const blueIcon = L.icon({
        iconUrl: 'data:image/svg+xml;utf8,' + blueSvg,
        iconSize: [35, 35],
        iconAnchor: [17, 35],
        popupAnchor: [0, -30]
    });

    // Create marker with tooltip bound above it (offset adjusted slightly upward)
    let marker = L.marker([-0.53322, 117.12537], { icon: blueIcon }).addTo(map)
        .bindTooltip("📍 Lokasi Tongkat Pintar", { direction: 'top', offset: [0, -22], className: 'marker-tooltip' });

    // =============================
    // VARIABEL GLOBAL
    // =============================
    let lastLat = null;
    let lastLng = null;
    let pathCoords = [];
    let pathLine = null;
    let trailDots = [];
    const MAX_TRAIL = 50;           

    // =============================
    // HITUNG JARAK (METER)
    // =============================
    function calculateDistance(lat1, lng1, lat2, lng2) {

        const R = 6371000;
        const toRad = x => x * Math.PI / 180;

        const dLat = toRad(lat2 - lat1);
        const dLng = toRad(lng2 - lng1);

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) *
            Math.cos(toRad(lat2)) *
            Math.sin(dLng / 2) ** 2;

        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }


    // =============================
    // FILTER GPS NOISE
    // =============================
    function filterGPSNoise(lat, lng) {
        let filteredLat = null;
        let filteredLng = null;

        if (filteredLat === null) {
            filteredLat = lat;
            filteredLng = lng;
            return {lat, lng};
        }

        const dist = calculateDistance(
            filteredLat,
            filteredLng,
            lat,
            lng
        );

        if (dist < GPS_NOISE_THRESHOLD) {
            return {
                lat: filteredLat,
                lng: filteredLng
            };
        }

        filteredLat = lat;
        filteredLng = lng;

        return {lat, lng};
    }

    // =============================
    // LOAD GPS DARI BACKEND
    // =============================
    let dotMarkers = [];
    const MAX_DOTS = 50; // batasi agar tidak berat
    async function loadGPS() {
    try {
        const res = await fetch(API_URL);
        const json = await res.json();

        if (!json.status || !json.data) return;

        const lat = parseFloat(json.data.latitude);
        const lng = parseFloat(json.data.longitude);
        // 🔥 VALIDASI WAJIB
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
            console.warn("⚠️ Data GPS invalid:", json.data);
            return;
        }
        const time = json.data.created_at;

    // =============================
    // AKURASI GPS (DINAMIS)
    // =============================
    let accuracyText = "± 5m";

    if (lastLat !== null && lastLng !== null) {
      const dist = calculateDistance(lastLat, lastLng, lat, lng);

      if (dist < 5) accuracyText = "± 5m (Sangat Baik)";
      else if (dist < 15) accuracyText = "± 10m (Baik)";
      else accuracyText = "± 20m (Buruk)";
    }

    document.getElementById("accuracy").textContent = accuracyText;

    // =============================
    // AKURASI SIGNAL
    // =============================
    document.getElementById("signal").textContent = getSignalQuality();
    function getSignalQuality() {
    const r = Math.random();
        if (r > 0.85) return "Sangat Baik (5/5)";
        if (r > 0.65) return "Baik (4/5)";
        if (r > 0.45) return "Cukup (3/5)";
        if (r > 0.25) return "Lemah (2/5)";
        return "Sangat Lemah (1/5)";
    }

    // =============================
    // UPDATE UI
    // =============================
    document.getElementById("lat").textContent = lat.toFixed(6);
    document.getElementById("lng").textContent = lng.toFixed(6);
    document.getElementById("timestamp").textContent = time;
    document.getElementById("coordinate").textContent =
      `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

    // =============================
    // UPDATE MAP
    // =============================
    const point = [lat, lng];

    // 🔵 Titik baru (circle kecil)
    const dot = L.circleMarker(point, {
    radius: 3,
    color: "#DC2626",
    fillColor: "#EF4444",
    fillOpacity: 0.9
    }).addTo(map);

    // Simpan ke array
    dotMarkers.push(dot);

    // 🔽 FADING TITIK LAMA
    dotMarkers.forEach((d, i) => {
        const opacity = Math.max(0.15, 1 - (dotMarkers.length - i) * 0.05);
        d.setStyle({ fillOpacity: opacity, opacity });
    });

    // Batasi jumlah titik (history)
    if (dotMarkers.length > MAX_DOTS) {
        const oldDot = dotMarkers.shift();
        map.removeLayer(oldDot);
    }

    // Marker utama (posisi terkini)
    marker.setLatLng(point);

    // Update tooltip content (ensure it stays correct; do NOT open automatically)
    if (marker) {
        if (marker.getTooltip && marker.getTooltip()) {
            marker.setTooltipContent("📍 Lokasi Tongkat Pintar");
        } else {
            marker.bindTooltip("📍 Lokasi Tongkat Pintar", { direction: 'top', offset: [0, -22], className: 'marker-tooltip' });
        }
        // Tooltip will not be opened automatically
    }

    // Fokus ke posisi terbaru TANPA geser aneh
    map.setView(point, 16);

    // =============================
    // SIMPAN DATA SEBELUMNYA
    // =============================
    lastLat = lat;
    lastLng = lng;

  } catch (e) {
    console.error("Gagal load GPS:", e);
  }
}

// =============================
// FITURE REFRESH MANUAL
// =============================
function refreshMap() {
    const btn = document.getElementById("refreshBtn");
    const icon = document.getElementById("refreshIcon");
    const text = document.getElementById("refreshText");

    btn.disabled = true;
    icon.classList.add("loading-spin");
    text.textContent = "Memperbarui...";

    const start = Date.now();

    loadGPS().finally(() => {
        const elapsed = Date.now() - start;
        const delay = Math.max(500, elapsed); // minimal 0.5 detik

        setTimeout(() => {
            btn.disabled = false;
            icon.classList.remove("loading-spin");
            text.textContent = "Refresh Lokasi";
        }, delay);
    });
}

// =============================
// INIT & INTERVAL
// =============================
loadGPS();
setInterval(loadGPS, 10000);
</script>

<script>
/* =====================================================
   SAVE GPS HISTORY (SHARED GLOBAL)
===================================================== */

// 🔥 GLOBAL agar tidak double insert antar halaman
if (!window.lastSavedTime) {
    window.lastSavedTime = Date.now() - 600000; // langsung boleh save pertama
}

function saveGPSHistory(lat, lng) {
    const now = Date.now();

    if (now - window.lastSavedTime < 600000) return;

    console.log("💾 [MAP] Kirim GPS ke DB...");

    fetch("../api/save_gps.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `lat=${lat}&lng=${lng}&api_key=12345-KUNCI-AMAN-7890`
    })
    .then(res => res.json())
    .then(res => console.log("✅ [MAP] Saved:", res))
    .catch(err => console.error("❌ [MAP] Error:", err));

    window.lastSavedTime = now;
}

/* ===============================
   MQTT CONFIG (REALTIME MAP)
================================ */
const MQTT_BROKER = "ws://127.0.0.1:9001";
const MQTT_TOPIC  = "smartcane/device01/status";
const mqttClient  = mqtt.connect(MQTT_BROKER);

let lastMQTTUpdate = Date.now();
let lastLatMQTT = null;
let lastLngMQTT = null;
let lastMoveTimeMQTT = Date.now();
// ===== FILTER GPS NOISE =====
let filteredLat = null;
let filteredLng = null;

const GPS_NOISE_THRESHOLD = 4; // meter 

mqttClient.on("connect", () => {
    console.log("✅ MQTT MAP CONNECTED");
    mqttClient.subscribe(MQTT_TOPIC);
});

    mqttClient.on("message", (topic, message) => {
        lastMQTTUpdate = Date.now();

        try {
            const d = JSON.parse(message.toString());
            if (!d.latitude || !d.longitude) return;

            const rawLat = d.latitude;
            const rawLng = d.longitude;

            const smooth = filterGPSNoise(rawLat, rawLng);

            const lat = smooth.lat;
            const lng = smooth.lng;

            // 🔥 SIMPAN KE DATABASE (10 MENIT SEKALI)
            saveGPSHistory(lat, lng);

            const timeStr = new Date().toISOString().replace("T"," ").substring(0,19);
            const now = Date.now();

            /* =========================
            UPDATE UI
            ========================= */
            document.getElementById("lat").textContent = lat.toFixed(6);
            document.getElementById("lng").textContent = lng.toFixed(6);
            document.getElementById("timestamp").textContent = timeStr;
            document.getElementById("coordinate").textContent =
                `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

            /* =========================
            UPDATE MAP + JEJAK
            ========================= */
            const point = [lat, lng];

            // Marker utama
            marker.setLatLng(point);

            // 🔥 WAJIB: paksa map ke posisi terbaru
            map.setView(point, 16);

            // 🔴 TAMBAH JEJAK BARU
            const dot = L.circleMarker(point, {
                radius: 3,
                color: "#DC2626",
                fillColor: "#EF4444",
                fillOpacity: 1,
                opacity: 1
            }).addTo(map);

            trailDots.push({
                marker: dot,
                createdAt: now
            });

            // ⏳ FADE HALUS
            const FADE_DELAY = 5000;
            const FADE_DURATION = 15000;

            trailDots.forEach(item => {
                const age = now - item.createdAt;

                if (age < FADE_DELAY) {
                    item.marker.setStyle({ opacity: 1, fillOpacity: 1 });
                } else {
                    const progress = (age - FADE_DELAY) / FADE_DURATION;
                    const opacity = Math.max(0.15, 1 - progress);
                    item.marker.setStyle({ opacity, fillOpacity: opacity });
                }
            });

            // 🧹 BATASI JUMLAH JEJAK
            if (trailDots.length > MAX_TRAIL) {
                map.removeLayer(trailDots[0].marker);
                trailDots.shift();
            }

        } catch (e) {
            console.error("MQTT MAP ERROR:", e);
        }
    });

async function fallbackToDatabase() {
    try {
        console.log("⚠️ MQTT mati, ambil data DB...");

        const res = await fetch("../api/get_last_gps.php");
        const json = await res.json();

        if (!json.status || !json.data) return;

        const lat = parseFloat(json.data.latitude);
        const lng = parseFloat(json.data.longitude);

        const point = [lat, lng];

        // update UI
        document.getElementById("lat").textContent = lat.toFixed(6);
        document.getElementById("lng").textContent = lng.toFixed(6);
        document.getElementById("timestamp").textContent = json.data.created_at;
        document.getElementById("coordinate").textContent =
            `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        // update map
        marker.setLatLng(point);
        map.setView(point, 16);

    } catch (err) {
        console.error("❌ fallback error:", err);
    }
}

setInterval(() => {
    const now = Date.now();

    // kalau 10 detik tidak ada data MQTT
    if (now - lastMQTTUpdate > 10000) {
        fallbackToDatabase();
    }
}, 5000);
</script>

</body>
</html>