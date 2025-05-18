<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/home.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="{{ route('pegawai.history') }}">
            <img src="{{ asset('images/history.png') }}" alt="History" class="history-icon">
        </a>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="greeting">
        <h2>Hai, <span id="nama-pegawai">{{ $pegawai->nama_lengkap }}</span>.</h2>
        <p>Semangat Bekerja!</p>
    </div>

    <div class="container">
        <input type="hidden" id="karyawan_id" value="{{ $pegawai->id }}">
        <input type="hidden" id="id_shift" value="{{ $pegawai->id_shift }}">

        <button id="btn-hadir" class="btn hadir" onclick="scanQR()">HADIR</button>
        <button id="btn-izin" class="btn izin" onclick="openPopup('popup-izin')">IZIN</button>
        <button id="btn-cuti" class="btn cuti" onclick="openPopup('popup-cuti')">CUTI</button>

        <!-- Tombol LOGOUT yang akan memunculkan konfirmasi -->
        <button class="btn logout" type="button" onclick="openLogoutPopup()">LOGOUT</button>
    </div>

    <!-- Popup Konfirmasi Logout -->
    <div id="popup-logout" class="popup" style="display:none;">
        <div class="popup-content">
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin logout?</p>
            <div class="popup-buttons">
                <button type="button" class="btn-done" onclick="confirmLogout()">YA</button>
                <button type="button" class="btn-cancel" onclick="closePopup('popup-logout')">BATAL</button>
            </div>
        </div>
    </div>

    <!-- Form Logout -->
    <form id="logout-form" action="{{ route('pegawai.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Tempat scanner QR -->
    <div id="reader" style="width:300px; margin:20px auto; display:none;"></div>
    <div id="result" style="text-align:center; color:green;"></div>

    <!-- Popup Izin -->
    <div id="popup-izin" class="popup">
        <div class="popup-content">
            <h3>IZIN</h3>
            <p class="label-izin">Tulis alasan izin dibawah ini:</p>
            <input type="text" id="alasan-izin" class="input-underline" required>

            <div class="popup-buttons">
                <button type="button" class="btn-done" onclick="submitIzin()">DONE</button>
                <button type="button" class="btn-cancel" onclick="closePopup('popup-izin')">CANCEL</button>
            </div>
        </div>
    </div>

    <!-- Popup Cuti -->
    <div id="popup-cuti" class="popup">
        <div class="popup-content">
            <h3>CUTI</h3>
            <div class="input-group">
                <label>Dari Tanggal:</label>
                <input type="date" id="tanggal-mulai">
            </div>
            <div class="input-group">
                <label>Sampai Tanggal:</label>
                <input type="date" id="tanggal-selesai">
            </div>
            <p class="label-alasan">Tulis alasanmu dibawah ini:</p>
            <input type="text" id="alasan-cuti" class="input-underline">
            <div class="popup-buttons">
                <button class="btn-done" onclick="submitCuti()">DONE</button>
                <button class="btn-cancel" onclick="closePopup('popup-cuti')">CANCEL</button>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <div id="notifikasi" class="notifikasi">
        <img id="notifikasi-gambar" src="" alt="Notifikasi">
        <p id="notifikasi-text"></p>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function changeColor(id) {
            let tombol = document.getElementById(id);
            tombol.style.backgroundColor = "#4B0082";
            tombol.style.color = "white";
            setTimeout(() => {
                tombol.style.backgroundColor = "#FFD700";
                tombol.style.color = "#4B0082";
            }, 3000);
        }

        function openPopup(id) {
            document.getElementById(id).style.display = "block";
        }

        function closePopup(id) {
            document.getElementById(id).style.display = "none";
        }

        // Fungsi untuk membuka popup konfirmasi logout
        function openLogoutPopup() {
            document.getElementById('popup-logout').style.display = 'block';  // Menampilkan popup
        }

        // Fungsi untuk menutup popup konfirmasi logout
        function closePopup(id) {
            document.getElementById(id).style.display = 'none';  // Menyembunyikan popup
        }

        function confirmLogout() {
            sessionStorage.setItem("loggedOut", "true");
            document.getElementById('logout-form').submit();  // Melakukan submit form logout
        }

        // Menghapus status logout setelah login berhasil
        if (sessionStorage.getItem("loggedOut") === "true") {
            sessionStorage.removeItem("loggedOut");  // Hapus status logout
        }

        // Jika pengguna sudah logout, redirect ke halaman login
        if (sessionStorage.getItem("loggedOut") === "true") {
            window.location.href = "{{ route('pegawai.loginPg') }}";  // Redirect ke halaman login
        }


    </script>
</body>
</html>
