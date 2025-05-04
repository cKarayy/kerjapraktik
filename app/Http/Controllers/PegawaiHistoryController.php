<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Izin;
use App\Models\Cuti;

class PegawaiHistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $izin = Izin::where('id_karyawan', $userId)->get();
        $cuti = Cuti::where('id_karyawan', $userId)->get();

        return view('pegawai.history', compact('izin', 'cuti'));
    }

    
}
