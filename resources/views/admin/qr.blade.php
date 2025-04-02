<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <link rel="stylesheet" href="{{ asset('css/admin/qr.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="keterangan-container">
        <span>SHIFT :</span>
        <label class="custom-radio">
            <input type="radio" name="shift" value="pagi" id="shiftPagi" onclick="calculateDelay('pagi')">
            <span class="checkmark"></span> PAGI
        </label>
        <label class="custom-radio">
            <input type="radio" name="shift" value="middle" id="shiftMiddle" onclick="calculateDelay('middle')">
            <span class="checkmark"></span> MIDDLE
        </label>
        <label class="custom-radio">
            <input type="radio" name="shift" value="malam" id="shiftMalam" onclick="calculateDelay('malam')">
            <span class="checkmark"></span> MALAM
        </label>
    </div>

    <div class="card-content">
        <div class="image-container">
            <div class="background-box"></div>
            <img id="qr-code" src="" alt="QR Code"> <!-- Ganti QR Code secara dinamis -->
        </div>
    </div>

    <p id="time-display"></p>
    <p id="delay-display"></p> <!-- Menampilkan keterlambatan -->

    <script>
        let shift = ''; // Untuk menyimpan shift yang dipilih

        function fetchQRCode() {
            $.get('/generate-qr', function(data) {
                // Perbarui QR Code dengan base64
                $('#qr-code').attr('src', 'data:image/png;base64,' + data.qr);

                // Simpan QR Code yang dihasilkan untuk absensi
                localStorage.setItem('latestQR', data.code);
            });
        }

        function startCountdown() {
            let timeLeft = 5;

            function updateDisplay() {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                document.getElementById("time-display").textContent =
                    "Time: " + minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');

                if (timeLeft > 0) {
                    timeLeft--;
                } else {
                    timeLeft = 5;
                    fetchQRCode(); // Ambil QR Code baru setiap 5 detik
                }
            }

            updateDisplay();
            setInterval(updateDisplay, 1000);
        }

        function calculateDelay(selectedShift) {
            // Tentukan jam masuk untuk shift pagi dan malam
            let shiftStartTime = selectedShift === 'pagi' ? 8 : 20; // Shift Pagi: 08:00, Shift Malam: 20:00

            let currentTime = new Date();
            let currentHour = currentTime.getHours();
            let currentMinute = currentTime.getMinutes();

            // Hitung keterlambatan
            let delayMinutes = (currentHour - shiftStartTime) * 60 + currentMinute;

            // Jika belum melewati waktu shift
            if (delayMinutes < 0) {
                delayMinutes = 0; // Tidak ada keterlambatan sebelum jam mulai
            }

            // Tampilkan keterlambatan
            let delayText = '';
            if (delayMinutes > 0) {
                let hours = Math.floor(delayMinutes / 60);
                let minutes = delayMinutes % 60;
                delayText = `Keterlambatan: ${hours} jam ${minutes} menit`;
            } else {
                delayText = 'Tidak terlambat';
            }

            document.getElementById("delay-display").textContent = delayText;
        }

        function scanQRCode(code) {
            $.post("{{ route('scan.qr') }}", {
                _token: "{{ csrf_token() }}",
                code: code
            }, function(response) {
                alert(response.message);
            });
        }

        // Ambil QR Code pertama kali
        window.onload = function() {
            fetchQRCode();
            startCountdown();
        };
    </script>
</body>
</html>
