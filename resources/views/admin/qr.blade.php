<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <img src="data:image/png;base64,{{ $qr }}" alt="QR Code">
        </div>
    </div>

    <p id="time-display"></p>
    <p id="delay-display"></p>

    <script>
        let currentQR = '';
        let shift = '';
        
        function fetchQRCode() {
            $.get('/generate-qr', function(data) {
                console.log("QR Code Data:", data); // Debugging

                if (data.qr && data.qr.length > 100) {
                    $('#qr-code').attr('src', 'data:image/png;base64,' + data.qr);
                } else {
                    console.error("QR Code tidak ditemukan atau tidak valid");
                }
            }).fail(function(xhr, status, error) {
                console.error("Gagal mengambil QR Code dari server: ", error);
            });
        }

        function calculateDelay(selectedShift) {
            let shiftStart = selectedShift === 'pagi' ? 8 :
                             selectedShift === 'middle' ? 14 : 20;

            let now = new Date();
            let delayMinutes = (now.getHours() - shiftStart) * 60 + now.getMinutes();

            if (delayMinutes < 0) delayMinutes = 0;

            let hours = Math.floor(delayMinutes / 60);
            let minutes = delayMinutes % 60;
            document.getElementById("delay-display").textContent =
                delayMinutes > 0 ? `Keterlambatan: ${hours} jam ${minutes} menit` : 'Tidak terlambat';
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
                    fetchQRCode(); // Ambil ulang QR setiap 5 detik
                }
            }

            updateDisplay();
            setInterval(updateDisplay, 1000);
        }

        window.onload = function() {
            fetchQRCode();
            startCountdown();
        }
    </script>
</body>
</html>
