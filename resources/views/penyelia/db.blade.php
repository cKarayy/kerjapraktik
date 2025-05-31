<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyelia</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
</head>
<body>
    <div class="header">
        <form action="{{ route('pegawai.logout') }}" method="POST" id="logout-form" style="display: none;">
            @csrf
        </form>

        <!-- Tombol logout yang memunculkan konfirmasi -->
        <img src="{{ asset('images/logout.png') }}" alt="Logout" class="logout" onclick="openLogoutPopup()">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <!-- Konten Dashboard Admin -->
    <div class="dashboard">
        <div class="grid-container">
            <a href="{{ route('qrcode') }}" class="card">
                <div class="card-header">QR CODE ABSENSI</div>
                <div class="card-content">
                    <div class="image-container">
                        <div class="background-box"></div>
                        <img src="{{ asset('images/qr_code.png') }}" alt="QR Code">
                    </div>
                </div>
            </a>
            <a href="{{ route('penyelia.laporanPy') }}" class="card">
                <div class="card-header">LAPORAN ABSENSI</div>
                <div class="card-content">
                    <img src="{{ asset('images/laporan_absensi.png') }}" alt="Laporan Absensi">
                </div>
            </a>
            <a href="{{ route('data_py') }}" class="card">
                <div class="card-header">DATA PEGAWAI</div>
                <div class="card-content">
                    <div class="image-container">
                        <div class="background-box"></div>
                        <img src="{{ asset('images/data_pegawai.png') }}" alt="Data Pegawai">
                    </div>
                </div>
            </a>
            <a href="{{ route('pegawai.registerPg') }}" class="card" onclick="checkAuth(event)">
                <div class="card-header">
                    NEW EMPLOYEE
                </div>
                <div class="card-content">
                    <img src="{{ asset('images/new.png') }}" alt="New E">
                </div>
            </a>
        </div>
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

    <script>
        // Fungsi untuk membuka popup konfirmasi logout
        function openLogoutPopup() {
            document.getElementById('popup-logout').style.display = 'block';  // Menampilkan popup
        }

        // Fungsi untuk menutup popup konfirmasi logout
        function closePopup(id) {
            document.getElementById(id).style.display = 'none';  // Menyembunyikan popup
        }

        // Fungsi untuk mengonfirmasi logout
        function confirmLogout() {
            sessionStorage.setItem("loggedOut", "true");  // Menyimpan status logout
            document.getElementById('logout-form').submit();  // Melakukan submit form logout
            closePopup('popup-logout');  // Menutup popup
        }
    </script>
</body>
</html>
