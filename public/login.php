<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Sistem Pendamping IoT</title>

<!-- ================= FONT & ICON ================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

/* ===================================================================
   1. ROOT VARIABLES (DESIGN SYSTEM)
   =================================================================== */
:root{
    --primary-100:#e0f2fe;
    --primary-300:#7dd3fc;
    --primary-500:#2563eb;
    --primary-700:#1e3a8a;

    --accent-100:#fef9c3;
    --accent-400:#fde68a;

    --neutral-100:#f8fafc;
    --neutral-300:#e2e8f0;
    --neutral-600:#64748b;
    --neutral-900:#0f172a;

    --danger:#dc2626;

    --radius-sm:10px;
    --radius-md:16px;
    --radius-lg:24px;

    --shadow-soft:0 15px 50px rgba(37,99,235,.2);
    --shadow-glow:0 0 30px rgba(125,211,252,.7);
}

/* ===================================================================
   2. RESET & WCAG BASE
   =================================================================== */
*,
*::before,
*::after{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

html{
    font-size:16px;
}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:
        radial-gradient(circle at top,var(--primary-100),transparent 60%),
        linear-gradient(180deg,#ffffff,var(--neutral-100));
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
    color:var(--neutral-900);
}

/* WCAG Focus */
*:focus-visible{
    outline:3px solid var(--primary-500);
    outline-offset:3px;
}

/* ===================================================================
   3. LAYOUT WRAPPER - MOBILE FIRST
   =================================================================== */
.login-wrapper{
    width:100%;
    max-width:420px;
    position:relative;
}

/* Brand section - HIDDEN on mobile */
.login-brand{
    display:none;
}

/* ===================================================================
   4. CARD CONTAINER (MOBILE)
   =================================================================== */
.login-container{
    background:#ffffff;
    border-radius:var(--radius-lg);
    padding:32px 24px 28px;
    box-shadow:var(--shadow-soft);
    animation:cardEnter 0.8s cubic-bezier(.22,1,.36,1);
}

/* Card Animation */
@keyframes cardEnter{
    0%{
        opacity:0;
        transform:translateY(40px) scale(.95);
    }
    100%{
        opacity:1;
        transform:none;
    }
}

/* ===================================================================
   5. HEADER SECTION - MOBILE
   =================================================================== */
.header{
    text-align:center;
    margin-bottom:28px;
}

/* Eye Icon - SMALLER on mobile */
.iot-icon{
    font-size:3.5rem;
    background:linear-gradient(
        135deg,
        var(--primary-500),
        var(--primary-300),
        var(--accent-400)
    );
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    filter:drop-shadow(0 0 20px rgba(125,211,252,.6));
    animation:eyeFloat 3s ease-in-out infinite;
    display:inline-block;
    margin-bottom:12px;
}

@keyframes eyeFloat{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-10px)}
}

/* Title - SMALLER on mobile */
.header h2{
    font-size:1.5rem;
    font-weight:700;
    color:var(--neutral-900);
    margin-bottom:6px;
}

/* Subtitle - VISIBLE on mobile */
.header p{
    font-size:0.9rem;
    color:var(--neutral-600);
    font-weight:500;
}

/* ===================================================================
   6. FORM ELEMENTS
   =================================================================== */
form{
    width:100%;
}

.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    font-weight:600;
    font-size:0.9rem;
    color:var(--neutral-900);
    margin-bottom:8px;
}

.input-group label i{
    margin-right:6px;
    color:var(--primary-500);
}

.input-group input{
    width:100%;
    padding:14px 16px;
    font-size:1rem;
    font-family:'Poppins',sans-serif;
    border-radius:var(--radius-md);
    border:2px solid var(--neutral-300);
    background:#f9fafb;
    color:var(--neutral-900);
    transition:all 0.3s ease;
    min-height:48px;
}

.input-group input::placeholder{
    color:#94a3b8;
}

.input-group input:focus{
    border-color:var(--primary-500);
    background:#ffffff;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
    outline:none;
}

/* ===================================================================
   7. BUTTON
   =================================================================== */
button{
    width:100%;
    margin-top:12px;
    padding:16px;
    border-radius:var(--radius-md);
    border:none;
    background:linear-gradient(
        135deg,
        var(--primary-500),
        var(--primary-700)
    );
    color:#ffffff;
    font-size:1rem;
    font-weight:700;
    letter-spacing:0.8px;
    text-transform:uppercase;
    cursor:pointer;
    box-shadow:0 10px 30px rgba(37,99,235,.35);
    transition:all 0.3s ease;
    min-height:50px;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 15px 40px rgba(37,99,235,.45);
}

button:active{
    transform:translateY(0);
}

/* ===================================================================
   8. ERROR MESSAGE
   =================================================================== */
.error-message{
    margin-top:16px;
    padding:12px 14px;
    border-radius:var(--radius-sm);
    background:#fee2e2;
    color:var(--danger);
    font-weight:600;
    font-size:0.85rem;
    text-align:center;
    border:2px solid var(--danger);
    animation:shake 0.5s ease;
}

.error-message i{
    margin-right:6px;
}

@keyframes shake{
    0%,100%{transform:translateX(0)}
    25%{transform:translateX(-8px)}
    75%{transform:translateX(8px)}
}

/* ===================================================================
   9. FOOTER ICONS
   =================================================================== */
.iot-logos{
    margin-top:28px;
    padding-top:24px;
    border-top:2px solid var(--neutral-100);
    display:flex;
    justify-content:center;
    gap:18px;
}

.iot-logos i{
    font-size:1.6rem;
    color:var(--primary-500);
    opacity:0.6;
    transition:all 0.3s ease;
}

.iot-logos i:hover{
    opacity:1;
    transform:scale(1.1);
    color:var(--accent-400);
}

/* ===================================================================
   10. TABLET ADJUSTMENTS
   =================================================================== */
@media(min-width:768px){
    .login-wrapper{
        max-width:460px;
    }

    .login-container{
        padding:40px 36px;
    }

    .iot-icon{
        font-size:4rem;
    }

    .header h2{
        font-size:1.75rem;
    }

    .header p{
        font-size:0.95rem;
    }

    .input-group input{
        padding:15px 18px;
    }

    button{
        padding:17px;
        font-size:1.05rem;
    }
}

/* ===================================================================
   11. DESKTOP CINEMATIC SPLIT MODE
   =================================================================== */
@media(min-width:1024px){

body{
    padding:0;
    overflow:hidden;
    background:linear-gradient(135deg,#e8f4f8 0%,#dbeafe 50%,#bfdbfe 100%);
    position:relative;
}

/* Animated background waves */
body::before{
    content:'';
    position:absolute;
    width:200%;
    height:200%;
    top:-50%;
    left:-50%;
    background:radial-gradient(circle at 30% 40%,rgba(96,165,250,0.15),transparent 40%),
               radial-gradient(circle at 70% 60%,rgba(37,99,235,0.12),transparent 40%);
    animation:waveMove 20s ease-in-out infinite;
    pointer-events:none;
}

@keyframes waveMove{
    0%,100%{transform:translate(0,0) rotate(0deg)}
    33%{transform:translate(5%,5%) rotate(5deg)}
    66%{transform:translate(-5%,3%) rotate(-3deg)}
}

.login-wrapper{
    display:grid;
    grid-template-columns:1.3fr 1fr;
    width:100%;
    height:100vh;
    max-width:1600px;
    gap:0;
    position:relative;
    z-index:1;
}

/* ================= BRAND SECTION (LEFT) ================= */
.login-brand{
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:80px 90px;
    color:#1e3a8a;
    background:linear-gradient(160deg,#f0f9ff 0%,#e0f2fe 50%,#dbeafe 100%);
    position:relative;
    overflow:hidden;
    animation:fadeLeft 1s ease;
}

@keyframes fadeLeft{
    from{
        opacity:0;
        transform:translateX(-60px);
    }
    to{
        opacity:1;
        transform:none;
    }
}

/* Modern vertical separator */
.login-brand::after{
    content:'';
    position:absolute;
    right:0;
    top:10%;
    height:80%;
    width:1px;
    background:linear-gradient(
        to bottom,
        transparent,
        rgba(37,99,235,0.3) 20%,
        rgba(37,99,235,0.5) 50%,
        rgba(37,99,235,0.3) 80%,
        transparent
    );
}

/* Decorative geometric shapes */
.login-brand::before{
    content:'';
    position:absolute;
    top:15%;
    right:8%;
    width:120px;
    height:120px;
    background:linear-gradient(135deg,rgba(96,165,250,0.15),rgba(147,197,253,0.08));
    border-radius:30% 70% 70% 30% / 30% 30% 70% 70%;
    animation:morphShape 8s ease-in-out infinite;
    z-index:0;
}

@keyframes morphShape{
    0%,100%{
        border-radius:30% 70% 70% 30% / 30% 30% 70% 70%;
        transform:rotate(0deg) scale(1);
    }
    50%{
        border-radius:70% 30% 30% 70% / 70% 70% 30% 30%;
        transform:rotate(180deg) scale(1.1);
    }
}

.brand-icon{
    font-size:5.5rem;
    background:linear-gradient(135deg,#2563eb,#3b82f6,#60a5fa);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    filter:drop-shadow(0 4px 20px rgba(37,99,235,0.3));
    margin-bottom:24px;
    animation:eyeFloat 3s ease-in-out infinite;
    position:relative;
    z-index:1;
}

.login-brand h1{
    font-size:3rem;
    font-weight:800;
    line-height:1.2;
    margin-bottom:18px;
    letter-spacing:-0.8px;
    color:#0f172a;
    position:relative;
    z-index:1;
    text-shadow:0 2px 10px rgba(37,99,235,0.1);
}

.login-brand p{
    font-size:1.1rem;
    line-height:1.7;
    max-width:520px;
    color:#475569;
    font-weight:400;
    position:relative;
    z-index:1;
}

/* ================= FORM SECTION (RIGHT) ================= */
.login-container{
    border-radius:0;
    box-shadow:-40px 0 80px rgba(0,0,0,0.08);
    background:#ffffff;
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:60px 70px 70px;
    max-width:100%;
    animation:fadeRight 1s ease;
    position:relative;
}

/* Decorative corner accent */
.login-container::before{
    content:'';
    position:absolute;
    top:0;
    right:0;
    width:200px;
    height:200px;
    background:radial-gradient(circle at top right,rgba(37,99,235,0.08),transparent 70%);
    border-radius:0 0 0 100%;
    z-index:0;
}

.login-container::after{
    content:'';
    position:absolute;
    bottom:0;
    left:0;
    width:180px;
    height:180px;
    background:radial-gradient(circle at bottom left,rgba(96,165,250,0.06),transparent 70%);
    border-radius:0 100% 0 0;
    z-index:0;
}

/* Ensure form content is above decorations */
.login-container form,
.login-container .header,
.login-container .iot-logos{
    position:relative;
    z-index:1;
}

@keyframes fadeRight{
    from{
        opacity:0;
        transform:translateX(60px);
    }
    to{
        opacity:1;
        transform:none;
    }
}

/* Hide mobile icon on desktop */
.iot-icon{
    display:none;
}

.header{
    text-align:left;
    margin-bottom:36px;
}

.header h2{
    font-size:1.9rem;
    margin-bottom:8px;
    color:#0f172a;
}

.header p{
    font-size:0.95rem;
    color:var(--neutral-600);
}

.input-group{
    margin-bottom:22px;
}

.input-group label{
    font-size:0.95rem;
}

.input-group input{
    padding:15px 18px;
    font-size:1.02rem;
    border:2px solid #e2e8f0;
    transition:all 0.3s ease;
}

.input-group input:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,0.12),0 4px 12px rgba(37,99,235,0.15);
    transform:translateY(-1px);
}

button{
    padding:17px;
    font-size:1.05rem;
    margin-top:14px;
    background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
    box-shadow:0 10px 30px rgba(37,99,235,0.35),0 4px 10px rgba(37,99,235,0.2);
    transition:all 0.4s cubic-bezier(0.4,0,0.2,1);
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 40px rgba(37,99,235,0.45),0 6px 15px rgba(37,99,235,0.3);
}

.iot-logos{
    margin-top:35px;
    padding-top:28px;
    border-top:2px solid #f1f5f9;
}

.iot-logos i{
    font-size:1.7rem;
    margin:0 16px;
    opacity:0.7;
    transition:all 0.3s ease;
}

.iot-logos i:hover{
    opacity:1;
    transform:translateY(-3px) scale(1.1);
    color:#2563eb;
}
}

/* ================= VIDEO TOOLTIP HELP ================= */
.video-help-box{
    margin-top:28px;
    position:relative;
    z-index:2;
}

.video-help-btn{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:14px 20px;
    border-radius:16px;
    background:#ffffff;
    color:#1e3a8a;
    font-weight:700;
    font-size:0.95rem;
    text-decoration:none;
    box-shadow:0 10px 30px rgba(37,99,235,0.18);
    border:2px solid rgba(37,99,235,0.12);
    transition:all 0.3s ease;
    position:relative;
}

.video-help-btn i{
    color:#dc2626;
    font-size:1.3rem;
}

.video-help-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 16px 40px rgba(37,99,235,0.28);
    border-color:#2563eb;
}

/* Tooltip */
.tooltip-text{
    visibility:hidden;
    opacity:0;
    width:280px;
    background:#0f172a;
    color:#ffffff;
    text-align:left;
    padding:12px 14px;
    border-radius:12px;
    font-size:0.82rem;
    font-weight:500;
    line-height:1.5;

    position:absolute;
    left:0;
    bottom:-78px;
    z-index:10;

    box-shadow:0 12px 30px rgba(15,23,42,0.25);
    transition:all 0.3s ease;
}

.video-help-btn:hover .tooltip-text{
    visibility:visible;
    opacity:1;
    transform:translateY(4px);
}

/* ===================================================================
   12. LARGE DESKTOP
   =================================================================== */
@media(min-width:1440px){
    .login-brand{
        padding:100px 110px;
    }

    .login-brand h1{
        font-size:3.4rem;
    }

    .login-brand p{
        font-size:1.2rem;
        max-width:560px;
    }

    .login-container{
        padding:70px 80px 80px;
    }

    .header h2{
        font-size:2.1rem;
    }

    .iot-logos i{
        font-size:1.9rem;
        margin:0 18px;
    }
}

/* ===================================================================
   13. ACCESSIBILITY - REDUCED MOTION
   =================================================================== */
@media(prefers-reduced-motion:reduce){
    *{
        animation-duration:0.01ms !important;
        animation-iteration-count:1 !important;
        transition-duration:0.01ms !important;
    }
}

/* ===================================================================
   14. HIGH CONTRAST MODE
   =================================================================== */
@media(prefers-contrast:high){
    .input-group input{
        border-width:3px;
    }
    
    button{
        border:3px solid var(--neutral-900);
    }
}

/* ===================================================================
   15. MOBILE VIDEO HELP LINK (VISIBLE ONLY ON MOBILE)
   =================================================================== */
.mobile-video-help{
    margin-bottom:22px;
    text-align:center;
}

.mobile-video-help a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:12px 16px;
    border-radius:14px;
    background:#eff6ff;
    color:#1e3a8a;
    font-weight:700;
    font-size:0.88rem;
    text-decoration:none;
    border:2px solid #bfdbfe;
}

.mobile-video-help i{
    color:#dc2626;
    font-size:1.2rem;
}

@media(min-width:1024px){
    .mobile-video-help{
        display:none;
    }
}

</style>
</head>

<body>

<div class="login-wrapper">

<!-- ================= BRAND (DESKTOP ONLY) ================= -->
<div class="login-brand">
    <i class="fas fa-eye-low-vision brand-icon" aria-hidden="true"></i>
    <h1>Sistem Pendamping IoT</h1>
    <p>
        Sistem tongkat pintar berbasis Internet of Things untuk meningkatkan
        keamanan dan kemandirian penyandang tunanetra dalam beraktivitas sehari-hari.
    </p>

    <div class="video-help-box">
    <a href="LINK_YOUTUBE_KAMU" target="_blank" class="video-help-btn">
        <i class="fab fa-youtube"></i>
        Lihat Video Panduan
        <span class="tooltip-text">
            Klik untuk melihat video cara kerja tongkat pintar dan panduan penggunaan sistem.
        </span>
    </a>
</div>
</div>

<!-- ================= FORM ================= -->
<div class="login-container">

<div class="header">
    <i class="fas fa-eye-low-vision iot-icon" aria-hidden="true"></i>
    <h2>Sistem Pendamping IoT</h2>
    <p>Masukkan kredensial untuk akses pendamping</p>
</div>

<div class="mobile-video-help">
    <a href="LINK_YOUTUBE_KAMU" target="_blank">
        <i class="fab fa-youtube"></i>
        Video Panduan Penggunaan
    </a>
</div>

<form onsubmit="return false;">

<div class="input-group">
<label for="nomor_hp">
    <i class="fas fa-phone"></i>Nomor HP
</label>
<input 
    type="text" 
    id="nomor_hp" 
    name="nomor_hp" 
    required 
    placeholder="Masukkan nomor HP">
</div>

<div class="input-group" id="otp-group" style="display:none;">
<label>
    <i class="fas fa-key"></i>Kode OTP
</label>
<input 
    type="text" 
    id="otp" 
    placeholder="Masukkan kode OTP">
    <!-- ✅ TIMER DI BAWAH -->
    <div id="otpTimerWrapper" style="
        display:flex;
        justify-content:flex-end;
        margin-top:6px;">
        <span id="otpTimer" style="
            font-size:12px;
            font-weight:600;
            color:#2563eb;
            display:none;">
            60s
        </span>
    </div>
</div>

<button type="button" id="btnKirimOtp" onclick="kirimOTP()">
    Kirim OTP
</button>

<button type="button" id="loginBtn" onclick="verifikasiOTP()" style="display:none;">
    Login
</button>

<div id="errorBox"></div>

</form>

<div class="iot-logos" aria-hidden="true">
<i class="fas fa-wifi" title="Koneksi Nirkabel"></i>
<i class="fas fa-microchip" title="Mikrokontroler"></i>
<i class="fas fa-cloud" title="Cloud Computing"></i>
<i class="fas fa-route" title="Jaringan IoT"></i>
</div>

</div>
</div>

<script>
let countdownInterval = null;
let isSendingOTP = false;

// ================= ERROR HANDLER =================
function showError(msg) {
    document.getElementById("errorBox").innerHTML = `
        <p class="error-message">
        <i class="fas fa-circle-exclamation"></i> ${msg}
        </p>
    `;
}

function clearError() {
    document.getElementById("errorBox").innerHTML = "";
}

// ================= VALIDASI NOMOR =================
function validasiNomor(nomor) {

    if (!nomor) {
        return "Nomor HP harus diisi!";
    }

    if (!/^[0-9]+$/.test(nomor)) {
        return "Nomor HP hanya boleh angka!";
    }

    if (!/^(\+62|62|08)[0-9]{8,13}$/.test(nomor)) {
        return "Format nomor HP tidak valid!";
    }

    if (nomor.length < 10 || nomor.length > 15) {
        return "Panjang nomor tidak valid!";
    }

    // 🔥 anti nomor palsu sederhana
    if (/^(.)\1+$/.test(nomor)) {
        return "Nomor tidak valid (angka berulang)";
    }

    if (nomor === "1234567890") {
        return "Nomor tidak valid!";
    }

    return null; // valid
}

// ================= KIRIM OTP =================
function kirimOTP() {

    let nomor = document.getElementById("nomor_hp").value.trim();
    let btn = document.getElementById("btnKirimOtp");

    clearError();

    let error = validasiNomor(nomor);
    if (error) {
        showError(error);
        return;
    }

    // 🔄 loading state
    btn.disabled = true;
    btn.innerText = "Mengirim...";
    isSendingOTP = true;

    fetch("kirim_otp.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "nomor_hp=" + encodeURIComponent(nomor)
    })
    .then(res => res.text())
    .then(data => {
        console.log("RESPON:", data);

        if (data !== "success") {
            showError(data);
            btn.disabled = false;
            btn.innerText = "Kirim OTP";
            return;
        }

        // ✅ sukses
        clearError();

        btn.style.display = "none";

        document.getElementById("otp-group").style.display = "block";
        document.getElementById("loginBtn").style.display = "block";
        document.getElementById("otp").focus();
        startCountdown(60);
    })
    .catch(() => {
        showError("Terjadi kesalahan jaringan!");
        btn.disabled = false;
        btn.innerText = "Kirim OTP";
        isSendingOTP = false;
    });
}

// ================= VERIFIKASI OTP =================
function verifikasiOTP() {

    let nomor = document.getElementById("nomor_hp").value.trim();
    let otp = document.getElementById("otp").value.trim();
    let btn = document.getElementById("loginBtn");

    clearError();

    if (!otp) {
        showError("Kode OTP harus diisi!");
        return;
    }

    if (!/^[0-9]{4,6}$/.test(otp)) {
        showError("OTP harus 4-6 digit angka!");
        return;
    }

    // 🔄 loading
    btn.disabled = true;
    btn.innerText = "Memverifikasi...";

    fetch("verifikasi_otp.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "nomor_hp=" + encodeURIComponent(nomor) + "&otp=" + encodeURIComponent(otp)
    })
    .then(res => res.text())
    .then(data => {

        if (data === "success") {
            window.location.href = "index.php";
            return;
        }

        showError("OTP salah atau kadaluarsa!");
        btn.disabled = false;
        btn.innerText = "Login";
    })
    .catch(() => {
        showError("Terjadi kesalahan jaringan!");
        btn.disabled = false;
        btn.innerText = "Login";
    });
}

function startCountdown(duration = 60) {

    const timerText = document.getElementById("otpTimer");

    if (!timerText) return;

    // 🔥 FIX: hentikan timer lama (biar tidak double)
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }

    let timer = duration;

    timerText.style.display = "block";
    timerText.onclick = null;
    timerText.classList.remove("resend"); // reset style

    countdownInterval = setInterval(() => {
        timer--;

        timerText.textContent = timer + "s";
        timerText.style.color = timer < 10 ? "#dc2626" : "#2563eb";

        if (timer <= 0) {
            clearInterval(countdownInterval);

            timerText.textContent = "Kirim ulang";
            timerText.style.cursor = "pointer";
            timerText.classList.add("resend"); // 🔥 aktifkan animasi

            timerText.onclick = function () {
                kirimOTP();
            };

            isSendingOTP = false;
        }

    }, 1000);
}
</script>

</body>
</html>