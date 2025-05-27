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
                        <!-- Menampilkan waktu pengajuan izin dengan format tanggal dan waktu -->
                        <td class="datetime">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i:s') }}
                        </td>
                        <td>Izin</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->alasan ?? '-' }}</td>
                    </tr>
                @endforeach

                {{-- Cuti --}}
                @foreach($cuti as $item)
                    <tr>
                        <!-- Menampilkan tanggal mulai dan selesai cuti, serta waktu pengajuan -->
                        <td class="datetime">
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }},
                            {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}
                        </td>
                        <td>Cuti</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->alasan ?? '-' }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</body>
</html>
