<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'isbn', 'publisher', 'year', 'ctrl_number', 'condition'
    ];
}
