<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Kehadiran</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/history.css') }}">
</head>
<body>

    <div class="header">
        <a href="{{ route('pegawai.login') }}">
            <img src="{{ asset('images/logout.png') }}" alt="Logout" class="back-btn">
        </a>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

        <!-- Tabel Riwayat Kehadiran -->
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>TANGGAL, WAKTU</th>
                        <th>KEHADIRAN</th>
                        <th>KETERLAMBATAN</th>
                    </tr>
                </thead>
                <tbody id="history-body">
                    <!-- Data akan ditambahkan secara real-time di sini -->
                </tbody>
            </table>
        </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadHistory();
        });

        // Simpan data ke localStorage agar tetap ada
        function addHistory(tanggal, waktu, status, keterlambatan) {
            let history = JSON.parse(localStorage.getItem("history")) || [];
            history.push({ tanggal, waktu, status, keterlambatan });
            localStorage.setItem("history", JSON.stringify(history));
            loadHistory();
        }

        // Memuat data history dari localStorage
        function loadHistory() {
            let history = JSON.parse(localStorage.getItem("history")) || [];
            let tbody = document.getElementById("history-body");
            tbody.innerHTML = "";

            history.forEach(item => {
                let row = `<tr>
                    <td>${item.tanggal}, ${item.waktu}</td>
                    <td>${item.status}</td>
                    <td>${item.keterlambatan}</td>
                </tr>`;
                tbody.innerHTML += row;
            });
        }

    </script>
</body>
</html>
