<?php
session_start(); 

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

    header("Location: login.php"); 
    exit; 
}

include __DIR__ . '/../config/koneksi.php';

// 🔥 FORMAT UNTUK INPUT (0857-1991-6327)
function formatNomor($nomor)
{
    if (substr($nomor, 0, 2) == "62") {
        $nomor = "0" . substr($nomor, 2);
    }

    return substr($nomor, 0, 4) . '-' .
           substr($nomor, 4, 4) . '-' .
           substr($nomor, 8);
}

// 🔥 FORMAT TOOLTIP (085719916327)
function formatTooltip($nomor)
{
    if (substr($nomor, 0, 2) == "62") {
        return "0" . substr($nomor, 2);
    }
    return $nomor;
}

// 🔥 PROSES SIMPAN
if (isset($_POST['nomor']) && !empty($_POST['nomor'])) {

    $nomor = $_POST['nomor'];

    // hapus strip
    $nomor = str_replace('-', '', $nomor);

    // 🔥 VALIDASI ANGKA
    if (!is_numeric($nomor)) {
        $error = "Nomor harus berupa angka!";
    } else {

        // 🔥 VALIDASI PANJANG
        if (strlen($nomor) < 10 || strlen($nomor) > 15) {
            $error = "Nomor tidak valid!";
        } else {

            // ubah 0 → 62
            if (substr($nomor, 0, 1) == "0") {
                $nomor = "62" . substr($nomor, 1);
            }

            $result = mysqli_query($conn, "
                UPDATE settings 
                SET value='$nomor' 
                WHERE nama_setting='nomor_sos'
            ");

            if ($result) {
                $success = "Nomor berhasil diperbarui!";
            } else {
                $error = "Gagal menyimpan data!";
            }
        }
    }
}

// 🔥 AMBIL DATA
$query = mysqli_query($conn, "SELECT value FROM settings WHERE nama_setting='nomor_sos'");
$data = mysqli_fetch_assoc($query);
$nomor_sos = $data['value'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengaturan Nomor SOS</title>

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
    max-width: 600px;
    margin: 0 auto;
}

/* CARD */
.card {
    background: #fff;
    border-radius: 20px;
    padding: 22px;
    margin-bottom: 18px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
}

/* HEADER */
.header-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.back-btn {
    width: 46px;
    height: 46px;
    background: #2563EB;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.back-btn i {
    color: #fff;
    font-size: 18px;
}

.header-content {
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    position: static;
    transform: none;
    text-align: left;
    margin-left: 12px;
}

.header-content h1 {
    font-size: 26px;
    color: #1F2937;
    font-weight: 700;
}

.header-content p {
    font-size: 14px;
    color: #6B7280;
}

/* FORM */
.form-group {
    margin-top: 20px;
}

label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 14px;
    margin-top: 8px;
    border-radius: 12px;
    border: 2px solid #E5E7EB;
    font-size: 15px;
    transition: 0.25s;
}

input:focus {
    border-color: #2563EB;
    outline: none;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}

/* BUTTON */
button {
    width: 100%;
    margin-top: 18px;
    padding: 14px;
    border-radius: 12px;
    border: none;
    background: #2563EB;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #1D4ED8;
}

/* SUCCESS */
.success {
    background: #D1FAE5;
    color: #065F46;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
    margin-top: 10px;
}

/* ERRROR */
.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
    margin-top: 10px;
}

.input-info {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    font-size: 12px;
    color: #6c757d;
}

.input-info i {
    color: #17a2b8; /* biru info */
}

/* DEFAULT = MOBILE (lebih natural ke kiri) */
.header-content {
    position: static;
    transform: none;
    text-align: left;
    margin-left: 12px;
}

/* RESPONSIVE DESKTOP */
@media (min-width: 768px) {
    .header-content {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        margin-left: 0;
    }
}

/* RESPONSIVE MOBILE*/
@media (max-width: 480px) {
    .header-content h1 {
        font-size: 18px;
    }

    .header-content p {
        font-size: 12px;
    }
}
</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="card header-card">

        <a href="sos.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="header-content">
            <h1>Pengaturan Nomor SOS</h1>
            <p>Ubah nomor tujuan notifikasi darurat</p>
        </div>

        <div style="width:46px;"></div>

    </div>
    
    <!-- FORM -->
    <div class="card">

        <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">

            <div class="form-group">
                <label>Nomor SOS (darurat)</label>
                <input 
                type="text" 
                name="nomor" 
                value="<?= $nomor_sos ? formatNomor($nomor_sos) : ''; ?>"
                title="<?= $nomor_sos ? formatTooltip($nomor_sos) : ''; ?>"
                required>

            <!-- INFO -->
            <small class="input-info">
                <i class="fas fa-info-circle"></i>
                Gunakan nomor aktif agar dapat dihubungi saat darurat
            </small>
            </div>

            <button type="submit">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>

        </form>

    </div>

</div>

</body>
</html>