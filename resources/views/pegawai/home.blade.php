<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/home.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <button class="btn logout" type="submit" onclick="document.getElementById('logout-form').submit()">LOGOUT</button>

        <form id="logout-form" action="{{ route('pegawai.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <!-- QR Camera Preview -->
    <video id="preview" style="width: 100%; max-width: 400px; display: none;"></video>

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

    <!-- jsQR library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>

    <script>
        let scanning = false;
        let video = null;
        let canvas = null;
        let canvasContext = null;

        function scanQR() {
            video = document.getElementById("preview");
            video.style.display = "block";

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: "environment" }
                }
            })
            .then(function(stream) {
                scanning = true;
                video.srcObject = stream;
                video.setAttribute("playsinline", true); 
                video.play();

                canvas = document.createElement("canvas");
                canvasContext = canvas.getContext("2d");

                requestAnimationFrame(tick);
            })
            .catch(function(err) {
                alert("Tidak bisa mengakses kamera: " + err.message);
            });
        }

        function tick() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                let imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code) {
                    scanning = false;
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.style.display = "none";

                    try {
                        let data = JSON.parse(code.data);

                        fetch("{{ route('absensi.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                            },
                            body: JSON.stringify(data)
                        })
                        .then(res => res.json())
                        .then(response => {
                            if (response.success) {
                                showNotification("hadir");
                                changeColor("btn-hadir");
                            } else {
                                alert(response.message);
                            }
                        });

                    } catch (e) {
                        alert("QR code tidak valid!");
                    }

                    return;
                }
            }

            if (scanning) {
                requestAnimationFrame(tick);
            }
        }

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

        function submitIzin() {
            const alasan = document.getElementById('alasan-izin').value;
            if (alasan.trim() === "") {
                alert("Alasan tidak boleh kosong!");
                return;
            }

            fetch("/izin", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ alasan: alasan })
            })
            .then(response => {
                if (response.ok) {
                    closePopup('popup-izin');
                    showNotification("izin");
                    changeColor("btn-izin");
                } else {
                    alert("Gagal mengajukan izin.");
                }
            });
        }

        function submitCuti() {
            let mulai = document.getElementById("tanggal-mulai").value;
            let selesai = document.getElementById("tanggal-selesai").value;
            let alasan = document.getElementById("alasan-cuti").value;

            if (!mulai || !selesai || alasan.trim() === "") {
                alert("Lengkapi semua data cuti!");
                return;
            }

            fetch('/cuti', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tanggal_mulai: mulai,
                    tanggal_selesai: selesai,
                    alasan: alasan
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification("cuti");
                    closePopup('popup-cuti');
                    changeColor("btn-cuti");
                } else {
                    alert("Gagal mengirim cuti: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Terjadi kesalahan, coba lagi.");
            });
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
    </script>
</body>
</html>
