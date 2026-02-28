<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DistributedBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'publisher', 'isbn', 'category', 'copies', 'available_copies', 'status',
        'edition','pages','source_of_funds','cost_price','year','condition'
    ];

    protected $dates = ['deleted_at'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'book_id', 'id');
    }
}
