<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    private $absensiLat = -2.9764;
    private $absensiLon = 104.755;

    // Radius maksimal dalam meter
    private $maxRadius = 1;

    public function validateLocation(Request $request)
    {
        $request->validate([
            'id_shift' => 'required',
            'user_lat' => 'required|numeric',
            'user_lon' => 'required|numeric',
        ]);

        $userLat = $request->user_lat;
        $userLon = $request->user_lon;

        $distance = $this->calculateDistance($userLat, $userLon, $this->absensiLat, $this->absensiLon);

        return response()->json([
            'valid' => $distance <= $this->maxRadius
        ]);
    }

    public function submitAbsensi(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string',
            'id_shift' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto_bukti' => 'required|image|max:2048',
        ]);

        $foto = $request->file('foto_bukti');
        $filename = 'absensi_' . $request->id_karyawan . '_' . time() . '.' . $foto->getClientOriginalExtension();
        $path = $foto->storeAs('public/absensi', $filename);

        $absensi = new Absensi();
        $absensi->id_karyawan = $request->id_karyawan;
        $absensi->id_shift = $request->id_shift;
        $absensi->latitude = $request->latitude;
        $absensi->longitude = $request->longitude;
        $absensi->foto_path = $path;
        $absensi->waktu_absensi = now();
        $absensi->id_admin = optional(Auth::guard('admin')->user())->id_admin;
        $absensi->save();

        return redirect()->back()->with('success', 'Absensi berhasil dikirim!');
    }



    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
