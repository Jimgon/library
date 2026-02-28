<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'status',
        'category',
        'copies',
        'publisher',
        'edition',
        'pages',
        'source_of_funds',
        'cost_price',
        'published_year',
        'purchase_price',
        'acquisition_type',
        'condition',
        'copy_status',
        'call_number',
        'available_copies',

        // ✅ NEW Dewey fields
        'dewey_decimal',
        'cutter_number',

        // control numbers stored per copy
        'control_numbers',
    ];

    protected $casts = [
        'control_numbers' => 'array',
    ];

    protected $dates = ['deleted_at'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'book_id', 'id');
    }

    public function getBorrowedCountAttribute()
    {
        return $this->borrows()
            ->whereNull('returned_at')
            ->count();
    }

    public function getAvailableCopiesAttribute()
    {
        return max(0, $this->copies - $this->borrowed_count);
    }
}
