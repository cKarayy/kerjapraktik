<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\QR;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function generate()
    {
        $randomCode = strtoupper(bin2hex(random_bytes(5))); // Buat kode unik
        $qrCode = QrCode::format('png')->size(300)->generate($randomCode);
        $base64Qr = base64_encode($qrCode);

        return response()->json([
            'qr' => $base64Qr,
            'code' => $randomCode,
        ]);
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

        $qr->update(['is_used' => true]);

        return response()->json(['message' => 'Absensi berhasil!']);
    }
}
