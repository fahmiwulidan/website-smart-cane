# 🦯 SmartCane Website

SmartCane Website merupakan platform monitoring berbasis **Internet of Things (IoT)** yang dirancang untuk membantu keluarga atau pendamping memantau kondisi tongkat pintar secara **real-time**. Sistem terintegrasi dengan **WhatsApp** untuk pengiriman OTP, notifikasi darurat, dan informasi lokasi pengguna.

🌐 **Website:** http://smartcane.cu.ma/

---

# ✨ Fitur Utama

- 🔐 Login menggunakan OTP melalui WhatsApp
- 📍 Monitoring lokasi SmartCane secara real-time
- 📡 Status koneksi perangkat
- 🚶 Status pergerakan pengguna
- 🔊 Status buzzer
- 🚧 Monitoring sensor ultrasonik depan
- 🕳️ Monitoring sensor ultrasonik bawah
- 🚨 SOS Darurat dengan notifikasi WhatsApp
- 🤕 Deteksi jatuh menggunakan Gyroscope
- 🗺️ Menampilkan lokasi terakhir ketika perangkat offline lebih dari 5 menit

---

# 🚀 Cara Menggunakan

## 1️⃣ Login

1. Buka website **SmartCane**
2. Masukkan nomor WhatsApp yang aktif
3. Klik **Kirim OTP**
4. Masukkan kode OTP yang diterima melalui WhatsApp
5. Klik **Masuk**

---

## 2️⃣ Dashboard

Dashboard menampilkan seluruh kondisi SmartCane secara **real-time**.

### 📡 Status Alat
Menampilkan kondisi perangkat:

- 🟢 Aktif
- 🔴 Tidak Aktif

> Jika perangkat offline lebih dari **5 menit**, sistem akan menyimpan lokasi terakhir SmartCane.

---

### 🚶 Status Pergerakan

Menampilkan apakah SmartCane sedang:

- Bergerak
- Diam

---

### 📍 Lokasi

Menampilkan lokasi pengguna secara real-time serta lokasi terakhir ketika perangkat tidak aktif.

---

### 🔊 Status Buzzer

Menampilkan kondisi buzzer:

- Aktif
- Tidak Aktif

---

### 🚧 Sensor Ultrasonik

Dashboard menampilkan dua sensor utama:

- **Sensor Depan** → Mendeteksi hambatan di depan pengguna.
- **Sensor Bawah** → Mendeteksi lubang atau perubahan permukaan jalan.

---

# 🚨 SOS Darurat

Pengguna dapat mengatur nomor WhatsApp tujuan melalui menu **Pengaturan SOS**.

Ketika tombol SOS ditekan, sistem akan secara otomatis:

- 📩 Mengirim pesan darurat ke WhatsApp
- 🌐 Menampilkan alert pada website
- 📍 Mengirim lokasi pengguna

---

# 🤕 Deteksi Jatuh (Gyroscope)

Apabila SmartCane mendeteksi pengguna terjatuh, sistem akan secara otomatis:

- 📩 Mengirim notifikasi ke WhatsApp
- 🌐 Menampilkan alert pada website
- 📍 Mengirim lokasi pengguna

---

# 🗺️ Riwayat Lokasi

Jika SmartCane berhenti mengirim data selama lebih dari **5 menit**, sistem akan:

- Menyimpan lokasi terakhir perangkat
- Menampilkan lokasi tersebut pada dashboard

---

# 💡 Teknologi

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- Firebase Realtime Database
- WhatsApp API
- Google Maps API
- Internet of Things (ESP32)

---

# 🎯 Tujuan

SmartCane dikembangkan untuk meningkatkan keamanan, keselamatan, dan kemandirian penyandang tunanetra melalui sistem monitoring berbasis IoT yang terintegrasi dengan website dan WhatsApp.

---

<div align="center">

**🦯 SmartCane - Smart Walking Stick for Better Mobility**

Made with ❤️ by **Fahmi Wulidan**

</div>
