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
        $employee = Employee::findOrFail($request->id);
        $employee->update([
            'name' => $request->name,
            'role' => $request->role,
            'status' => $request->status,
            'shift' => $request->shift,
        ]);

        return redirect()->route('data_py');
    }


    public function saveAll(Request $request)
    {
        $message = [];
        // ADD
        if ($request->has('new_names')) {
            foreach ($request->new_names as $index => $name) {
                $employee = new Employee();
                $employee->name = $name;
                $employee->role = $request->new_roles[$index];
                $employee->status = $request->new_statuses[$index];
                $employee->shift = $request->new_shifts[$index];

                // Foto
                if ($request->hasFile("new_photos.$index")) {
                    $file = $request->file("new_photos.$index");
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/photos'), $filename);
                    $employee->photo = 'uploads/photos/' . $filename;
                }

                $employee->save();
            }
        }

        // EDIT
        if ($request->has('edit_ids')) {
            foreach ($request->edit_ids as $index => $id) {
                $employee = Employee::find($id);
                if ($employee) {
                    $employee->name = $request->edit_names[$index];
                    $employee->role = $request->edit_roles[$index];
                    $employee->status = $request->edit_statuses[$index];
                    $employee->shift = $request->edit_shifts[$index];
                    $employee->save();
                }
            }
            $message[] = "Perubahan data berhasil disimpan.";
        }

        // DELETE
        if ($request->has('deleted_ids')) {
            $deletedIds = $request->input('deleted_ids');
            Employee::whereIn('id', $deletedIds)->delete();
            $message[] = count($deletedIds) . " data pegawai berhasil dihapus.";
        }

        return response()->json(['success' => true]);
    }

}
