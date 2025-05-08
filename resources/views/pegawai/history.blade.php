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
        <a href="{{ route('pegawai.loginPg') }}">
            <img src="{{ asset('images/logout.png') }}" alt="Logout" class="back-btn">
        </a>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>TANGGAL, WAKTU</th>
                    <th>KEHADIRAN</th>
                    <th>ALASAN</th>
                    <th>STATUS</th>
                    <th>KETERLAMBATAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($izin as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>Izin</td>
                        <td>{{ $item->alasan }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>-</td>
                    </tr>
                @endforeach

                @foreach($cuti as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                        <td>Cuti</td>
                        <td>{{ $item->alasan }}</td>
                        <td>{{ ucfirst($item->status) }}</td> 
                        <td>-</td>
                    </tr>
                @endforeach

                {{-- @foreach($hadir as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>Hadir</td>
                        <td>{{ $item->alasan }}</td> <!-- Menampilkan alasan -->
                        <td>{{ ucfirst($item->status) }}</td> <!-- Menampilkan status -->
                        <td>{{ $item->keterlambatan ?? '-' }}</td> <!-- Menampilkan keterlambatan jika ada -->
                    </tr>
                @endforeach --}}
            </tbody>
        </table>
    </div>
</body>
</html>
