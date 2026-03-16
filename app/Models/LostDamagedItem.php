<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LostDamagedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_id',
        'book_id',
        'user_id',
        'type',
        'copy_number',
        'remarks',
        'penalty',
        'due_date',
        'status',
        'role',
        'origin',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function borrower()
    {
        // Intelligently determine if borrower is a Teacher or User
        // by checking which table contains the user_id
        $teacher = Teacher::find($this->user_id);
        if ($teacher) {
            return $teacher;
        }
        return User::find($this->user_id);
    }
}
