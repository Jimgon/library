<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'remark',
        'notes',
        'role',
        'origin',
        'copy_number',
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

    // Accessor to get the borrower (User or Teacher) directly
    public function getBorrower()
    {
        if ($this->role === 'teacher') {
            return Teacher::find($this->user_id);
        }
        return User::find($this->user_id);
    }
}
