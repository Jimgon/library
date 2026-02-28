<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::whereNull('deleted_at');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gender', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        $teachers = $query->with('borrows.book')->orderBy('name')->paginate(10);
        return view('users.teachers', compact('teachers'));
    }

    public function create()
    {
        return view('users.create_teacher');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'gender'      => 'required|string',
            'address'     => 'required|string',
            'phone_number'=> 'required|string|max:20',
            'email'       => 'required|email|unique:teachers,email',
        ]);
        $teacher = Teacher::create($request->only(['name','gender','address','phone_number','email']));
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Added Teacher',
            'details' => "Teacher '{$teacher->name}' added by " . Auth::user()->name,
        ]);
        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function edit(Teacher $teacher)
    {
        return view('users.edit_teacher', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'gender'      => 'required|string',
            'address'     => 'required|string',
            'phone_number'=> 'required|string|max:20',
            'email'       => 'required|email|unique:teachers,email,' . $teacher->id,
        ]);
        $teacher->update($request->only(['name','gender','address','phone_number','email']));
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Teacher',
            'details' => "Teacher '{$teacher->name}' updated by " . Auth::user()->name,
        ]);
        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function updateRemark(Request $request, Teacher $teacher)
    {
        $request->validate([
            'remark' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
        ]);

        $remark = $request->input('remark');
        if ($request->filled('comment')) {
            $remark = 'Special Notes: ' . $request->input('comment');
        }

        $teacher->update(['remark' => $remark]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Teacher Remark',
            'details' => "Remark for teacher '{$teacher->name}' updated to '{$remark}' by " . Auth::user()->name,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Remark updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;
        $teacher->delete();
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Deleted Teacher',
            'details' => "Teacher '{$name}' deleted by " . Auth::user()->name,
        ]);
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    // Import teachers from CSV
    public function importForm()
    {
        return view('users.teachers_import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $imported = 0;
        $errors = [];

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            // Skip header row
            $header = fgetcsv($handle, 1000, ',');
            
            $rowNum = 2; // Start from row 2 (after header)
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                try {
                    if (count($row) < 5) {
                        $errors[] = "Row {$rowNum}: Insufficient columns.";
                        $rowNum++;
                        continue;
                    }

                    $name = trim($row[0] ?? '');
                    $email = trim($row[1] ?? '');
                    $gender = trim($row[2] ?? '');
                    $address = trim($row[3] ?? '');
                    $phone_number = trim($row[4] ?? '');

                    if (!$name || !$email) {
                        $errors[] = "Row {$rowNum}: Name and Email are required.";
                        $rowNum++;
                        continue;
                    }

                    // Check if teacher already exists
                    if (Teacher::where('email', $email)->exists()) {
                        $errors[] = "Row {$rowNum}: Email '{$email}' already exists.";
                        $rowNum++;
                        continue;
                    }

                    Teacher::create([
                        'name' => $name,
                        'email' => $email,
                        'gender' => $gender,
                        'address' => $address,
                        'phone_number' => $phone_number,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNum}: " . $e->getMessage();
                }
                $rowNum++;
            }
            fclose($handle);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Imported Teachers',
            'details' => "Imported {$imported} teacher(s) by " . Auth::user()->name,
        ]);

        session()->flash('import_summary', [
            'imported' => $imported,
            'errors' => $errors,
        ]);

        return redirect()->route('teachers.index');
    }
}

