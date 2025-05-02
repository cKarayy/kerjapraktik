<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\QR;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    // Menampilkan QR code berdasarkan shift yang dipilih
    public function showQRCode(Request $request)
    {
        $shift = $request->input('shift');  // Tidak ada nilai default, akan null jika tidak ada shift yang dipilih
        return view('qrcode', compact('shift'));
    }

    // Generate QR Code berdasarkan shift
    public function generateByShift($shift)
    {
        // Validasi shift yang diperbolehkan
        $validShifts = ['pagi', 'middle', 'malam'];
        if (!in_array($shift, $validShifts)) {
            return response()->json(['error' => 'Shift tidak valid'], 400);
        }

        // Ambil admin yang terkait untuk memasukkan id_admin
        $admin = Admin::first();  // Bisa disesuaikan jika Anda ingin mengambil admin berdasarkan kondisi tertentu

        $uuid = Str::uuid();
        $data = [
            'uuid' => $uuid,
            'shift' => $shift,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Generate QR Code dalam format base64
        $qrBase64 = base64_encode(QrCode::format('png')->size(300)->generate(json_encode($data)));

        // Menyimpan QR Code ke database
        DB::table('qr_code')->insert([
            'id_admin' => $admin->id_admin,  // Menggunakan id_admin yang diambil dari database
            'kode' => json_encode($data),  // Menyimpan data QR code dalam bentuk JSON
            'waktu_generate' => now(),
            'kehadiran' => 'belum hadir',  // Bisa disesuaikan
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['qr' => $qrBase64]);
    }

    // Proses scan QR Code untuk absensi
    public function scan(Request $request)
    {
        $request->validate(['code' => 'required|string']);  // Validasi input QR Code

        $qr = QR::where('kode', $request->code)->first();  // Cari QR Code di database

        if (!$qr) {
            return response()->json(['message' => 'QR Code tidak ditemukan.'], 404);
        }

        // Cek apakah QR Code sudah digunakan atau kedaluwarsa
        if ($qr->kehadiran == 'sudah hadir') {
            return response()->json(['message' => 'QR Code sudah digunakan.'], 403);
        }

        if (now()->greaterThan($qr->waktu_generate->addMinutes(5))) {  // Validasi waktu kedaluwarsa 5 menit
            return response()->json(['message' => 'QR Code sudah kedaluwarsa.'], 403);
        }

        // Update status QR Code menjadi digunakan
        $qr->update(['kehadiran' => 'sudah hadir', 'updated_at' => now()]);

        return response()->json(['message' => 'Absensi berhasil!']);
    }
}
