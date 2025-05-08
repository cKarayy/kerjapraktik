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
        <!-- Hidden input for id_shift -->
        <input type="hidden" id="id_shift" value="{{ $pegawai->id_shift }}">

        <button id="btn-hadir" class="btn hadir" onclick="scanQR()">HADIR</button>
        <button id="btn-izin" class="btn izin" onclick="openPopup('popup-izin')">IZIN</button>
        <button id="btn-cuti" class="btn cuti" onclick="openPopup('popup-cuti')">CUTI</button>
        <button class="btn logout" type="submit" onclick="document.getElementById('logout-form').submit()">LOGOUT</button>

        <form id="logout-form" action="{{ route('pegawai.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

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

      function scanQR() {
    document.getElementById('reader').style.display = 'block';  // Menampilkan kamera QR
    document.getElementById("btn-hadir").disabled = true;  // Menonaktifkan tombol Hadir
    const html5Qr = new Html5Qrcode("reader");

    html5Qr.start(
        { facingMode: "environment" },  // Menggunakan kamera belakang
        { fps: 10, qrbox: 250 },
        (decodedText) => {
            html5Qr.stop().then(() => {
                document.getElementById('reader').style.display = 'none';  // Menyembunyikan kamera setelah scan
                document.getElementById('result').innerText = "";  // Kosongkan hasil scan QR

                // Jika decodedText berupa string JSON, kita perlu mengubahnya menjadi objek
                const qrData = JSON.parse(decodedText);  // Pastikan QR mengandung data dalam format JSON

                const karyawanId = document.getElementById('karyawan_id').value;  // Mengambil ID karyawan
                const idShift = document.getElementById('id_shift').value;  // Mengambil ID shift dari data pegawai
                const status = "hadir";  // Status absensi adalah "Hadir"
                const waktuMasuk = new Date();  // Mengambil waktu sekarang sebagai waktu masuk
                const idCode = qrData.uuid;  // ID QR code yang dipindai (harusnya sesuai dengan data yang di-generate sebelumnya)

                // Hitung keterlambatan
                const waktuShiftMulai = new Date(waktuMasuk.toLocaleDateString() + " " + "10:00");  // Waktu mulai shift (contoh 10:00)
                const keterlambatan = Math.max(0, (waktuMasuk - waktuShiftMulai) / (1000 * 60));  // Menghitung keterlambatan dalam menit

                // Mengirimkan seluruh data absensi ke server
                fetch("{{ route('absensi.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        id_karyawan: karyawanId,  // ID karyawan yang sedang login
                        id_shift: idShift,  // ID shift dari data pegawai
                        status: status,  // Status kehadiran
                        waktu_masuk: waktuMasuk.toISOString(),  // Waktu absensi (waktu saat QR dipindai)
                        id_code: idCode,  // ID QR code yang dipindai
                        keterlambatan: keterlambatan  // Keterlambatan dalam menit
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification("hadir");  // Menampilkan notifikasi berhasil
                        changeColor("btn-hadir");  // Mengubah warna tombol Hadir
                    } else {
                        alert("Gagal: " + data.message);  // Jika gagal, tampilkan pesan error
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan.");
                });
            });
        },
        (errorMessage) => {
            // Callback error (opsional)
        }
    ).catch(err => {
        alert("Tidak dapat membuka kamera: " + err);  // Menampilkan error jika kamera gagal
    });
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
                    "X-CSRF-TOKEN": csrfToken
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
                    'X-CSRF-TOKEN': csrfToken
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
                alert("Terjadi kesalahan, coba lagi.");
            });
        }

        function showNotification(jenis) {
            let gambar = "{{ asset('images/success.png') }}";
            let pesan = jenis === "hadir" ? "Berhasil!" :
                        jenis === "izin" ? "Izin sudah diajukan." :
                        "Cuti sudah diajukan.";

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
