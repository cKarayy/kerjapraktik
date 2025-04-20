<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\QR;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function showQRCode(Request $request)
    {
        $shift = $request->input('shift', 'pagi'); // Default to 'pagi' if not set
        return view('qrcode', compact('shift'));
    }

    public function generateByShift($shift)
    {
        $validShifts = ['pagi', 'middle', 'malam'];
        if (!in_array($shift, $validShifts)) {
            return response()->json(['error' => 'Shift tidak valid'], 400);
        }

        $uuid = Str::uuid();
        $data = [
            'uuid' => $uuid,
            'shift' => $shift,
            'timestamp' => now()->toDateTimeString(),
        ];

        $qrContent = json_encode($data);

        // Generate QR Code in PNG format and encode it in base64
        $qrBase64 = base64_encode(QrCode::format('png')->size(300)->generate($qrContent));

        // Insert the generated QR code into the database
        DB::table('qr_codes')->insert([
            'uuid' => $uuid,
            'code' => $qrContent,
            'shift' => $shift,
            'valid_until' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['qr' => $qrBase64]);
    }

    public function scan(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $qr = QR::where('code', $request->code)->first();

        if (!$qr) {
            return response()->json(['message' => 'QR Code tidak ditemukan.'], 404);
        }

        if ($qr->is_used) {
            return response()->json(['message' => 'QR Code sudah digunakan.'], 403);
        }

        if (now()->greaterThan($qr->valid_until)) {
            return response()->json(['message' => 'QR Code sudah kedaluwarsa.'], 403);
        }

        $qr->update(['is_used' => true, 'scanned_at' => now()]);

        return response()->json(['message' => 'Absensi berhasil!']);
    }
}
