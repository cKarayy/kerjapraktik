<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QR;
use App\Models\shifts;
use App\Models\Absensi;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class QRController extends Controller
{
    // Menampilkan QR Code
public function showQRCode(Request $request)
{
    $shift = $request->input('shift');
    $qrCode = null;

    if ($shift) {
        $uuid = Str::uuid();  // Generate UUID untuk QR Code

        $qrData = [
            'uuid' => $uuid,
            'shift' => $shift,
            'timestamp' => now()->toDateTimeString()
        ];

        // Simpan QR code ke database
        QR::create([
            'id_admin' => auth('admin')->id(),
            'kode' => $uuid,
            'waktu_generate' => now(),
            'kehadiran' => 'belum hadir',  // Status awal QR
        ]);

        // Generate QR Code dalam format SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate(json_encode($qrData));
    }

    return view('admin.qr', [
        'shift' => $shift,
        'qrCode' => $qrCode
    ]);
}

    // Memproses pemindaian QR Code
   public function scan(Request $request)
{
    // Mendapatkan data status kehadiran dan UUID dari QR code yang discan
    $statusKehadiran = $request->input('status');
    $employeeId = $request->input('employee_id');  // ID pegawai dari hasil scan QR

    // Mencari data pegawai berdasarkan ID
    $employee = Employee::find($employeeId);

    if ($employee) {
        $adminId = auth('admin')->id();
        $shiftId = $employee->id_shift ?? null;
        $shift = Shifts::find($shiftId);

        // Cek apakah shift valid
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak valid!'], 404);
        }

        $waktuMasuk = Carbon::now(); // Waktu saat scan QR
        $waktuShiftMulai = Carbon::parse($shift->jam_masuk);  // Waktu mulai shift

        // Menghitung keterlambatan
        $keterlambatan = 0;
        if ($waktuMasuk->gt($waktuShiftMulai)) {
            $keterlambatan = $waktuMasuk->diffInMinutes($waktuShiftMulai);  // Menghitung keterlambatan
        }

        // Menyimpan data absensi
        $absensi = new Absensi();
        $absensi->id_karyawan = $employee->id_karyawan;
        $absensi->id_admin = $adminId;
        $absensi->tanggal = Carbon::today();
        $absensi->waktu_masuk = $waktuMasuk;
        $absensi->kehadiran = $statusKehadiran;
        $absensi->id_shift = $shiftId;
        $absensi->keterlambatan = $keterlambatan;
        $absensi->id_code = $request->input('id_code');  // ID QR Code yang dipindai
        $absensi->save();

        return response()->json(['message' => 'Absensi berhasil dicatat!', 'status' => 'success']);
    } else {
        return response()->json(['message' => 'Pegawai tidak ditemukan!'], 404);
    }
}

}
