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
    public function showQRCode(Request $request)
    {
        $namaShift = $request->input('shift'); // ini 'pagi', 'middle', atau 'malam'

        if (!$namaShift) {
            return view('admin.qr', ['shift' => null, 'qrCode' => null]);
        }

        // Cari id_shift dari nama_shift
        $shift = shifts::where('nama_shift', $namaShift)->first();

        if (!$shift) {
            return view('admin.qr', ['shift' => null, 'qrCode' => null]);
        }

        // Cari QR code berdasarkan id_shift
        $existingQR = QR::where('id_shift', $shift->id_shift)->first();

        if (!$existingQR) {
            $uuid = (string) Str::uuid();

            $existingQR = QR::create([
                'id_admin' => Auth::guard('admin')->id(),
                'kode' => $uuid,
                'id_shift' => $shift->id_shift,
            ]);
        }

        $qrData = [
            'uuid' => $existingQR->kode,
            'id_shift' => $existingQR->id_shift,
        ];

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate(json_encode($qrData));

        return view('admin.qr', ['shift' => $namaShift, 'qrCode' => $qrCode]);
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

    public function validateLocation(Request $request)
    {
        $shift = QrCode::where('id_shift', $request->id_shift)->first();

        if (!$shift) return response()->json(['valid' => false]);

        $distance = $this->calculateDistance(
            $shift->latitude, $shift->longitude,
            $request->user_lat, $request->user_lon
        );

        return response()->json(['valid' => $distance <= 1]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2) * sin($dlat/2) +
            cos($lat1) * cos($lat2) *
            sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required',
            'id_shift' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'foto' => 'required|image|max:2048',
        ]);

        $path = $request->file('foto')->store('absensi_foto', 'public');

        Absensi::create([
            'id_karyawan' => $request->id_karyawan,
            'id_shift' => $request->id_shift,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto_path' => $path,
            'waktu' => now(),
        ]);

        return redirect(to: 'pegawai.home')->with('success', 'Absensi berhasil!');
    }


}
