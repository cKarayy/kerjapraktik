<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <style>
        @font-face {
            font-family: 'Bevan';
            src: url('../fonts/Bevan-Regular.ttf') format('truetype');
        }

        body {
            font-family: "KumbhSans";
            background-color: #fdf8e6;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        .header {
            width: 100%;
            display: flex;
            justify-content: flex-end; 
            align-items: center;
            padding: 10px;
            position: absolute;
            top: 0;
            right: 10px;
        }

        .logo {
            width: 180px;
            position: absolute;
            right: 10px;
            top: 10px;
        }

        .card {
            background-color: #0c2c64;
            width: 250px;
            height: 300px;
            border-radius: 10px;
            text-align: center;
            padding: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            text-decoration: none; /* Hilangkan garis link */
        }

        .card-content {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .image-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .background-box {
            position: absolute;
            width: 400px;
            height: 400px;
            background-color: white;
            border-radius: 10px;
            z-index: 0; /* Kotak putih di bawah */
        }

        .image-container img {
            position: relative;
            width: 80%; /* Sesuaikan ukuran gambar */
            z-index: 1; /* Gambar berada di atas kotak putih */
        }

        #time-display {
            font-family: 'Bevan', sans-serif;
            color: #4B0082;
            font-size: 20px;
            margin-top: -40px; 
        }


    </style>
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