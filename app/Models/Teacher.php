<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'gender',
        'address',
        'phone_number',
        'email',
        'remark',
    ];

    protected $dates = ['deleted_at'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'user_id', 'id');
    }
}
