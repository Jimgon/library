<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',       // Who performed the action
        'action',        // e.g., 'Added Student', 'Borrowed Book'
        'target_type',   // e.g., 'User', 'Book'
        'target_id',     // ID of affected record
        'details',       // Description or name of the target
    ];

    // The staff/admin who performed the action
    public function user()
    {
        return $this->belongsTo(\App\Models\SystemUser::class, 'user_id', 'id');
    }
}
