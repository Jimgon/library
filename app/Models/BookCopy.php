<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookCopy extends Model
{
    use HasFactory;

    protected $table = 'book_copies';

    protected $fillable = [
        'book_id',
        'control_number',
        'acquisition_year',
        'status',
        'condition',
        'is_lost_damaged',
    ];

    protected $casts = [
        'acquisition_year' => 'integer',
        'is_lost_damaged' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'book_copy_id', 'id');
    }

    // ===== SCOPES =====
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->where('is_lost_damaged', false);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeLostOrDamaged($query)
    {
        return $query->where('is_lost_damaged', true);
    }

    // ===== HELPER METHODS =====
    public function isAvailable()
    {
        return $this->status === 'available' && !$this->is_lost_damaged;
    }

    public function isBorrowed()
    {
        return $this->status === 'borrowed';
    }

    public function markAsLost()
    {
        $this->update([
            'status' => 'lost',
            'is_lost_damaged' => true,
        ]);
    }

    public function markAsDamaged()
    {
        $this->update([
            'status' => 'damaged',
            'is_lost_damaged' => true,
        ]);
    }

    public function markAsAvailable()
    {
        $this->update([
            'status' => 'available',
            'is_lost_damaged' => false,
        ]);
    }

    public function markAsBorrowed()
    {
        $this->update([
            'status' => 'borrowed',
            'is_lost_damaged' => false,
        ]);
    }

    public function getActiveBorrow()
    {
        return $this->borrows()
                    ->whereNull('returned_at')
                    ->first();
    }
}
