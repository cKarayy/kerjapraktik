<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/home.css') }}">

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
        <button id="btn-hadir" class="btn hadir" onclick="scanQR()">HADIR</button>
        <button id="btn-izin" class="btn izin" onclick="openPopup('popup-izin')">IZIN</button>
        <button id="btn-cuti" class="btn cuti" onclick="openPopup('popup-cuti')">CUTI</button>
        <button class="btn logout"  type="submit" onclick="document.getElementById('logout-form').submit()">LOGOUT</button>

        <form id="logout-form" action="{{ route('pegawai.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <!-- Popup Izin -->
    <div id="popup-izin" class="popup">
        <div class="popup-content">
            <h3>IZIN</h3>
            <p class="label-izin">Tulis alasan izin dibawah ini:</p>
            <input type="text" id="alasan-izin" class="input-underline">

            <div class="popup-buttons">
                <button class="btn-done" onclick="submitIzin()">DONE</button>
                <button class="btn-cancel" onclick="closePopup('popup-izin')">CANCEL</button>
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
        function changeColor(id) {
            let tombol = document.getElementById(id);

            tombol.style.backgroundColor = "#4B0082";
            tombol.style.color = "white";

            setTimeout(() => {
                tombol.style.backgroundColor = "#FFD700";
                tombol.style.color = "#4B0082";
            }, 3000);
        }

        function scanQR() {
            let now = new Date();
            let tanggal = now.toLocaleDateString("id-ID");
            let waktu = now.toLocaleTimeString("id-ID");
            let keterlambatan = "00.00.00"; // Bisa dihitung otomatis nanti

            alert("Membuka kamera untuk scan QR...");
            showNotification("hadir");
            changeColor("btn-hadir");
            addHistory(tanggal, waktu, "Hadir", keterlambatan);
        }

        function openPopup(id) {
            document.getElementById(id).style.display = "block";
        }

        function closePopup(id) {
            document.getElementById(id).style.display = "none";
        }

        function submitIzin() {
            let alasan = document.getElementById("alasan-izin").value;
            if (alasan.trim() === "") {
                alert("Masukkan alasan izin!");
                return;
            }
            showNotification("izin");
            closePopup('popup-izin');
            changeColor("btn-izin");
        }

        function submitCuti() {
            let mulai = document.getElementById("tanggal-mulai").value;
            let selesai = document.getElementById("tanggal-selesai").value;
            let alasan = document.getElementById("alasan-cuti").value;

            if (!mulai || !selesai || alasan.trim() === "") {
                alert("Lengkapi semua data cuti!");
                return;
            }

            showNotification("cuti");
            closePopup('popup-cuti');
            changeColor("btn-cuti");
        }

        function showNotification(jenis) {
            let gambar = "";
            let pesan = "";

            if (jenis === "hadir") {
                gambar = "{{ asset('images/success.png') }}";
                pesan = "Berhasil!";
            } else if (jenis === "izin") {
                gambar = "{{ asset('images/success.png') }}";
                pesan = "Izin sudah diajukan.";
            } else if (jenis === "cuti") {
                gambar = "{{ asset('images/success.png') }}";
                pesan = "Cuti sudah diajukan.";
            }

            document.getElementById("notifikasi-gambar").src = gambar;
            document.getElementById("notifikasi-text").innerText = pesan;
            document.getElementById("notifikasi").style.display = "block";

            setTimeout(() => {
                document.getElementById("notifikasi").style.display = "none";
            }, 3000);
        }

        // function logout() {
        //     document.getElementById('logout-form').submit();
        // }
    </script>

</body>
</html>
