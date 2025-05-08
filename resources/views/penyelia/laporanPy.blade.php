<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/admin/laporan.css') }}">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h2>LAPORAN KEHADIRAN PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="divider"></div>

    <form method="GET" action="{{ route('penyelia.laporanPy') }}">
        <div class="filter-container">
            <div class="keterangan-container">
                <span>KETERANGAN</span>
                <div class="dropdown-container" onclick="toggleDropdown()">
                    <img src="{{ asset('images/dd.png') }}" alt="Dropdown">
                    <div class="dropdown" id="dropdownMenu">
                        <div class="option" onclick="selectOption(this)">IZIN</div>
                        <div class="option" onclick="selectOption(this)">CUTI</div>
                    </div>
                </div>

                <label class="custom-radio">
                    <input type="radio" name="shift" value="pagi" {{ old('shift') == 'pagi' ? 'checked' : '' }}>
                    <span class="checkmark"></span> PAGI
                </label>
                <label class="custom-radio">
                    <input type="radio" name="shift" value="middle" {{ old('shift') == 'middle' ? 'checked' : '' }}>
                    <span class="checkmark"></span> MIDDLE
                </label>
                <label class="custom-radio">
                    <input type="radio" name="shift" value="malam" {{ old('shift') == 'malam' ? 'checked' : '' }}>
                    <span class="checkmark"></span> MALAM
                </label>
            </div>

            <div class="bulan-filter">
                <label for="bulan">Bulan:</label>
                <input type="month" name="bulan" id="bulan" class="" value="{{ old('bulan', date('Y-m')) }}" required>
                <button class="btn-container">TAMPILKAN LAPORAN</button>
            </div>
        </div>
    </form>

    <!-- TABEL KEHADIRAN -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>NAMA LENGKAP</th>
                    <th>KEHADIRAN</th>
                    <th>KETERANGAN</th>
                    <th>KETERLAMBATAN</th>
                    <th>STATUS PERSETUJUAN</th> <!-- Kolom untuk status persetujuan -->
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>{{ $item->karyawan->nama }}</td> <!-- Menampilkan nama pegawai -->
                        <td>{{ $item->kehadiran ?? 'Cuti/Izin' }}</td>
                        <td>{{ $item->shift ?? $item->alasan }}</td>
                        <td>{{ $item->lateness ?? '-' }}</td>
                        <td>
                            @if($item instanceof \App\Models\Cuti || $item instanceof \App\Models\Izin)
                                <form action="{{ route('penyelia.persetujuan', $item->id) }}" method="POST">
                                    @csrf
                                    <select name="status_persetujuan" onchange="this.form.submit()">
                                        <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Disetujui" {{ $item->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="Ditolak" {{ $item->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    function selectOption(selectedElement) {
        // Reset semua opsi ke warna default
        document.querySelectorAll(".option").forEach(option => {
            option.classList.remove("selected");
        });

        // Tambahkan class "selected" ke elemen yang dipilih
        selectedElement.classList.add("selected");

        // Tutup dropdown setelah memilih
        document.getElementById("dropdownMenu").classList.remove("show");
    }

    // Tutup dropdown jika klik di luar area dropdown
    document.addEventListener("click", function (event) {
        const dropdownBox = document.querySelector(".dropdown-container");
        const dropdownMenu = document.getElementById("dropdownMenu");

        if (!dropdownBox.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });
</script>

</body>
</html>
