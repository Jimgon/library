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
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function borrow()
    {
        return $this->belongsTo(Borrow::class, 'borrow_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
