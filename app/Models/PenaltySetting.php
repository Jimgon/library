<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltySetting extends Model
{
    protected $fillable = [
        'borrow_days_allowed',
        'penalty_per_day'
    ];
}
