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
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>TANGGAL, WAKTU</th>
                    <th>KETERANGAN</th>
                    <th>STATUS</th>
                    <th>ALASAN / KETERLAMBATAN</th>
                </tr>
            </thead>
            <tbody>

                {{-- Izin --}}
                @foreach($izin as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>Izin </td> <!-- Menampilkan jenis Izin -->
                        <td>{{ ucfirst($item->status) }}</td> <!-- Menampilkan status -->
                        <td>{{ $item->alasan ?? '-' }}</td> <!-- Menampilkan alasan -->
                    </tr>
                @endforeach

                {{-- Cuti --}}
                @foreach($cuti as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                        <td>Cuti</td> <!-- Menampilkan jenis Cuti -->
                        <td>{{ ucfirst($item->status) }}</td> <!-- Menampilkan status -->
                        <td>{{ $item->alasan ?? '-' }}</td> <!-- Menampilkan alasan -->
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</body>
</html>
