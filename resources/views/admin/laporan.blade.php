<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laporan Kehadiran Pegawai - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin/laporan.css') }}" />
</head>
<body>
<div class="container">
    <!-- HEADER -->
    <div class="header">
        <h2>LAPORAN KEHADIRAN PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo" />
    </div>

    <div class="divider"></div>

    <form method="GET" action="{{ route('admin.laporan') }}">
        <div class="filter-container">
            <div class="keterangan-container">
                <span>KETERANGAN</span>
                <div class="dropdown-container" onclick="toggleDropdown()">
                    <img src="{{ asset('images/dd.png') }}" alt="Dropdown" />
                    <div class="dropdown" id="dropdownMenu">
                        <div class="option" onclick="selectOption(this, 'hadir')">HADIR</div>
                        <div class="option" onclick="selectOption(this, 'cuti')">CUTI</div>
                        <div class="option" onclick="selectOption(this, 'izin')">IZIN</div>
                    </div>
                </div>
                <input type="hidden" name="keterangan" id="keterangan" value="{{ old('keterangan', request('keterangan')) }}" />

                @foreach($shifts as $shiftItem)
                    <label class="custom-radio">
                        <input type="radio" name="shift" value="{{ $shiftItem->id_shift }}"
                            {{ request('shift') == $shiftItem->id_shift ? 'checked' : '' }} />
                        <span class="checkmark"></span> {{ strtoupper($shiftItem->nama_shift) }}
                    </label>
                @endforeach
            </div>

            <div class="bulan-filter">
                <label for="bulan">Bulan:</label>
                <input type="month" name="bulan" id="bulan" value="{{ request('bulan') ?? date('Y-m') }}" required />
                <button class="btn-container">TAMPILKAN LAPORAN</button>
            </div>
        </div>
    </form>

    <!-- TABEL LAPORAN -->
    <div class="table-export-wrapper">
        <div class="table-container">
            @if(($keterangan ?? '') == '' || ($keterangan ?? '') == 'hadir')
                <table class="table">
                    <thead>
                        <tr>
                            <th>TANGGAL, WAKTU</th>
                            <th>NAMA LENGKAP</th>
                            <th>KEHADIRAN</th>
                            <th>KETERANGAN</th>
                            <th>KETERLAMBATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $item)
                        <tr>
                            <td>{{ $item->tanggal_scan }}</td>
                            <td>{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->kehadiran }}</td>
                            <td>{{ $item->shift }}</td>
                            <td>{{ $item->lateness }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">Tidak ada data absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif(($keterangan ?? '') == 'cuti')
                <table class="table">
                    <thead>
                        <tr>
                            <th>TANGGAL, WAKTU PENGAJUAN</th>
                            <th>TANGGAL MULAI</th>
                            <th>TANGGAL SELESAI</th>
                            <th>NAMA LENGKAP</th>
                            <th>ALASAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuti as $item)
                        <tr>
                            <!-- Menambahkan koma di antara tanggal dan waktu pengajuan -->
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }},
                                {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}</td>
                            <td>{{ $item->tanggal_mulai }}</td>
                            <td>{{ $item->tanggal_selesai }}</td>
                            <td>{{ $item->karyawan->nama_lengkap ?? 'N/A' }}</td>
                            <td>{{ $item->alasan }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">Tidak ada data cuti untuk bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif(($keterangan ?? '') == 'izin')
                <table class="table">
                    <thead>
                        <tr>
                            <th>TANGGAL, WAKTU PENGAJUAN</th>
                            <th>NAMA LENGKAP</th>
                            <th>ALASAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($izin as $item)
                        <tr>
                            <!-- Menambahkan koma di antara tanggal dan waktu pengajuan -->
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }},
                                {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}</td>
                            <td>{{ $item->karyawan->nama_lengkap ?? 'N/A' }}</td>
                            <td>{{ $item->alasan }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">Tidak ada data izin untuk bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Export buttons tetap ada -->
        <div class="export-buttons">
            <a href="{{ route('penyelia.laporan.export', ['format' => 'excel', 'bulan' => $bulan, 'shift' => $shiftName, 'keterangan' => $keterangan]) }}" class="export-btn" target="_blank" rel="noopener">
                <img src="{{ asset('images/excel.png') }}" alt="Excel" />
                <span>EXPORT TO EXCEL</span>
            </a>
            <a href="{{ route('penyelia.laporan.export', ['format' => 'pdf', 'bulan' => $bulan, 'shift' => $shiftName, 'keterangan' => $keterangan]) }}" class="export-btn" target="_blank" rel="noopener">
                <img src="{{ asset('images/gambar_pdf.png') }}" alt="PDF" />
                <span>EXPORT TO PDF</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const keteranganInput = document.getElementById('keterangan');
        const keterangan = keteranganInput.value.trim().toLowerCase();

        const options = document.querySelectorAll(".option");
        options.forEach(option => {
            if (option.textContent.trim().toLowerCase() === keterangan) {
                option.classList.add("selected");
            } else {
                option.classList.remove("selected");
            }
        });
    });

    function toggleDropdown() {
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    function selectOption(selectedElement, keterangan) {
        document.getElementById('keterangan').value = keterangan;

        document.querySelectorAll(".option").forEach(option => {
            option.classList.remove("selected");
        });

        selectedElement.classList.add("selected");
        document.getElementById("dropdownMenu").classList.remove("show");
    }

    document.addEventListener("click", function(event) {
        const dropdownBox = document.querySelector(".dropdown-container");
        const dropdownMenu = document.getElementById("dropdownMenu");

        if (!dropdownBox.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });
</script>
</body>
</html>
