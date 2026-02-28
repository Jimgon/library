<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\SystemUser;
use App\Models\Teacher;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class UtilitiesController extends Controller
{
    // List available backup files
    public function listBackups()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];
        if (file_exists($backupDir)) {
            $files = glob($backupDir . '/*.zip');
            usort($files, function($a, $b) { return filemtime($b) - filemtime($a); }); // newest first
            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }
        return view('utilities.backups', compact('backups'));
    }

    // Download a specific backup file
    public function downloadBackup($filename)
    {
        $backupDir = storage_path('app/backups');
        $safeName = basename($filename);
        $file = $backupDir . '/' . $safeName;
        if (!file_exists($file)) {
            abort(404, 'Backup file not found.');
        }
        return response()->download($file);
    }
    // Utilities Dashboard
    public function index()
    {
        return view('utilities.index');
    }

    // Logs Page
    public function logs()
    {
        $logs = ActivityLog::latest()->paginate(20);
        return view('utilities.activity-log', compact('logs'));
    }

    // Archive Page
    public function archive()
    {
        $books = Book::onlyTrashed()->paginate(10);
        $students = User::onlyTrashed()->paginate(10);
        $staff = SystemUser::onlyTrashed()->paginate(10);
        $teachers = Teacher::onlyTrashed()->paginate(10);

        return view('utilities.archive', compact('books', 'students', 'staff', 'teachers'));
    }

    // Restore single item
    public function restore($model, $id)
    {
        $class = $this->getModel($model);
        $item = $class::onlyTrashed()->find($id);

        if (!$item) {
            return back()->with('error', 'Item not found or not deleted.');
        }

        // Get item details before restoring
        $details = $this->getItemDetails($model, $item);
        
        $item->restore();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Restored ' . ucfirst($model),
            'target_type' => $model,
            'target_id' => $id,
            'details' => $details
        ]);

        return back()->with('success', ucfirst($model) . " restored successfully.");
    }

    // Restore all
    public function restoreAll($model)
    {
        $class = $this->getModel($model);
        $items = $class::onlyTrashed()->get();

        foreach ($items as $item) {
            $id = $item->id ?? $item->id;
            $details = $this->getItemDetails($model, $item);
            
            $item->restore();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Restored ' . ucfirst($model),
                'target_type' => $model,
                'target_id' => $id,
                'details' => $details
            ]);
        }

        return back()->with('success', "All {$model}s restored successfully.");
    }

    // Delete permanently (single)
    public function delete($model, $id)
    {
        $class = $this->getModel($model);
        $item = $class::onlyTrashed()->find($id);

        if (!$item) {
            return back()->with('error', 'Item not found.');
        }

        $idValue = $item->id ?? $item->id;
        $details = $this->getItemDetails($model, $item);
        
        $item->forceDelete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Permanently Deleted ' . ucfirst($model),
            'target_type' => $model,
            'target_id' => $idValue,
            'details' => $details
        ]);

        return back()->with('success', ucfirst($model) . " deleted permanently.");
    }

    // Delete all
    public function deleteAll($model)
    {
        $class = $this->getModel($model);
        $items = $class::onlyTrashed()->get();

        foreach ($items as $item) {
            $id = $item->id ?? $item->id;
            $details = $this->getItemDetails($model, $item);
            
            $item->forceDelete();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Permanently Deleted ' . ucfirst($model),
                'target_type' => $model,
                'target_id' => $id,
                'details' => $details
            ]);
        }

        return back()->with('success', "All {$model}s deleted permanently.");
    }

    /**
     * Database Backup Function (MySQL)
     */
    public function backup()
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $filename = "mysql_backup_" . date('Y-m-d_H-i-s');
        $sqlPath = $backupDir . '/' . $filename . '.sql';

        $passwordPart = $password !== null && $password !== '' ? "--password={$password}" : '';
        $command = "mysqldump --host={$host} --port={$port} --user={$username} {$passwordPart} {$database} > \"{$sqlPath}\"";

        exec($command);

        $zipPath = $backupDir . '/' . $filename . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            if (file_exists($sqlPath)) {
                $zip->addFile($sqlPath, basename($sqlPath));
            }
            $zip->close();
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Database Backup',
            'target_type' => 'database',
            'details' => 'Created MySQL backup: ' . $filename
        ]);

        // Do not download, just redirect back with success message
        return redirect()->route('utilities.backups')->with('success', 'Backup created successfully.');
    }

    // Map string → Model
    private function getModel($model)
    {
        $model = strtolower($model);

        if ($model === 'book') return Book::class;
        if ($model === 'student') return User::class;
        if ($model === 'teacher') return Teacher::class;
        if ($model === 'staff' || $model === 'account') return SystemUser::class;

        abort(404, 'Invalid model type.');
    }

    // Get item details for logging
    private function getItemDetails($model, $item)
    {
        $model = strtolower($model);

        if ($model === 'book') {
            return "Book: '{$item->title}' by {$item->author} (ISBN: {$item->isbn})";
        } elseif ($model === 'student') {
            return "Student: {$item->first_name} {$item->last_name} (Email: {$item->email})";
        } elseif ($model === 'teacher') {
            return "Teacher: {$item->name} (Email: {$item->email})";
        } elseif ($model === 'staff' || $model === 'account') {
            return "Staff/Admin: {$item->email} (Role: {$item->role})";
        }

        return "Unknown item";
    }
}

