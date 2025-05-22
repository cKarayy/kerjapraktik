<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Scan QR Code - Absensi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        #reader {
            width: 320px;
            margin: 20px auto;
        }

        #result {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            color: green;
        }

        #photo-form {
            display: none;
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        #photo-form input, #photo-form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #218838;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Scan QR Code untuk Absensi</h2>

    <div id="reader"></div>
    <div id="result"></div>

    <form id="photo-form" method="POST" action="{{ route('absensi.submit') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_karyawan" id="form-id_karyawan">
        <input type="hidden" name="id_shift" id="form-id_shift">
        <input type="hidden" name="latitude" id="form-latitude">
        <input type="hidden" name="longitude" id="form-longitude">

        <label for="foto">Ambil Foto Anda (kamera belakang):</label>
        <input type="file" name="foto" id="foto" accept="image/*" capture="environment" required>

        <button type="submit">Kirim Absensi</button>
    </form>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius bumi (dalam meter)
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear().then(() => {
                document.getElementById('result').innerText = "QR Code terbaca: " + decodedText;

                const parts = decodedText.split("|");
                if (parts.length !== 2) {
                    alert("Format QR Code tidak valid!");
                    return;
                }

                const id_karyawan = parts[0];
                const id_shift = parts[1];

                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;

                    fetch("{{ route('absensi.validate-location') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            id_shift: id_shift,
                            user_lat: userLat,
                            user_lon: userLon
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            // Isi form dan tampilkan
                            document.getElementById("form-id_karyawan").value = id_karyawan;
                            document.getElementById("form-id_shift").value = id_shift;
                            document.getElementById("form-latitude").value = userLat;
                            document.getElementById("form-longitude").value = userLon;
                            document.getElementById("photo-form").style.display = "block";
                        } else {
                            alert("Anda tidak berada dalam radius 1 meter dari lokasi QR Code.");
                        }
                    })
                    .catch(error => {
                        alert("Terjadi kesalahan saat validasi lokasi: " + error);
                    });
                }, function(error) {
                    alert("Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.");
                });
            }).catch(err => {
                console.error('Scanner clear error:', err);
            });
        }

        const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
