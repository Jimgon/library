<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'gender',
        'address',
        'phone_number',
        'role',
        'first_name',
        'last_name',
        'grade_section',
        'lrn',
        'borrowed',
        'remark',
    ];

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    protected $dates = ['deleted_at'];
}
