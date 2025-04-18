<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Employee;

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

        foreach ($request->name as $i => $name) {
            $photoPath = null;
            if (isset($photos[$i])) {
                $photoPath = $photos[$i]->store('employees', 'public');
            }

            Employee::create([
                'name' => $name,
                'role' => $request->role[$i],
                'shift' => $request->shift[$i],
                'status' => $request->status[$i],
                'photo' => $photoPath,
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
 $names = $request->input('names');
 $roles = $request->input('roles');
 $statuses = $request->input('statuses');
 $shifts = $request->input('shifts');

 $failedUpdates = [];  // Menyimpan error jika ada

 // Loop untuk memproses setiap data pegawai yang ingin diperbarui
 for ($i = 0; $i < count($ids); $i++) {
     $employee = Employee::find($ids[$i]);

     if ($employee) {
         // Update data karyawan
         $employee->name = $names[$i];
         $employee->role = $roles[$i];
         $employee->status = $statuses[$i];
         $employee->shift = $shifts[$i];
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
        $message = [];

        // ADD
        if ($request->has('new_names')) {
            foreach ($request->new_names as $index => $name) {
                $employee = new Employee();
                $employee->name = $name;
                $employee->role = $request->new_roles[$index] ?? '';
                $employee->status = $request->new_statuses[$index] ?? 'active';
                $employee->shift = $request->new_shifts[$index] ?? '';

                if ($request->hasFile("new_photos.$index")) {
                    $employee->photo = $request->file("new_photos.$index")->store('employees', 'public');
                }

                $employee->save();
            }

            $message[] = "Data pegawai berhasil ditambahkan.";
        }

        // EDIT
        if ($request->has('edit_ids')) {
            foreach ($request->edit_ids as $index => $id) {
                $employee = Employee::find($id);
                if ($employee) {
                    $employee->name = $request->edit_names[$index] ?? $employee->name;
                    $employee->role = $request->edit_roles[$index] ?? $employee->role;
                    $employee->status = $request->edit_statuses[$index] ?? $employee->status;
                    $employee->shift = $request->edit_shifts[$index] ?? $employee->shift;
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

        return response()->json(['success' => true, 'message' => $message]);
    }

}
