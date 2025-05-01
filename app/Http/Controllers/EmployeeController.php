<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('admin.datapegawai', compact('employees'));
    }

    public function store(Request $request)
    {
        $photos = $request->file('photo');

        foreach ($request->nama_lengkap as $i => $nama_lengkap) {
            $photoPath = null;
            if (isset($photos[$i])) {
                $photoPath = $photos[$i]->store('employees', 'public');
            }

            Employee::create([
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $request->jabatan[$i],
                'id_shift' => $request->id_shift[$i],
                'status' => $request->status[$i],
                'id_penyelia' => $request->id_penyelia[$i] ?? null,  // Jika ada penyelia
            ]);
        }

        return redirect()->route('data_py');
    }

    public function destroy(Request $request)
    {
        $employee = Employee::findOrFail($request->id);
        $employee->delete();

        return redirect()->route('data_py');
    }

    public function update(Request $request)
    {
        // Ambil data yang dikirim dari frontend
        $ids = $request->input('ids');
        $nama_lengkap = $request->input('nama_lengkap');
        $jabatan = $request->input('jabatan');
        $status = $request->input('status');
        $id_shift = $request->input('id_shift');
        $id_penyelia = $request->input('id_penyelia');

        $failedUpdates = [];  // Menyimpan error jika ada

        // Loop untuk memproses setiap data pegawai yang ingin diperbarui
        for ($i = 0; $i < count($ids); $i++) {
            $employee = Employee::find($ids[$i]);

            if ($employee) {
                // Update data karyawan
                $employee->nama_lengkap = $nama_lengkap[$i];
                $employee->jabatan = $jabatan[$i];
                $employee->status = $status[$i];
                $employee->id_shift = $id_shift[$i];
                $employee->id_penyelia = $id_penyelia[$i] ?? null;
                $employee->save();
            } else {
                // Kirimkan log ke konsol browser jika karyawan tidak ditemukan
                echo "<script>console.log('Employee with ID {$ids[$i]} not found.');</script>";

                // Simpan error untuk respons JSON
                $failedUpdates[] = "Employee with ID {$ids[$i]} not found.";
            }
        }

        // Jika ada data gagal diperbarui, kirimkan respons error
        if (!empty($failedUpdates)) {
            return response()->json(['status' => 'error', 'message' => $failedUpdates], 400);
        }

        // Jika berhasil, kirimkan respons sukses
        return response()->json(['status' => 'success', 'message' => 'Data updated successfully!']);
    }

    public function saveAll(Request $request)
    {
        Log::info('Request masuk:', $request->all());
        $message = [];

        // ADD
        if ($request->has('new_nama_lengkap')) {
            foreach ($request->new_nama_lengkap as $index => $nama_lengkap) {
                $employee = new Employee();
                $employee->nama_lengkap = $nama_lengkap;
                $employee->jabatan = $request->new_jabatan[$index] ?? '';
                $employee->status = $request->new_status[$index] ?? 'active';
                $employee->id_shift = $request->new_id_shift[$index] ?? '';
                $employee->id_penyelia = $request->new_id_penyelia[$index] ?? null;

                $employee->save();
            }

            $message[] = "Data pegawai berhasil ditambahkan.";
        }

        // EDIT
        if ($request->has('edit_ids')) {
            Log::info('Mulai proses edit data karyawan', ['edit_ids' => $request->edit_ids]);
            foreach ($request->edit_ids as $index => $id) {
                $employee = Employee::find($id);

                if ($employee) {
                    Log::info("Edit employee ID: $id", [
                        'before' => $employee->toArray(),
                        'input' => [
                            'nama_lengkap' => $request->edit_nama_lengkap[$index] ?? null,
                            'jabatan' => $request->edit_jabatan[$index] ?? null,
                            'status' => $request->edit_status[$index] ?? null,
                            'id_shift' => $request->edit_id_shift[$index] ?? null,
                            'id_penyelia' => $request->edit_id_penyelia[$index] ?? null,
                        ]
                    ]);

                    $employee->nama_lengkap = $request->edit_nama_lengkap[$index] ?? $employee->nama_lengkap;
                    $employee->jabatan = $request->edit_jabatan[$index] ?? $employee->jabatan;
                    $employee->status = $request->edit_status[$index] ?? $employee->status;
                    $employee->id_shift = $request->edit_id_shift[$index] ?? $employee->id_shift;
                    $employee->id_penyelia = $request->edit_id_penyelia[$index] ?? $employee->id_penyelia;
                    $employee->save();
                }
            }

            $message[] = "Data pegawai berhasil diubah.";
        }

        // DELETE
        if ($request->has('deleted_ids')) {
            foreach ($request->deleted_ids as $id) {
                $employee = Employee::find($id);
                if ($employee) {
                    $employee->delete();
                }
            }

            $message[] = "Data pegawai berhasil dihapus.";
        }

        return response()->json($request->all());
    }

    public function showPegawai()
    {
        $employees = Employee::all(); // atau pakai where tertentu jika perlu filter

        return view('admin.data', compact('employees'));
    }

    public function showPegawaiPenyelia()
    {
        $employees = Employee::all(); // HARUS sama supaya datanya sinkron

        return view('penyelia.data', compact('employees'));
    }
}
