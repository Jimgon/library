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
        'copy_years',
        'copy_conditions',
        'lost_control_numbers',
    ];

    protected $casts = [
        'control_numbers' => 'array',
        'copy_years' => 'array',
        'copy_status' => 'array',
        'copy_conditions' => 'array',
        'lost_control_numbers' => 'array',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Ensure copy_years is always an array
     */
    public function getCopyYearsAttribute($value)
    {
        if (is_null($value) || (is_array($value) && empty($value))) {
            return [];
        }
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

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
    $totalCopies = $this->copies ?? 0;
    $borrowedCopies = $this->borrowed_copies ?? 0;
    $lostDamagedCount = count($this->lost_control_numbers ?? []);
    
    return $totalCopies - $borrowedCopies - $lostDamagedCount;
    }

    /**
     * Mark a control number as lost/unavailable
     */
    public function markControlNumberAsLost($controlNumber)
    {
        $lostNumbers = $this->lost_control_numbers ?? [];
        if (!in_array($controlNumber, $lostNumbers)) {
            $lostNumbers[] = $controlNumber;
            $this->update(['lost_control_numbers' => $lostNumbers]);
        }
    }

    /**
     * Check if a control number is marked as lost
     */
    public function isControlNumberLost($controlNumber)
    {
        $lostNumbers = $this->lost_control_numbers ?? [];
        return in_array($controlNumber, $lostNumbers);
    }

    /**
     * Get available control numbers (not borrowed and not lost)
     */
    public function getAvailableControlNumbers()
    {
        if (!$this->control_numbers || !is_array($this->control_numbers)) {
            return [];
        }

        $borrowedCtrls = $this->borrows()
            ->whereNull('returned_at')
            ->pluck('copy_number')
            ->toArray();

        $lostCtrls = $this->lost_control_numbers ?? [];

        return array_filter($this->control_numbers, function ($ctrl) use ($borrowedCtrls, $lostCtrls) {
            return !in_array($ctrl, $borrowedCtrls) && !in_array($ctrl, $lostCtrls);
        });
    }
}
