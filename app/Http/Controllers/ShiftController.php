<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\shifts;

class ShiftController extends Controller
{
    public function getAll() {
        return response()->json(shifts::all());
    }

    public function store(Request $request) {
        shifts::create($request->all());
        return response()->json(['message' => 'Shift ditambahkan']);
    }

    public function updateMultiple(Request $request) {
        foreach ($request->shifts as $shiftData) {
            $shift = shifts::find($shiftData['id']);
            $shift->update([
                'nama_shift' => $shiftData['nama_shift'],
                'jam_masuk' => $shiftData['jam_masuk'],
                'jam_keluar' => $shiftData['jam_keluar'],
            ]);
        }
        return response()->json(['message' => 'Semua shift diperbarui']);
    }

    public function deleteMultiple(Request $request) {
        shifts::destroy($request->ids);
        return response()->json(['message' => 'Shift dihapus']);
    }

    public function storeMultiple(Request $request)
    {
        $data = $request->validate([
            'shifts' => 'required|array',
            'shifts.*.nama_shift' => 'required|string|max:255',
            'shifts.*.jam_masuk' => 'required',
            'shifts.*.jam_keluar' => 'required',
        ]);

        foreach ($data['shifts'] as $shift) {
            shifts::create([
                'nama_shift' => $shift['nama_shift'],
                'jam_masuk' => $shift['jam_masuk'],
                'jam_keluar' => $shift['jam_keluar'],
            ]);
        }

        return response()->json(['message' => 'Shifts berhasil disimpan.']);
    }


}
