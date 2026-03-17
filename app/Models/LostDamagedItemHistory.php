<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LostDamagedItemHistory extends Model
{
    use HasFactory;

    protected $table = 'lost_damaged_item_histories';

    protected $fillable = [
        'lost_damaged_item_id',
        'action',
        'remarks',
        'created_by',
    ];

    /**
     * Get the lost/damaged item that owns this history record.
     */
    public function lostDamagedItem()
    {
        return $this->belongsTo(LostDamagedItem::class);
    }

    /**
     * Get the user who created this history entry.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
