<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Laporan Kehadiran Pegawai</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; }
    </style>
</head>
<body>
    <h3>Laporan Kehadiran Pegawai Bulan {{ $bulan }}</h3>
    <table>
        <thead>
            @if($keterangan == 'cuti')
                <tr>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Nama Lengkap</th>
                    <th>Alasan</th>
                    <th>Status</th>
                </tr>
            @elseif($keterangan == 'izin')
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Lengkap</th>
                    <th>Alasan</th>
                    <th>Status</th>
                </tr>
            @else
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Lengkap</th>
                    <th>Kehadiran</th>
                    <th>Keterangan</th>
                    <th>Keterlambatan</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($data as $item)
                @if($keterangan == 'cuti')
                    <tr>
                        <td>{{ $item->tanggal_mulai }}</td>
                        <td>{{ $item->tanggal_selesai }}</td>
                        <td>{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                        <td>{{ $item->alasan }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                    </tr>
                @elseif($keterangan == 'izin')
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                        <td>{{ $item->alasan }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $item->tanggal_scan }}</td>
                        <td>{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                        <td>{{ $item->kehadiran }}</td>
                        <td>{{ $item->shift }}</td>
                        <td>{{ $item->lateness }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
