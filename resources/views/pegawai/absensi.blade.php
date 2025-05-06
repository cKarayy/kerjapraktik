<!DOCTYPE html>
<html>
<head>
    <title>Scan QR Code - Absensi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #reader {
            width: 300px;
            margin: auto;
        }
        #result {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            color: green;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Scan QR Code untuk Absensi</h2>

    <div id="reader"></div>
    <div id="result"></div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

                fetch("{{ route('absensi.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        id_karyawan: id_karyawan,
                        id_shift: id_shift
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Absensi berhasil!");
                        window.location.href = "{{ url('/home') }}";
                    } else {
                        alert("Gagal: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Terjadi kesalahan: " + error);
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
