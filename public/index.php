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
<title>Monitoring Tongkat Tunanetra</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

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
    
    .header-card h1 {
        font-size: 32px;
        color: #1F2937;
        margin-bottom: 12px;
        font-weight: 700;
    }
    
    .header-card p {
        color: #6B7280;
        line-height: 1.6;
        font-size: 15px;
    }
    
    /* Tabs */
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .tab-btn {
        flex: 1;
        padding: 14px 20px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .tab-active {
        background: #2563EB;
        color: white;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    }
    
    .tab-inactive {
        background: white;
        color: #6B7280;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .tab-inactive:hover {
        background: #F3F4F6;
    }
    
    /* SOS Alert Banner */
    .sos-banner {
        display: none;
        justify-content: space-between;
        align-items: center;
        background: #e53935;
        color: #fff;
        padding: 22px 20px; /* more vertical space */
        margin-bottom: 24px;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(0,0,0,0.25);
        font-family: 'Segoe UI', sans-serif;
        animation: pulse 1.6s infinite;
        min-height: 84px;
        gap: 20px;
    }

    .sos-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .sos-icon {
        width: 64px;
        height: 64px;
        background: #fff;
        color: #e53935;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .sos-text {
        line-height: 1.1;
        display: flex;
        flex-direction: column;
        justify-content: center; /* vertically center text with icon */
    }

    .sos-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .sos-subtitle {
        font-size: 14px;
        opacity: 0.95;
        margin-top: 4px;
    }

    .sos-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: nowrap;
    }

    .sos-icon i {
        font-size: 30px;
        color: #e53935;
    }

    .sos-icon i {
        font-size: 28px;
        color: #DC2626;
    }
    
    .sos-content {
        flex: 1;
    }

    .sos-banner.active {
    display: flex;
    }
    
    .sos-content h2 {
        font-size: 24px;
        margin-bottom: 8px;
    }
    
    .sos-content p {
        color: #FEE2E2;
        font-size: 14px;
    }

    .sos-blink {
    animation: pulse 1.2s infinite;
    }

    @keyframes pulse {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
    
    /* Buttons inside sos actions: unified layout */
    .sos-actions .btn-map,
    .sos-actions .btn-confirm {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        min-height: 44px;
        transition: all 0.22s ease;
        border: none;
    }

    .btn-map {
        background: #ffffff;
        color: #e53935;
        border: 1px solid rgba(229,57,53,0.08);
        box-shadow: 0 6px 18px rgba(229,57,53,0.06);
    }

    .btn-confirm {
        background: #b71c1c;
        color: #fff;
        border: none;
        box-shadow: 0 6px 18px rgba(183,28,28,0.12);
        padding: 8px 16px;
    }

    .btn-map:hover {
        background: #ffffffb9;
        transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(229,57,53,0.12);
    }

    .btn-confirm:hover {
        background: #9a0007;
        transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(183,28,28,0.18);
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(229,57,53,0.6); }
        70% { box-shadow: 0 0 0 10px rgba(229,57,53,0); }
        100% { box-shadow: 0 0 0 0 rgba(229,57,53,0); }
    }
    
    .btn-confirm:hover {
        background: #cfcfcf49;
    }
    
    /* Status Card */
    .status-card {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .status-icon {
        width: 48px;
        height: 48px;
        background: #DBEAFE;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .status-icon i {
        font-size: 20px;
        color: #2563EB;
    }
    
    .status-info p:first-child {
        font-size: 13px;
        color: #6B7280;
        margin-bottom: 4px;
    }
    
    .status-info p:last-child {
        font-size: 16px;
        font-weight: 600;
        color: #1F2937;
    }

    /* ===== FIX STATUS ROW ALIGNMENT ===== */
    .status-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }

    /* ikon sejajar */
    .status-row i {
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    /* label dibuat lebar tetap */
    .status-label {
        width: 140px; /* KUNCI SEJAJAR */
        font-size: 14px;
        color: #6B7280;
        font-weight: 600;
    }

    /* badge tetap fleksibel */
    .status-row .status-badge {
        white-space: nowrap;
    }
    
    .divider {
        border: none;
        border-top: 1px solid #E5E7EB;
        margin: 15px 0;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #FEE2E2;
        color: #DC2626;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
    }
    
    /* Grid */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
    }
    
    .sensor-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .sensor-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .sensor-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .sensor-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .sensor-icon i {
        font-size: 22px;
    }
    
    .sensor-card h3 {
        font-size: 18px;
        color: #1F2937;
        font-weight: 600;
    }
    
    .sensor-value {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .sensor-label {
        font-size: 13px;
        color: #6B7280;
    }

    .distance-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
        grid-column: 1 / -1;
        width: 100%;
    }

    .distance-value {
        margin-top: 8px;
    }

    .distance-subtitle {
        margin-top: 6px;
        font-size: 13px;
        color: #6B7280;
        line-height: 1.5;
    }

    .distance-realtime {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ECFDF5;
        color: #059669;
        font-size: 12px;
        font-weight: 700;
    }
    
    /* SOS Card */
    .sos-card {
        background: white;
        transition: all 0.3s ease;
    }
    
    .sos-card.sos-active {
        background: #DC2626;
        animation: pulse-card 1.1s infinite;
    }
    
    .sos-card.sos-active h3,
    .sos-card.sos-active .sensor-value,
    .sos-card.sos-active .sensor-label {
        color: white !important;
    }
    
    .sos-card.sos-active .sensor-icon {
        background: white !important;
    }
    
    .sos-card.sos-active .sensor-icon i {
        color: #DC2626 !important;
    }
    
    /* 🔴 SOS OUTLINE PULSE */
    .sos-danger {
        animation: sosOutlinePulse 1.4s ease-out infinite;
        filter: drop-shadow(0 0 6px rgba(220,38,38,0.8));
    }

    @keyframes sosOutlinePulse {
        0% {
            stroke-opacity: 1;
            stroke-width: 2;
            transform: scale(1);
        }
        50% {
            stroke-opacity: 0.5;
            stroke-width: 4;
            transform: scale(1.05);
        }
        100% {
            stroke-opacity: 1;
            stroke-width: 2;
            transform: scale(1);
        }
    }
    
    /* Buttons */
    .btn {
        width: 100%;
        padding: 12px 20px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 12px;
    }
    
    .btn-primary {
        background: #2563EB;
        color: white;
    }
    
    .btn-primary:hover {
        background: #1D4ED8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .btn-warning {
        background: #EA580C;
        color: white;
    }
    
    .btn-warning:hover {
        background: #C2410C;
    }
    
    .btn-danger {
        background: white;
        color: #DC2626;
    }
    
    .btn-danger:hover {
        background: #FEE2E2;
    }
    
    /* Colors */
    .orange { color: #EA580C; }
    .red { color: #DC2626; }
    .green { color: #10B981; }
    .yellow { color: #F59E0B; }
    .blue { color: #2563EB; }
    
    .bg-orange { background: #FED7AA; }
    .bg-red { background: #FEE2E2; }
    .bg-green { background: #D1FAE5; }
    .bg-yellow { background: #FEF3C7; }
    .bg-blue { background: #DBEAFE; }
    
    /* Info Box */
    .info-box {
        background: #EFF6FF;
        border-left: 4px solid #2563EB;
        border-radius: 10px;
        padding: 16px;
        margin-top: 20px;
    }
    
    .info-box p {
        color: #1E40AF;
        font-size: 14px;
        line-height: 1.6;
    }
    
    /* Map Page */
    .map-container {
        position: relative;
        width: 100%;
        height: 400px;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .map-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .map-overlay {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        pointer-events: none;
        z-index: 2;
    }
    
    .map-marker {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -100%);
        font-size: 40px;
        color: #DC2626;
        animation: bounce 2s infinite;
        pointer-events: none;
        z-index: 2;
    }

    #map {
    width: 100%;
    height: 500px;
    z-index: 1;
    }

    /* Location Card */
    .location-card {
        padding: 16px;
    }

    /* Data Box */
    .location-data {
        background: #F9FAFB;
        border-radius: 12px;
        padding: 10px 13px;
        margin-bottom: 12px;
        border: 1px solid #E5E7EB;
    }

    /* Row */
    .location-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 5px;
    }

    .location-row:not(:last-child) {
        border-bottom: 1px solid #E5E7EB;
    }

    /* Label */
    .location-label {
        font-size: 0.9rem;
        color: #6B7280;
        font-weight: 600;
    }

    /* Value */
    .location-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #757575ff;
        font-family: monospace;
    }

    /* Button (location card only) */
    .location-card .btn-map {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 6px;
    }

    .map-link {
        display: block;
        text-decoration: none;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translate(-50%, -100%); }
        50% { transform: translate(-50%, -110%); }
    }
    
    .map-info {
        background: #F9FAFB;
        padding: 16px;
        border-radius: 10px;
    }
    
    .map-info p {
        color: #6B7280;
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .map-info p:last-child {
        margin-bottom: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }
        
        .header-card h1 {
            font-size: 24px;
        }
        
        .grid {
            grid-template-columns: 1fr;
        }

        .distance-grid {
            grid-template-columns: 1fr;
        }

        .distance-grid .sensor-card {
            min-width: 0;
        }

        #gyroCard {
            grid-column: span 1 !important;
        }
        
        .sos-banner.active {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
        }
        
        .sensor-value {
            font-size: 28px;
        }
    }

    /* ===================== */
    /* RESPONSIVE MOBILE */
    /* ===================== */
    @media (max-width: 768px) {
        .sos-banner {
            flex-direction: column;
            align-items: flex-start; /* keep content left-aligned */
            gap: 12px;
            padding: 18px;
            text-align: left; /* alert text left on mobile */
            min-height: unset;
        }

        .sos-left {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-start; /* align icon + text to left */
            width: 100%;
        }

        .sos-text {
            text-align: left; /* force text to the left */
        }

        .sos-icon {
            width: 56px;
            height: 56px;
            font-size: 26px;
        }

        .sos-title {
            font-size: 20px;
        }

        .sos-subtitle {
            font-size: 13px;
        }

        .sos-actions {
            width: 100%;
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .sos-actions .btn-map,
        .sos-actions .btn-confirm {
            flex: 1 1 45%;
            padding: 12px 12px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
        }

        .sensor-card {
            padding: 18px;
        }

        .sensor-value {
            font-size: 30px;
        }

        .sensor-card h3 {
            font-size: 16px;
        }
    }
</style>

</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="card header-card" style="
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFF 100%);
        border-left: 5px solid #2563EB;">

        <h1 style="
            font-size: 32px;
            font-weight: 800;
            color: #1E293B;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;">
            <span style="
                background: linear-gradient(135deg, #2563EB, #1D4ED8);
                color: white;
                padding: 10px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;">
                <i class="fas fa-blind"></i>
            </span>
            Monitoring Tongkat Pintar Tunanetra
        </h1>

        <p style="
            color: #475569;
            font-size: 15px;
            line-height: 1.75;
            max-width: 900px;">
            Sistem ini membantu penyandang tunanetra bergerak dengan aman dan mandiri melalui tongkat pintar berbasis IoT 
            yang dilengkapi sensor GPS, tombol darurat (SOS), dan deteksi jatuh dengan pemantauan melalui dashboard website.
        </p>

        <div style="
            margin-top: 6px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;">

            <!-- Badge 1 -->
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, #2563EB, #1D4ED8);
                color: white;
                padding: 7px 16px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);">
                <i class="fas fa-satellite-dish"></i>
                Real-Time Monitoring
            </span>

            <!-- Badge 2 -->
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #ECFEFF;
                color: #0369A1;
                padding: 7px 16px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                border: 1px solid #BAE6FD;">
                <i class="fas fa-location-dot"></i>
                GPS Tracking
            </span>

            <!-- Badge 3 -->
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #FEF3C7;
                color: #92400E;
                padding: 7px 16px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                border: 1px solid #FDE68A;">
                <i class="fas fa-bell"></i>
                Sistem Peringatan SOS
            </span>

        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button id="tabRealtime" class="tab-btn tab-active">
            <i class="fas fa-chart-line"></i> Data Real-Time
        </button>
        <button id="tabMap" class="tab-btn tab-inactive">
            <i class="fas fa-map-marker-alt"></i> Peta Lokasi
        </button>
    </div>

    <!-- Content Realtime -->
    <div id="contentRealtime">

        <!-- SOS Alert Banner -->
        <div id="sosAlert" class="sos-banner">
            <div class="sos-left">
                <div class="sos-icon">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="sos-text">
                    <div class="sos-title">DARURAT - SOS AKTIF!</div>
                    <div class="sos-subtitle">
                        Tunanetra membutuhkan bantuan segera! Periksa lokasi dan segera hubungi.
                    </div>
                </div>
            </div>

            <div class="sos-actions">
                <button onclick="openSOSLocation()" class="btn-map">
                    Lihat Lokasi
                </button>
                <button onclick="confirmSOS()" class="btn-confirm">
                    Konfirmasi
                </button>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card">
            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="status-info">
                    <p>Waktu Pembaruan</p>
                    <p id="timestamp">No Connection!! | Selasa, 18 November 2025 00:00:00</p>
                </div>
            </div>

            <div class="divider"></div>

            <div class="status-row">
                <i class="fas fa-microchip blue"></i>

                <span class="status-label">
                    Status Alat
                </span>

                <span id="deviceStatusBadge" class="status-badge">
                    <i id="deviceStatusIcon" class="fas fa-circle-xmark"></i>
                    <span id="status">TIDAK AKTIF</span>
                </span>
            </div>

            <div class="status-row">
                <i class="fas fa-person-walking blue"></i>

                <span class="status-label">
                    Status Pergerakan
                </span>

                <span id="movementStatus" class="status-badge">
                    <i class="fas fa-person-circle-minus"></i>
                    <span> DIAM </span>
                </span>
            </div>
        </div>

        <!-- Sensor Grid -->
        <div class="grid">

            <!-- SOS Card -->
            <div id="sosCard" class="sensor-card">
                <div class="sensor-header">
                    <div class="sensor-icon bg-orange">
                        <i class="fas fa-triangle-exclamation orange"></i>
                    </div>
                    <h3>SOS Darurat</h3>
                </div>

                <div class="sensor-value orange" id="sosState">Normal</div>
                <div class="sensor-label" id="sosLabel">Kondisi aman</div>

                <!-- TOMBOL KONSISTEN -->
                <a href="sos.php" style="text-decoration: none;">
                    <button class="btn btn-warning">
                        <i class="fas fa-clipboard-list"></i>
                        Riwayat SOS
                    </button>
                </a>
            </div>

        <!-- Location Card -->
        <div class="sensor-card location-card">

            <div class="sensor-header">
                <div class="sensor-icon bg-blue">
                    <i class="fas fa-map-marker-alt blue"></i>
                </div>
                <h3>Lokasi</h3>
            </div>

            <div class="location-data">
                <div class="location-row">
                    <span class="location-label">Lat</span>
                    <span class="location-value" id="lat">-</span>
                </div>

                <div class="location-row">
                    <span class="location-label">Long</span>
                    <span class="location-value" id="lng">-</span>
                </div>
            </div>

            <a id="lihatPetaLink" href="map.php" class="map-link">
                <button type="button" class="btn btn-primary btn-map">
                    <i class="fas fa-map"></i> Lihat Peta
                </button>
            </a>
        </div>

            <!-- Buzzer Card -->
            <div class="sensor-card">
                <div class="sensor-header">
                    <div class="sensor-icon bg-red">
                        <i class="fas fa-bell red"></i>
                    </div>
                    <h3>Buzzer</h3>
                </div>
                <div class="sensor-value red" id="buzzer">Mati</div>
                <div class="sensor-label">Status buzzer</div>
            </div>

            <!-- Distance Sensors -->
            <div class="distance-grid">
                <div id="distanceFrontCard" class="sensor-card">
                    <div class="sensor-header">
                        <div class="sensor-icon bg-green">
                            <i class="fas fa-ruler-horizontal green"></i>
                        </div>
                        <h3>Depan</h3>
                    </div>
                    <div class="sensor-value green distance-value" id="distanceFront">1 cm</div>
                    <div class="sensor-label">Halangan depan tongkat</div>
                    <div class="distance-realtime"><i class="fas fa-signal"></i> Realtime</div>
                </div>

                <div id="distanceBottomCard" class="sensor-card">
                    <div class="sensor-header">
                        <div class="sensor-icon bg-green">
                            <i class="fas fa-ruler-vertical green"></i>
                        </div>
                        <h3>Bawah</h3>
                    </div>
                    <div class="sensor-value green distance-value" id="distanceBottom">1 cm</div>
                    <div class="sensor-label">Permukaan atau Halangan bawah</div>
                    <div class="distance-realtime"><i class="fas fa-signal"></i> Realtime</div>
                </div>
            </div>

            <!-- Gyroscope Card -->
            <div id="gyroCard" class="sensor-card" style="grid-column: span 2;">
                <div class="sensor-header">
                    <div class="sensor-icon bg-yellow">
                        <i class="fas fa-person-falling yellow"></i>
                    </div>
                    <h3>Sensor Gyroscope</h3>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div class="sensor-value yellow" id="gyroStatus">NORMAL</div>
                        <div class="sensor-label" id="gyroLabel">
                            Tongkat dalam posisi aman
                        </div>
                    </div>

                    <div style="font-size:70px; margin-right:40px;" id="gyroIcon">
                        <i class="fas fa-person-walking yellow"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p>
                <strong>Info:</strong> Data diperbarui secara real-time. 
                Lokasi GPS terakhir: Lat <span id="infoLat">-</span>, Lng <span id="infoLng">-</span>
            </p>
        </div>

    </div>

    <!-- Content Map -->
    <div id="contentMap" style="display: none;">
        
        <div class="card">
            <h2 style="color: #1F2937; margin-bottom: 8px;">Peta Lokasi Tongkat Pintar</h2>
            <p style="color: #6B7280; font-size: 14px;">Lokasi real-time tongkat tunanetra berdasarkan GPS</p>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="map-container">
                <div id="map" style="width:100%; height:500px;"></div>
                <div class="map-overlay">
                    <span>Lokasi Tongkat Pintar</span>
                </div>
                <div class="map-marker">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="map-info">
                <p><strong>Koordinat:</strong> <span id="mapLat">-</span>, <span id="mapLng">-</span></p>
                <p><strong>Waktu Update:</strong> <span id="mapTime">18-11-2025 | Selasa, 18 November 2025 00:00:00</span></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mqtt/5.14.1/mqtt.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
/* =====================================================
   GLOBAL STATE
===================================================== */
let activeSOSId = null;
let sosAlreadyShown = false;
let sosLat = null;
let sosLng = null;
let sosConfirmed = false;
let sosModeActive = false;
let sosDangerBox = null;
let fallSOSAlreadySent = false;
let lastManualSOSSendAt = 0;
let lastFallSOSSendAt = 0;
let lastSosValue = 0;

let lastLat = null;
let lastLng = null;
let lastMQTTUpdate = Date.now();
let lastMoveTime = Date.now();
let moveStartTime = null;
let isMovingConfirmed = false;

let dotMarkers = [];
const MAX_DOTS = 30;

let lastValidLat = null;
let lastValidLng = null;
const MIN_MOVE_METER = 2; // kurang dari ini = noise

/* =====================================================
   CONFIG
===================================================== */
const BASE_URL = "http://localhost/Website IoT Tongkat Pintar/";
const STATUS_API_URL = BASE_URL + "api/get_last_gps.php";
const SOS_STATUS_API_URL = BASE_URL + "api/get_sos_logs.php";
const CONFIRM_SOS_API_URL = BASE_URL + "api/confirm_sos.php";

/* =====================================================
   FORMAT TIMESTAMP
===================================================== */
function formatTimestamp(ts) {
    const d = new Date(ts.replace(" ", "T"));
    if (isNaN(d)) return "-";

    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];

    return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}
            | ${days[d.getDay()]}, ${String(d.getDate()).padStart(2,'0')} ${months[d.getMonth()]} ${d.getFullYear()}
            ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
}

function getWIBTimeString() {
    return new Date().toLocaleString("id-ID", {
        timeZone: "Asia/Jakarta",
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });
}

function updateTimeFromGPS() {
    const formatted = getWIBTimeString();
    document.getElementById("timestamp").textContent = formatted;
    if (document.getElementById("mapTime")) {
        document.getElementById("mapTime").textContent = formatted;
    }
}

/* =====================================================
   STATUS ALAT (ONLINE / OFFLINE)
===================================================== */
function getDeviceStatusFromTimestamp(timestamp) {
    const last = new Date(timestamp.replace(" ", "T"));
    const now = new Date();
    const diff = (now - last) / 1000;

    if (diff <= 15) {
        return { text:"Aktif", bg:"#D1FAE5", color:"#065F46", icon:"fa-circle-check" };
    }
    return { text:"TIDAK AKTIF", bg:"#FEE2E2", color:"#DC2626", icon:"fa-circle-xmark" };
}

/* =====================================================
   STATUS PERGERAKAN (SUPER SIMPLE VERSION)
===================================================== */
let prevLat = null;
let prevLng = null;

const MOVE_THRESHOLD = 5; // meter (boleh 4 - 6)

function updateMovementFromGPS(lat, lng) {

    const el = document.getElementById("movementStatus");
    if (!el) return;

    // INIT pertama
    if (prevLat === null) {
        prevLat = lat;
        prevLng = lng;
        return;
    }

    // hitung jarak dari posisi sebelumnya
    const dist = distanceMeter(prevLat, prevLng, lat, lng);

    console.log("Distance movement:", dist);

    if (dist >= MOVE_THRESHOLD) {

        el.innerHTML = `<i class="fas fa-person-walking"></i> BERGERAK`;
        el.style.background = "#DBEAFE";
        el.style.color = "#1E40AF";

    } else {

        el.innerHTML = `<i class="fas fa-person-circle-minus"></i> DIAM`;
        el.style.background = "#FEF3C7";
        el.style.color = "#92400E";
    }

    // update posisi sebelumnya
    prevLat = lat;
    prevLng = lng;
}

/* =====================================================
   SENSOR GYROSCOPE (JATUH / NORMAL)
===================================================== */
function updateGyroscope(status) {

    const card  = document.getElementById("gyroCard");
    const value = document.getElementById("gyroStatus");
    const label = document.getElementById("gyroLabel");
    const icon  = document.getElementById("gyroIcon");

    // =========================
    // TERJATUH
    // =========================
    if (status === "fall") {

        card.style.background = "#FEE2E2";

        value.textContent = "JATUH";
        value.className = "sensor-value red";

        label.textContent = "Tongkat terdeteksi jatuh";

        icon.innerHTML =
            '<i class="fas fa-person-falling red"></i>';
    }

    // =========================
    // SOS MANUAL
    // =========================
    else if (status === "manual") {

        card.style.background = "#FEF3C7";

        value.textContent = "SOS";
        value.className = "sensor-value yellow";

        label.textContent = "Tombol SOS ditekan";

        icon.innerHTML =
            '<i class="fas fa-triangle-exclamation yellow"></i>';
    }

    // =========================
    // NORMAL
    // =========================
    else {

        card.style.background = "white";

        value.textContent = "NORMAL";
        value.className = "sensor-value yellow";

        label.textContent = "Tongkat dalam posisi aman";

        icon.innerHTML =
            '<i class="fas fa-person-walking yellow"></i>';
    }
}

/* =====================================================
   UPDATE STATUS REALTIME
===================================================== */
async function updateGPSData() {
    try {
        const res = await fetch(STATUS_API_URL);
        const json = await res.json();
        if (!json.status) return;

        const d = json.data;

        // GPS
        ["lat","mapLat","infoLat"].forEach(id => document.getElementById(id).textContent = d.latitude.toFixed(6));
        ["lng","mapLng","infoLng"].forEach(id => document.getElementById(id).textContent = d.longitude.toFixed(6));

        // GPS ONLY (dari database)
        ["lat","mapLat","infoLat"].forEach(id => {
            document.getElementById(id).textContent = d.latitude.toFixed(6);
        });

        ["lng","mapLng","infoLng"].forEach(id => {
            document.getElementById(id).textContent = d.longitude.toFixed(6);
        });

        // waktu dari database
        updateTimeFromGPS(d.created_at);

        // map update
        updateLeafletMap(d.latitude, d.longitude);

        // Status alat
        const s = getDeviceStatusFromTimestamp(d.created_at);
        const badge = document.getElementById("status").closest(".status-badge");
        badge.style.background = s.bg;
        badge.style.color = s.color;
        badge.querySelector("i").className = `fas ${s.icon}`;
        document.getElementById("status").textContent = s.text;

        // Waktu & pergerakan
        updateTimeFromGPS(d.created_at);

        // Map
        updateLeafletMap(d.latitude, d.longitude);

    if (d.sos == 1 && !sosConfirmed) {
        sosModeActive = true;
        showSOSOnMap(d.latitude, d.longitude);
    } else {
        sosModeActive = false;
    }

    } catch (e) {
        console.error("STATUS API ERROR:", e);
    }
}

/* =====================================================
   TAB SWITCHING (REALTIME <-> MAP)
===================================================== */
document.getElementById("tabRealtime").addEventListener("click", () => {
    document.getElementById("tabRealtime").classList.add("tab-active");
    document.getElementById("tabRealtime").classList.remove("tab-inactive");

    document.getElementById("tabMap").classList.remove("tab-active");
    document.getElementById("tabMap").classList.add("tab-inactive");

    document.getElementById("contentRealtime").style.display = "block";
    document.getElementById("contentMap").style.display = "none";
});

document.getElementById("tabMap").onclick = function () {
    this.classList.add("tab-active");
    this.classList.remove("tab-inactive");

    document.getElementById("tabRealtime").classList.remove("tab-active");
    document.getElementById("tabRealtime").classList.add("tab-inactive");

    document.getElementById("contentRealtime").style.display = "none";
    document.getElementById("contentMap").style.display = "block";

    setTimeout(() => {
        // 🔥 AMBIL KOORDINAT TERBARU
        let mapLat = parseFloat(document.getElementById("mapLat").textContent);
        let mapLng = parseFloat(document.getElementById("mapLng").textContent);

        // 🔥 VALIDASI DAN GUNAKAN DEFAULT JIKA INVALID
        if (!mapLat || !mapLng || isNaN(mapLat) || isNaN(mapLng) || (mapLat === 0 && mapLng === 0)) {
            console.warn("⚠️ Invalid map coordinates!");
            return;
        }
        // Gunakan SOS location jika aktif
        if (sosModeActive && sosLat && sosLng) {
            mapLat = sosLat;
            mapLng = sosLng;
        }

        // 🔥 VALIDASI KOORDINAT
        if (!mapLat || !mapLng || isNaN(mapLat) || isNaN(mapLng)) {
            console.warn("⚠️ Invalid map coordinates!");
            return;
        }

        // 🔥 INIT MAP PERTAMA KALI
        if (!leafletMap) {
            initLeafletMap(mapLat, mapLng);
        }

        // 🔥 PASTIKAN MAP RENDER DENGAN BAIK
        leafletMap.invalidateSize();
        leafletMap.setView([mapLat, mapLng], 16);

        // 🔥 UPDATE MARKER
        if (leafletMarker) {
            leafletMarker.setLatLng([mapLat, mapLng]);
        }

        // 🔥 JIKA SOS AKTIF, TAMPILKAN DENGAN STYLE DARURAT
        if (sosModeActive && sosLat && sosLng) {
            if (leafletMarker) {
                leafletMarker
                    .setIcon(sosIcon)
                    .setPopupContent(`
                        🚨 <strong>SOS AKTIF!</strong><br>
                        Lokasi Darurat Tongkat Pintar
                    `)
                    .openPopup();

                // 🔥 TAMBAH ANIMASI BLINK
                setTimeout(() => {
                    leafletMarker.getElement()?.classList.add("sos-blink");
                }, 0);
            }

            // 🔥 TAMPILKAN DANGER BOX (DANGER ZONE)
            if (!sosDangerBox) {
                showSOSOnMap(sosLat, sosLng);
            }

            leafletMap.setView([sosLat, sosLng], 17);
        } else {
            // 🔥 MODE NORMAL - JANGAN HAPUS ANIMASI, BIARKAN TERUS KEDIP SAMPAI KONFIRMASI
            if (leafletMarker) {
                leafletMarker
                    .setIcon(normalIcon)
                    .setPopupContent("📍 Lokasi Tongkat Pintar");

                // ❌ JANGAN HAPUS CLASS - BIARKAN TERUS BLINK SAMPAI KONFIRMASI
                // leafletMarker.getElement()?.classList.remove("sos-blink");
            }

            // 🔥 HAPUS DANGER BOX JIKA ADA
            if (sosDangerBox && leafletMap) {
                leafletMap.removeLayer(sosDangerBox);
                sosDangerBox = null;
            }
        }

    }, 300);
};

/* =====================================================
    MENGHILANGKAN NOISE GPS 
===================================================== */
function distanceMeter(lat1, lng1, lat2, lng2) {
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

/* =====================================================
   UPDATE SOS (DIPERBAIKI TANPA MENGURANGI)
===================================================== */
async function updateSOSStatus() {
    try {
        const res = await fetch(SOS_STATUS_API_URL);
        const json = await res.json();

        const sosAlert = document.getElementById("sosAlert");
        const sosCard = document.getElementById("sosCard");

        if (json.status && json.is_active && json.data && !sosConfirmed) {

            sosModeActive = true;

            activeSOSId = json.data.id;
            sosLat = json.data.lat;
            sosLng = json.data.lng;

            // Tampilkan banner hanya sekali
            if (!sosAlreadyShown) {
                sosAlert.classList.add("active");
                sosCard.classList.add("sos-active");
                sosAlreadyShown = true;
            }

            document.getElementById("sosState").textContent = "AKTIF DARURAT!!";
            document.getElementById("sosLabel").textContent =
                "Ditekan: " + formatTimestamp(json.data.time);

            // 🔴 MARKER MERAH & FOKUS KE SOS
            if (leafletMarker && leafletMap) {
                leafletMarker.setIcon(sosIcon);

                // 🔔 UBAH POPUP SAAT SOS AKTIF
                leafletMarker
                    .setPopupContent(`
                        🚨 <strong>SOS AKTIF!</strong><br>
                        Lokasi Darurat Tongkat Pintar
                    `)
                    .openPopup();

                // 🔥 TAMBAH ANIMASI BLINK (PENTING!)
                if (leafletMarker.getElement()) {
                    leafletMarker.getElement().classList.add("sos-blink");
                    console.log("🚨 SOS Blink animation triggered from API!");
                }

                leafletMap.invalidateSize();

                setTimeout(() => {
                    leafletMap.setView([sosLat, sosLng], 17, {
                        animate: true,
                        duration: 0.5
                    });
                }, 200);
            }

        } else {
            // RESET SOS
            activeSOSId = null;
            sosLat = null;
            sosLng = null;
            sosAlreadyShown = false;
            sosConfirmed = false;
            sosModeActive = false;

            sosAlert.classList.remove("active");
            sosCard.classList.remove("sos-active");

            document.getElementById("sosState").textContent = "Normal";
            document.getElementById("sosLabel").textContent = "Kondisi aman";

            // 🔵 MARKER NORMAL - HAPUS ANIMASI SAAT SOS SELESAI
            if (leafletMarker) {
                leafletMarker
                    .setIcon(normalIcon)
                    .setPopupContent("📍 Lokasi Tongkat Pintar");

                leafletMarker.getElement()?.classList.remove("sos-blink");
            }

            // 🔥 HAPUS DANGER BOX ANIMASI
            if (sosDangerBox && leafletMap) {
                leafletMap.removeLayer(sosDangerBox);
                sosDangerBox = null;
            }
        }

    } catch (err) {
        console.error("SOS ERROR:", err);
    }
}

/* =====================================================
   REFRESH LOOP (ASLI)
===================================================== */
async function refreshAllData() {
    await Promise.all([updateGPSData(), updateSOSStatus()]);
}

/* =====================================================
   ACTION BUTTONS (ASLI + FIX)
===================================================== */
function sendSOSRequest(source = "manual") {
    const lat = document.getElementById("lat").textContent;
    const lng = document.getElementById("lng").textContent;

    return fetch("../api/send_sos.php", {
        method: "POST",
        headers: { "Content-Type":"application/x-www-form-urlencoded" },
        body: `lat=${lat}&lng=${lng}&source=${encodeURIComponent(source)}`
    }).then(async (response) => {
        await refreshAllData();
        return response;
    });
}

function sendSOS() {
    sendSOSRequest("manual");
}

function triggerSOSWhatsapp(source, gpsValid, lat, lng, force = false) {
    const now = Date.now();
    const lastSendAt = source === "fall" ? lastFallSOSSendAt : lastManualSOSSendAt;

    if (now - lastSendAt < 1500) {
        return;
    }

    if (source === "fall") {
        lastFallSOSSendAt = now;
    } else {
        lastManualSOSSendAt = now;
    }

    fetch("../api/send_sos.php", {
        method: "POST",
        headers: { "Content-Type":"application/x-www-form-urlencoded" },
        body:
            `lat=${gpsValid ? lat : 0}` +
            `&lng=${gpsValid ? lng : 0}` +
            `&source=${source}` +
            `&force=${force ? 1 : 0}`
    })
    .then(res => res.json())
    .then(r => {
        console.log("SOS API:", r);
        if (r.id) activeSOSId = r.id;
    })
    .catch(err => console.error("SOS API ERROR:", err));
}

function confirmSOS() {

    if (!activeSOSId) {
        return alert("Tunanetera dalam kondisi Darurat!!");
    }

    fetch(CONFIRM_SOS_API_URL, {
        method: "POST",
        headers: {
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body: `id=${activeSOSId}`
    })
    .then(res => res.json())
    .then(() => {

        sosModeActive = false;
        sosConfirmed = true;

        document.getElementById("sosAlert").classList.remove("active");
        document.getElementById("sosCard").classList.remove("sos-active");

        document.getElementById("sosState").textContent = "Normal";
        document.getElementById("sosLabel").textContent = "Kondisi aman";

        if (typeof updateGyroscope === "function") {
            updateGyroscope(null);
        }

        if (leafletMarker && leafletMarker.getElement()) {
            leafletMarker.getElement().classList.remove("sos-blink");
        }

        if (sosDangerBox && leafletMap) {
            leafletMap.removeLayer(sosDangerBox);
            sosDangerBox = null;
        }

        activeSOSId = null;
        sosLat = null;
        sosLng = null;
    })
    .catch(err => {
        console.error("CONFIRM SOS ERROR:", err);
    });
}

function openSOSLocation() {
    if (!sosLat || !sosLng) return alert("Lokasi belum tersedia");
    window.open(`https://www.google.com/maps?q=${sosLat},${sosLng}`, "_blank");
}

/* =====================================================
   LEAFLET MAP STATE
===================================================== */
let leafletMap = null;
let leafletMarker = null;

/* =====================================================
   INIT MAP
===================================================== */
function initLeafletMap(lat, lng) {
    if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
        console.warn("⚠️ Invalid coordinates:", {lat, lng});
        return;
    }

    if (!leafletMap) {
        console.log("🗺️ Creating leaflet map with coordinates:", {lat, lng});
        leafletMap = L.map("map").setView([lat, lng], 16);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(leafletMap);
    }

    // 🔥 SELALU BUAT/UPDATE MARKER DENGAN BENAR
    if (!leafletMarker) {
        console.log("✅ Creating new marker at:", {lat, lng});
        leafletMarker = L.marker([lat, lng], { icon: normalIcon })
            .addTo(leafletMap)
            .bindPopup("📍 Lokasi Tongkat Pintar");
    } else {
        console.log("📍 Updating existing marker to:", {lat, lng});
        leafletMarker.setLatLng([lat, lng]);
    }

    leafletMap.setView([lat, lng], 16);
}

/* =====================================================
   SIMULASI JATUH (HANYA TESTING)
===================================================== */
function simulateFall() {

    const lat = document.getElementById("lat").textContent;
    const lng = document.getElementById("lng").textContent;

    // Ubah UI jadi jatuh
    updateGyroscope(1);

    document.getElementById("sosAlert").classList.add("active");
    document.getElementById("sosCard").classList.add("sos-active");

    document.getElementById("sosState").textContent = "SIMULASI JATUH!";
    document.getElementById("sosLabel").textContent = "Trigger dari dashboard";

    // kirim ke backend
    sendSOSRequest("auto")
    .then(res => res.json())
    .then(r => console.log("SOS RESULT:", r));
}

/* =====================================================
   UPDATE MAP REAL-TIME
===================================================== */
function updateLeafletMap(lat, lng) {
    // 🔥 VALIDASI KOORDINAT (HARUS BUKAN 0,0 DAN BUKAN UNDEFINED)
    const latNum = parseFloat(lat);
    const lngNum = parseFloat(lng);

    // Skip hanya jika (0,0) atau undefined
    if (isNaN(latNum) || isNaN(lngNum) || (latNum === 0 && lngNum === 0)) {
        console.warn("⚠️ Invalid GPS coordinates (skipping):", {lat, lng, latNum, lngNum});
        return;
    }

    const point = [latNum, lngNum];
    console.log("🚀 updateLeafletMap called with:", point);

    // Init map pertama kali
    if (!leafletMap) {
        console.log("🗺️ Initializing map with coordinates:", {latNum, lngNum});
        initLeafletMap(latNum, lngNum);
        return;
    }

    // ✅ Marker utama (posisi terkini)
    if (!leafletMarker) {
        console.warn("⚠️ leafletMarker belum ada, membuat marker baru!");
        leafletMarker = L.marker(point, { icon: normalIcon })
            .addTo(leafletMap)
            .bindPopup("📍 Lokasi Tongkat Pintar");
    } else {
        leafletMarker.setLatLng(point);
        console.log("📍 Marker updated to:", point);
    }

    // 🔵 TITIK BARU (PALING TERANG)
    if (leafletMap) {
        const dot = L.circleMarker(point, {
            radius: 3,
            color: "#DC2626",
            fillColor: "#EF4444",
            fillOpacity: 0.9
        }).addTo(leafletMap);

        dotMarkers.push(dot);

        // 🔽 FADING TITIK LAMA
        dotMarkers.forEach((d, i) => {
            const opacity = Math.max(0.15, 1 - (dotMarkers.length - i) * 0.05);
            d.setStyle({ fillOpacity: opacity, opacity });
        });

        // Batasi jumlah titik
        if (dotMarkers.length > MAX_DOTS) {
            leafletMap.removeLayer(dotMarkers.shift());
        }

        // 🎯 FOKUS KE POSISI TERBARU (ANTI LOMPAT)
        leafletMap.panTo(point, { animate: true });
    }
}

const normalIcon = L.icon({
    iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -30]
});

const sosIcon = L.icon({
    iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -30]
});

function showSOSOnMap(lat, lng) {
    if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
        console.warn("⚠️ showSOSOnMap: Invalid coordinates", {lat, lng});
        return;
    }

    if (!leafletMap) {
        console.warn("⚠️ showSOSOnMap: Map not initialized yet");
        return;
    }

    // 🔒 JIKA SUDAH ADA, JANGAN BUAT LAGI
    if (sosDangerBox) {
        console.log("✅ SOS danger box sudah ada");
        return;
    }

    const offset = 0.00005;

    sosDangerBox = L.rectangle([
        [lat - offset, lng - offset],
        [lat + offset, lng + offset]
    ], {
        color: "#DC2626",
        weight: 2,
        fillOpacity: 0,
        dashArray: "6 4"
    }).addTo(leafletMap);

    console.log("🚨 SOS danger box created at:", {lat, lng});
}

/* =====================================================
   SAVE GPS HISTORY (10 MENIT)
===================================================== */
let lastSavedTime = Date.now() - 600000;

function saveGPSHistory(lat, lng) {
    const now = Date.now();

    // 10 menit (600000 ms)
    if (now - lastSavedTime < 600000) return;

    console.log("💾 Kirim GPS ke database...");

    fetch("../api/save_gps.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `lat=${lat}&lng=${lng}&api_key=12345-KUNCI-AMAN-7890`
    })
    .then(res => res.json())
    .then(res => console.log("✅ GPS Saved:", res))
    .catch(err => console.error("❌ Save GPS error:", err));

    lastSavedTime = now;
}

/* =====================================================
   INIT (ASLI)
===================================================== */
// refreshAllData();
// setInterval(refreshAllData, 10000);
</script>

<script>
/* ===============================
   MQTT DASHBOARD (FIX FINAL)
================================ */

const MQTT_BROKER = "wss://broker.hivemq.com:8884/mqtt";
const MQTT_TOPIC  = "smartcane/device01/status";
const mqttClient  = mqtt.connect(MQTT_BROKER);

mqttClient.on("connect", () => {
    console.log("✅ MQTT Connected");
    mqttClient.subscribe(MQTT_TOPIC);
});

mqttClient.on("message", (topic, message) => {
console.log("RAW MQTT:", message.toString());
lastMQTTUpdate = Date.now();
    try {
        const d = JSON.parse(message.toString());
        console.log("📩 MQTT:", d);
        let sosType = String(d.sos_type || "normal").trim().toLowerCase();
        if (!["manual", "fall", "normal"].includes(sosType)) {
            sosType = "normal";
        }
        const sosSource = sosType === "fall" ? "fall" : "manual";

        /* =========================
           GPS VALIDATION
        ========================= */
        const gpsValid =
            d.gps_fix == 1 &&
            Number.isFinite(Number(d.latitude)) &&
            Number.isFinite(Number(d.longitude));

        const lat = Number(d.latitude);
        const lng = Number(d.longitude);

        // 🔥 SIMPAN KE DATABASE SETIAP 10 MENIT
        if (gpsValid) {
            saveGPSHistory(lat, lng);
        }

        /* =========================
           GPS UI (AMAN)
        ========================= */
        ["lat","mapLat","infoLat"].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = gpsValid ? lat.toFixed(6) : "-";
        });

        ["lng","mapLng","infoLng"].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = gpsValid ? lng.toFixed(6) : "-";
        });

        /* =========================
           MAP (HANYA JIKA VALID)
        ========================= */
        if (gpsValid && typeof updateLeafletMap === "function") {
            updateLeafletMap(lat, lng);
        }

        /* =========================
           SENSOR
        ========================= */
        const getDistanceText = (value) => {
            const num = Number(value);
            return Number.isFinite(num) && num >= 0 ? `${num} cm` : "- cm";
        };

        const frontDistance =
            d.jarak_depan ?? d.jarak_front ?? d.distance_depan ?? d.distance_front ?? d.jarak;
        const bottomDistance =
            d.jarak_bawah ?? d.jarak_bottom ?? d.distance_bawah ?? d.distance_bottom;

        const frontEl = document.getElementById("distanceFront");
        if (frontEl) {
            frontEl.textContent = getDistanceText(frontDistance);
        }

        const bottomEl = document.getElementById("distanceBottom");
        if (bottomEl) {
            bottomEl.textContent = getDistanceText(bottomDistance);
        }

        const buzzerEl = document.getElementById("buzzer");
        if (buzzerEl) {
            buzzerEl.textContent = d.buzzer == 1 ? "AKTIF" : "Mati";
        }

        /* =========================
        SOS / GYROSCOPE FINAL FIX
        ========================= */

        const sosValue = Number(d.sos || 0);
        const prevSosValue = lastSosValue;
        lastSosValue = sosValue;

        if (sosValue === 1 && prevSosValue !== 1) {
            triggerSOSWhatsapp(sosSource, gpsValid, lat, lng, true);
        }

        /* =========================
        SOS AKTIF
        ========================= */
        if (sosValue === 1) {

            sosModeActive = true;

            document.getElementById("sosAlert").classList.add("active");
            document.getElementById("sosCard").classList.add("sos-active");

            if (sosType === "fall") {

                document.getElementById("sosState").textContent = "TERDETEKSI JATUH!";
                document.getElementById("sosLabel").textContent = "Sensor gyroscope aktif";

                if (typeof updateGyroscope === "function") {
                    updateGyroscope("fall");
                }

            } else {

                document.getElementById("sosState").textContent = "AKTIF DARURAT!!";
                document.getElementById("sosLabel").textContent = "Tombol SOS ditekan";

                if (typeof updateGyroscope === "function") {
                    updateGyroscope("manual");
                }
            }

            if (gpsValid) {
                sosLat = lat;
                sosLng = lng;

                if (typeof showSOSOnMap === "function") {
                    showSOSOnMap(lat, lng);
                }
            }

            if (leafletMarker && leafletMarker.getElement()) {
                leafletMarker.getElement().classList.add("sos-blink");
            }

        }

        /* =========================
        SOS NORMAL
        ========================= */
        if (sosValue === 0) {

            sosModeActive = false;

            window.sosAlreadySent = false;
            window.sosLocked = false;

            document.getElementById("sosAlert").classList.remove("active");
            document.getElementById("sosCard").classList.remove("sos-active");

            document.getElementById("sosState").textContent = "Normal";
            document.getElementById("sosLabel").textContent = "Kondisi aman";

            if (typeof updateGyroscope === "function") {
                updateGyroscope(null);
            }

            if (leafletMarker && leafletMarker.getElement()) {
                leafletMarker.getElement().classList.remove("sos-blink");
            }
        }

        /* =========================
           STATUS ALAT
        ========================= */
        const badge = document.getElementById("deviceStatusBadge");
        const statusText = document.getElementById("status");

        if (badge && statusText) {
            badge.style.background = "#D1FAE5";
            badge.style.color = "#065F46";
            badge.querySelector("i").className = "fas fa-circle-check";
            statusText.textContent = "AKTIF";
        }

        /* =========================
           TIME
        ========================= */
        if (typeof updateTimeFromGPS === "function") {
            updateTimeFromGPS();
        }

        /* =========================
           MOVEMENT
        ========================= */
        if (gpsValid && typeof updateMovementFromGPS === "function") {
            updateMovementFromGPS(lat, lng);
        }

        /* =========================
        SOS REALTIME FIX FINAL
        ========================= */

        if (sosValue === 1 && sosSource === "fall" && !fallSOSAlreadySent) {
            fallSOSAlreadySent = true;
        }

        if (sosValue === 1 && !window.sosLocked) {

            sosModeActive = true;
            window.sosLocked = true;

            sosLat = gpsValid ? lat : null;
            sosLng = gpsValid ? lng : null;

            document.getElementById("sosAlert").classList.add("active");
            document.getElementById("sosCard").classList.add("sos-active");

            if (sosType === "fall") {
                document.getElementById("sosState").textContent = "TERDETEKSI JATUH!";
                document.getElementById("sosLabel").textContent = "Sensor gyroscope aktif";
                updateGyroscope("fall");
            } else {
                document.getElementById("sosState").textContent = "AKTIF DARURAT!!";
                document.getElementById("sosLabel").textContent = "Tombol SOS ditekan";
                updateGyroscope("manual");
            }

            if (gpsValid && typeof showSOSOnMap === "function") {
                showSOSOnMap(lat, lng);
            }

            if (leafletMarker && leafletMarker.getElement()) {
                leafletMarker.getElement().classList.add("sos-blink");
            }

        }

        if (sosValue === 0) {
            sosModeActive = false;
            fallSOSAlreadySent = false;

            if (!window.sosLocked) {
                updateGyroscope(null);
            }
        }

        if (sosValue === 1 && sosSource !== "fall") {
            fallSOSAlreadySent = false;
        }
    // JANGAN RESET SOS BERDASARKAN MQTT
    } catch (e) {
        console.error("🚨 MQTT ERROR:", e);
    }
});

async function fallbackToDatabase() {
    try {
        const res = await fetch(BASE_URL + "api/get_last_gps.php");
        const json = await res.json();

        if (!json.status) return;

        const lat = parseFloat(json.data.latitude);
        const lng = parseFloat(json.data.longitude);

        if (!lat || !lng || isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
            console.warn("⚠️ Fallback invalid:", lat, lng);
            return;
        }

        ["lat","mapLat","infoLat"].forEach(id => {
            document.getElementById(id).textContent = lat.toFixed(6);
        });

        ["lng","mapLng","infoLng"].forEach(id => {
            document.getElementById(id).textContent = lng.toFixed(6);
        });

        updateLeafletMap(lat, lng);

    } catch (err) {
        console.error("Fallback error:", err);
    }
}

setInterval(() => {
    const now = Date.now();

    if (now - lastMQTTUpdate > 10000) {
        fallbackToDatabase();
    }
}, 5000);

mqttClient.on("error", err => {
    console.error("❌ MQTT Error:", err);
});

// updateSOSStatus();
// setInterval(() => {updateSOSStatus();}, 3000);
</script>

</body>
</html>