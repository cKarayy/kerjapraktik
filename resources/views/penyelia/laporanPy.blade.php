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
                        <div class="option" onclick="selectOption(this, 'cuti')">CUTI</div>
                        <div class="option" onclick="selectOption(this, 'izin')">IZIN</div>
                    </div>
                </div>
                <input type="hidden" name="keterangan" id="keterangan" value="{{ old('keterangan', '') }}">

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

    <!-- TABEL ABSENSI -->
<div class="table-container" id="absensi-table">
    <table class="table">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>NAMA LENGKAP</th>
                <th>KEHADIRAN</th>
                <th>KETERANGAN</th>
                <th>KETERLAMBATAN</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach($absensi as $item)
                <tr>
                    <td>{{ $item->tanggal_scan }}</td>
                    <td>{{ $item->karyawan->nama_lengkap ?? '-'}}</td>
                    <td>{{ $item->kehadiran }}</td>
                    <td>{{ $item->shift }}</td>
                    <td>{{ $item->lateness }}</td>
                </tr>
            @endforeach --}}
        </tbody>
    </table>
</div>

<!-- TABEL CUTI (Hanya tampil jika 'cuti' dipilih di dropdown) -->
<div id="cuti-table" class="table-container" style="display:none;">
    <table class="table">
        <thead>
            <tr>
                <th>TANGGAL MULAI</th>
                <th>TANGGAL SELESAI</th>
                <th>NAMA LENGKAP</th>
                <th>ALASAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cuti as $item)
                <tr>
                    <td>{{ $item->tanggal_mulai }}</td>
                    <td>{{ $item->tanggal_selesai }}</td>
                    <td>{{ $item->karyawan->nama_lengkap ?? 'N/A' }}</td>
                    <td>{{ $item->alasan }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                    <td>
                        @if($item->status == 'menunggu')
                            <form method="POST" action="{{ route('penyelia.updateStatus', $item->id) }}">
                                @csrf
                                <input type="hidden" name="jenis" value="cuti">
                                <button type="submit" name="status" value="disetujui" class="btn-accept">ACCEPT</button>
                                <button type="submit" name="status" value="ditolak" class="btn-refuse">REFUSE</button>
                            </form>
                            @else
                        -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data cuti untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


<!-- TABEL IZIN (Hanya tampil jika 'izin' dipilih di dropdown) -->
<div id="izin-table" class="table-container" style="display:none;">
    <table class="table">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>NAMA LENGKAP</th>
                <th>ALASAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($izin as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->karyawan->nama_lengkap ?? 'N/A' }}</td>
                    <td>{{ $item->alasan }}</td>
                    <td>{{ ucfirst($item->status) }}</td> <!-- Menampilkan status yang diambil dari database -->
                    <td>
                        @if($item->status == 'menunggu') <!-- Tampilkan tombol hanya jika status "menunggu" -->
                            <form method="POST" action="{{ route('penyelia.updateStatus', $item->id) }}">
                                @csrf
                                <input type="hidden" name="jenis" value="izin">
                                <button type="submit" name="status" value="disetujui" class="btn-accept">ACCEPT</button>
                                <button type="submit" name="status" value="ditolak" class="btn-refuse">REFUSE</button>
                            </form>
                        @else
                        -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada data izin untuk bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

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
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    function selectOption(selectedElement, keterangan) {
        // Set nilai hidden input sesuai pilihan
        document.getElementById('keterangan').value = keterangan;

        // Reset semua opsi ke warna default
        document.querySelectorAll(".option").forEach(option => {
            option.classList.remove("selected");
        });

        // Tambahkan class "selected" ke elemen yang dipilih
        selectedElement.classList.add("selected");

        // Tutup dropdown setelah memilih
        document.getElementById("dropdownMenu").classList.remove("show");

        // Sembunyikan tabel absensi
        document.getElementById('absensi-table').style.display = 'none';

        // Tampilkan tabel berdasarkan pilihan
        if (keterangan == 'cuti') {
            document.getElementById('cuti-table').style.display = 'block';
            document.getElementById('izin-table').style.display = 'none';
        } else if (keterangan == 'izin') {
            document.getElementById('cuti-table').style.display = 'none';
            document.getElementById('izin-table').style.display = 'block';
        }
    }

    // Tutup dropdown jika klik di luar area dropdown
    document.addEventListener("click", function (event) {
        const dropdownBox = document.querySelector(".dropdown-container");
        const dropdownMenu = document.getElementById("dropdownMenu");

        if (!dropdownBox.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });

    function selectOption(selectedElement, keterangan) {
        document.getElementById('keterangan').value = keterangan;

        document.querySelectorAll(".option").forEach(option => {
            option.classList.remove("selected");
        });

        selectedElement.classList.add("selected");

        document.getElementById("dropdownMenu").classList.remove("show");

        // Menyembunyikan tabel absensi dan menampilkan tabel cuti atau izin
        document.getElementById('absensi-table').style.display = 'none';
        if (keterangan == 'cuti') {
            document.getElementById('cuti-table').style.display = 'block';
            document.getElementById('izin-table').style.display = 'none';
        } else if (keterangan == 'izin') {
            document.getElementById('cuti-table').style.display = 'none';
            document.getElementById('izin-table').style.display = 'block';
        }
    }

</script>



</body>
</html>
