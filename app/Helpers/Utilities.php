<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class Utilities
{
    public static function logActivity($action, $details = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(), // Currently logged-in user
            'action' => $action,
            'details' => $details,
        ]);
    }
}
