<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
</head>
<body>
    <div class="header">
        <form action="{{ route('admin.logout') }}" method="POST" id="logout-form" style="display: none;">
            @csrf
        </form>

        <img src="{{ asset('images/logout.png') }}" alt="Logout" class="logout" onclick="document.getElementById('logout-form').submit();">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="dashboard">
        <div class="grid-container">
            <a href="{{ route('qrcode') }}" class="card">
                <div class="card-header">QR CODE ABSENSI</div>
                <div class="card-content">
                    <div class="image-container">
                        <div class="background-box"></div>
                        <img src="{{ asset('images/qr_code.png') }}" alt="QR Code">
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.laporan') }}" class="card">
                <div class="card-header">LAPORAN ABSENSI</div>
                <div class="card-content">
                    <img src="{{ asset('images/laporan_absensi.png') }}" alt="Laporan Absensi">
                </div>
            </a>
            <a href="{{ route('data_admin') }}" class="card">
                <div class="card-header">DATA PEGAWAI</div>
                <div class="card-content">
                    <div class="image-container">
                        <div class="background-box"></div>
                        <img src="{{ asset('images/data_pegawai.png') }}" alt="Data Pegawai">
                    </div>
                </div>
            </a>
        </div>
    </div>

    <script>
            @if(session('console_log'))
        console.log("{{ session('console_log') }}");
    @endif
    </script>
</body>
</html>
