<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <link rel="stylesheet" href="{{ asset('css/admin/qr.css') }}">
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="card-content">
        <div class="image-container">
            <div class="background-box"></div>
                <img src="{{ asset('images/data_pegawai.png') }}" alt="Data Pegawai"> <!-- ganti qr code tar-->
        </div>
    </div>
    <p id="time-display"></p>


    <script>
        function startCountdown() {
            let timeLeft = 5; // Waktu awal dalam detik

            function updateDisplay() {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                document.getElementById("time-display").textContent =
                    "Time: " + minutes.toString().padStart(2, '0') + ":" + seconds.toString().padStart(2, '0');

                if (timeLeft > 0) {
                    timeLeft--; // Kurangi waktu setiap detik
                } else {
                    timeLeft = 5; // Reset ke 5 detik
                }
            }

            updateDisplay(); // Panggil pertama kali untuk menghindari delay
            setInterval(updateDisplay, 1000); // Jalankan setiap 1 detik
        }

        window.onload = startCountdown;
    </script>

</body>
</html>
