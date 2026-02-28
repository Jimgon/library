<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserController extends Controller
{
    public function teachers(Request $request)
    {
        $query = User::where('role', 'teacher');

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

        $teachers = $query->orderBy('name')->paginate(10);

        return view('users.teachers', compact('teachers'));
    }
    public function index(Request $request)
    {
        $query = User::where(function($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'teacher');
        })->with('borrows.book'); // eager-load borrows with books

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('grade_section', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $grade = $request->input('grade');
            $query->where('grade_section', 'like', "%{$grade}%");
        }

        $users = $query->orderBy('last_name')
                       ->orderBy('first_name')
                       ->paginate(10);

        // Convert dates to Carbon to prevent blanks
        $users->each(function($user) {
            $user->borrows->each(function($borrow) {
                if ($borrow->borrowed_at) $borrow->borrowed_at = Carbon::parse($borrow->borrowed_at);
                if ($borrow->due_date)    $borrow->due_date    = Carbon::parse($borrow->due_date);
            });
        });

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create_student');
    }

    public function store(Request $request)
    {
        // If creating a teacher, validate only the relevant fields
        if ($request->input('role') === 'teacher') {
            $request->validate([
                'name'        => 'required|string|max:255',
                'gender'      => 'required|string',
                'address'     => 'required|string',
                'phone_number'=> 'required|string|max:20',
                'email'       => 'required|email|unique:users,email',
            ]);
            $user = User::create([
                'name'        => $request->name,
                'gender'      => $request->gender,
                'address'     => $request->address,
                'phone_number'=> $request->phone_number,
                'email'       => $request->email,
                'role'        => 'teacher',
            ]);
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action'  => 'Added Teacher',
                'details' => "Teacher '{$user->name}' added by " . Auth::user()->name,
            ]);
            return redirect()->route('users.teachers')->with('success', 'Teacher created successfully.');
        }

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'grade'         => 'nullable|integer|between:7,12',
            'strand'        => 'nullable|string|in:ABM,GAS,STEM,HUMSS,ICT,TVL',
            'section'       => 'nullable|string|max:50',
            'grade_section' => 'nullable|string|max:255',
            'lrn'           => 'nullable|string|unique:users,lrn',
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email',
            'borrowed'      => 'nullable|integer|min:0',
        ]);

        // Combine grade, strand, section into grade_section if separate fields are provided
        $gradeSection = $request->grade_section;
        if (!$gradeSection && ($request->grade || $request->strand || $request->section)) {
            $parts = [];
            if ($request->grade) $parts[] = $request->grade;
            if ($request->strand) $parts[] = $request->strand;
            if ($request->section) $parts[] = $request->section;
            $gradeSection = implode('-', $parts);
        }

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'grade_section' => $gradeSection,
            'lrn'           => $request->lrn,
            'phone_number'  => $request->phone_number,
            'address'       => $request->address,
            'email'         => $request->email,
            'borrowed'      => $request->borrowed ?? 0,
        ]);

        // Log activity with full name
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Added Student',
            'details' => "Student '{$user->first_name} {$user->last_name}' added by " . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('borrows.book');

        $user->borrows->each(function($borrow) {
            if ($borrow->borrowed_at) $borrow->borrowed_at = Carbon::parse($borrow->borrowed_at);
            if ($borrow->due_date)    $borrow->due_date    = Carbon::parse($borrow->due_date);
        });

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit_student', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'grade'         => 'nullable|integer|between:7,12',
            'strand'        => 'nullable|string|in:ABM,GAS,STEM,HUMSS,ICT,TVL',
            'section'       => 'nullable|string|max:50',
            'grade_section' => 'nullable|string|max:255',
            'lrn'           => 'nullable|string|unique:users,lrn,' . $user->id,
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email,' . $user->id,
            'borrowed'      => 'nullable|integer|min:0',
        ]);

        // Combine grade, strand, section into grade_section if separate fields are provided
        $gradeSection = $request->grade_section;
        if (!$gradeSection && ($request->grade || $request->strand || $request->section)) {
            $parts = [];
            if ($request->grade) $parts[] = $request->grade;
            if ($request->strand) $parts[] = $request->strand;
            if ($request->section) $parts[] = $request->section;
            $gradeSection = implode('-', $parts);
        }

        $user->update([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'grade_section' => $gradeSection,
            'lrn'           => $request->lrn,
            'phone_number'  => $request->phone_number,
            'address'       => $request->address,
            'email'         => $request->email,
            'borrowed'      => $request->borrowed ?? $user->borrowed,
        ]);

        // Log activity with full name
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Student',
            'details' => "Student '{$user->first_name} {$user->last_name}' updated by " . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        $user->delete(); // Permanently deletes (no soft deletes)

        // Log activity with full name of the admin performing the delete
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Deleted Student',
            'details' => "Student '{$name}' deleted by " . Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $errors = [];

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (in_array($extension, ['xlsx', 'xls'])) {
            // Temporarily disable Excel import due to compatibility issues
            return redirect()->route('users.index')->with('error', 'Excel import is currently not available. Please use CSV format for now.');
        } else {
            // CSV
            $handle = fopen($file->getRealPath(), 'r');
            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        // Skip header row if present
        if (isset($rows[0]) && is_array($rows[0]) && count($rows[0]) >= 2) {
            array_shift($rows);
        }

        foreach ($rows as $row) {
            // Basic validation: at least name is required
            if (empty($row[0])) {
                $errors[] = "Missing required field (Name) in row: " . json_encode($row);
                continue;
            }

            // Parse combined name into first_name and last_name
            $fullName = trim($row[0]);
            $nameParts = explode(' ', $fullName, 2); // Split into max 2 parts
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? ''; // Second part as last name, empty if only one word

            // Check if LRN already exists (if provided)
            if (!empty($row[4]) && User::where('lrn', $row[4])->exists()) {
                $errors[] = "LRN {$row[4]} already exists.";
                continue;
            }

            // Combine grade, strand, and section into grade_section
            $grade = trim($row[1] ?? '');
            $strand = trim($row[2] ?? '');
            $section = trim($row[3] ?? '');
            $gradeSectionCombined = null;

            if ($grade || $strand || $section) {
                $parts = [];
                if ($grade) $parts[] = $grade;
                if ($strand) $parts[] = $strand;
                if ($section) $parts[] = $section;
                $gradeSectionCombined = implode('-', $parts);
            }

            User::create([
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'grade_section' => $gradeSectionCombined,
                'lrn'           => $row[4] ?? null,
                'phone_number'  => $row[5] ?? null,
                'address'       => $row[6] ?? null,
            ]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Imported Students',
            'details' => 'Students imported from ' . strtoupper($extension) . ' file.' . (!empty($errors) ? ' Errors: ' . implode(', ', $errors) : ''),
        ]);

        if (!empty($errors)) {
            return redirect()->route('users.index')->with('warning', 'Students imported with some errors: ' . implode(', ', $errors));
        }

        return redirect()->route('users.index')->with('success', 'Students imported successfully.');
    }

    public function updateRemark(Request $request, User $user)
    {
        $request->validate([
            'remark' => 'nullable|string',
            'comment' => 'nullable|string|max:255',
        ]);

        $remark = $request->remark;
        if ($remark === 'Special Notes' && $request->filled('comment')) {
            $remark = 'Special Notes: ' . $request->comment;
        }

        $user->update(['remark' => $remark]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated Student Remark',
            'details' => "Updated remark for student {$user->first_name} {$user->last_name} to: {$remark}",
        ]);

        return redirect()->route('users.index')->with('success', 'Remark updated successfully.');
    }

    /**
     * Bulk delete selected users (students).
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_users', []);

        Log::info('bulkDelete called', ['selected_users' => $ids, 'actor_id' => Auth::id()]);

        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->route('users.index')->with('warning', 'No students selected for deletion.');
        }

        $deleted = 0;
        $names = [];
        $users = User::whereIn('id', $ids)->get();
        foreach ($users as $user) {
            $names[] = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? $user->id);
        }
        
        // Permanently delete (no soft deletes)
        User::whereIn('id', $ids)->delete();
        $deleted = count($ids);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Bulk Deleted Students',
            'details' => "Deleted {$deleted} students: " . implode(', ', array_slice($names, 0, 10)) . (count($names) > 10 ? '...' : ''),
        ]);

        return redirect()->route('users.index')->with('success', "Deleted {$deleted} selected students.");
    }

}


