<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrow extends Model
{
    use HasFactory;

    // Return status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_RETURNED_ON_TIME = 'returned_on_time';
    public const STATUS_LATE_RETURN = 'late_return';
    public const STATUS_DAMAGED_FOR_REPAIR = 'damaged_for_repair';
    public const STATUS_LOST_AND_FOUND = 'lost_and_found';

    // Available status values for validation
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RETURNED_ON_TIME => 'Returned (On Time)',
            self::STATUS_LATE_RETURN => 'Late Return',
            self::STATUS_DAMAGED_FOR_REPAIR => 'Damaged / For Repair',
            self::STATUS_LOST_AND_FOUND => 'Lost and Found',
        ];
    }

    // Get status badge color
    public static function getStatusColor($status)
    {
        return match($status) {
            self::STATUS_RETURNED_ON_TIME => 'success',
            self::STATUS_LATE_RETURN => 'warning',
            self::STATUS_DAMAGED_FOR_REPAIR => 'danger',
            self::STATUS_LOST_AND_FOUND => 'info',
            self::STATUS_PENDING => 'secondary',
            default => 'light',
        };
    }

    // Get status display label
    public static function getStatusLabel($status)
    {
        return self::getStatusOptions()[$status] ?? 'Unknown';
    }

    protected $fillable = [
        'user_id',
        'book_id',
        'book_copy_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'return_status',
        'remark',
        'notes',
        'role',
        'origin',
        'copy_number',
        'status',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_date' => 'date',
        'returned_at' => 'datetime',
    ];

    // Get the borrower (either User or Teacher based on the 'role' field)
    public function user()
    {
        if ($this->role === 'teacher') {
            return $this->belongsTo(Teacher::class, 'user_id');
        }
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    // Accessor to get the borrower (User or Teacher) directly
    public function getBorrower()
    {
        if ($this->role === 'teacher') {
            return Teacher::find($this->user_id);
        }
        return User::find($this->user_id);
    }
}
