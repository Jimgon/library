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
     * Hide the database column so only the accessor is used
     */
    protected $hidden = ['available_copies'];

    /**
     * Force the accessor to be used for available_copies instead of the database column
     */
    protected $appends = ['available_copies'];

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

    /**
     * Get available copies - calculated dynamically from total, borrowed, and lost
     */
    public function getAvailableCopiesAttribute()
    {
        $totalCopies = (int) ($this->attributes['copies'] ?? 0);
        
        // Count borrowed copies
        if ($this->relationLoaded('borrows')) {
            // Use the eager-loaded relation
            $borrowedCopies = 0;
            foreach ($this->borrows as $borrow) {
                if (is_null($borrow->returned_at)) {
                    $borrowedCopies++;
                }
            }
        } else {
            // Query if not eager-loaded
            $borrowedCopies = (int) $this->borrows()
                ->whereNull('returned_at')
                ->count();
        }
        
        $lostDamagedCount = count($this->lost_control_numbers ?? []);
        
        return max(0, $totalCopies - $borrowedCopies - $lostDamagedCount);
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
