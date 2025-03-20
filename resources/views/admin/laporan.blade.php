<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h2>LAPORAN KEHADIRAN PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <!-- GARIS PEMBATAS -->
    <div class="divider"></div>

    <!-- FILTER SHIFT & KETERANGAN -->
        <div class="keterangan-container">
            <span>KETERANGAN</span>
            <div class="dropdown-container" onclick="toggleDropdown()">
                <img src="{{ asset('images/dd.png') }}" alt="Dropdown">
                <div class="dropdown">
                    <div onclick="selectOption(this, 'izin')">IZIN</div>
                    <div onclick="selectOption(this, 'cuti')">CUTI</div>
                </div>
            </div>
            <div class="shift">
                <label><input type="radio" name="shift" value="pagi"> PAGI</label>
                <label><input type="radio" name="shift" value="malam"> MALAM</label>
            </div>
        </div>
    <!-- TABEL KEHADIRAN -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>NAMA LENGKAP</th>
                    <th>KEHADIRAN</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- nanti isi tabel -->
                </tr>
                <tr>
                    <!-- nanti isi tabel -->
                </tr>
                <tr>
                   <!-- nanti isi tabel -->
                </tr>
            </tbody>
        </table>

        <!-- TOMBOL EXPORT -->
        <div class="export-buttons">
            <button class="export-btn">
                <img src="{{ asset('images/excel.png') }}" alt="Excel"> 
                <span>EXPORT TO EXCEL</span>
            </button>
            <button class="export-btn">
                <img src="{{ asset('images/gambar_pdf.png') }}" alt="PDF"> 
                <span>EXPORT TO PDF</span>
            </button>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.querySelector('.dropdown').classList.toggle('show');
        }

        function selectOption(element, option) {
            document.querySelector('.dropdown-container').style.backgroundColor = option === 'izin' ? '#543310' : '#CDC497';
            document.querySelector('.dropdown').classList.remove('show');
        }

        document.addEventListener("click", function(event) {
            if (!event.target.closest(".dropdown-container")) {
                document.querySelector('.dropdown').classList.remove('show');
            }
        });
    </script>


</body>
</html>