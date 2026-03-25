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

    // ===== RELATIONSHIPS =====
    public function copies()
    {
        return $this->hasMany(BookCopy::class, 'book_id', 'id');
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
     * Get the count of available copies (only books that can currently be borrowed)
     * This is a consistent accessor that always uses BookCopy records as source of truth
     */
    public function getAvailableCopiesAttribute()
    {
        // Always use BookCopy records as source of truth (new normalized structure)
        return $this->copies()
            ->where('status', 'available')
            ->where('is_lost_damaged', false)
            ->count();
    }

    /**
     * Get the total count of ALL copies regardless of status
     * This includes available, borrowed, lost, damaged, found, repaired, etc.
     */
    public function getTotalCopiesAttribute()
    {
        // Use BookCopy records count as the source of truth
        $totalFromBookCopy = $this->copies()->count();
        
        // If BookCopy records exist, use them; otherwise fallback to the copies field
        if ($totalFromBookCopy > 0) {
            return $totalFromBookCopy;
        }
        
        // Fallback for records that don't have BookCopy entries yet
        return $this->copies ?? 0;
    }

    /**
     * Mark a control number as lost/unavailable
     */
    public function markControlNumberAsLost($controlNumber)
    {
        // Update BookCopy if it exists (new normalized structure)
        $bookCopy = $this->getCopyByControlNumber($controlNumber);
        if ($bookCopy) {
            $bookCopy->markAsLost();
        }
        
        // Also update the legacy JSON array for backward compatibility
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
        // Use new BookCopy structure if available
        if ($this->copies()->exists()) {
            return $this->copies()
                ->where('status', 'available')
                ->where('is_lost_damaged', false)
                ->pluck('control_number')
                ->toArray();
        }
        
        // Fallback to old array-based structure
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

    // ===== NEW METHODS FOR NORMALIZED STRUCTURE =====
    
    /**
     * Get a specific copy by control number
     */
    public function getCopyByControlNumber($controlNumber)
    {
        return $this->copies()
            ->where('control_number', $controlNumber)
            ->first();
    }

    /**
     * Create a new copy for this book
     */
    public function addCopy($controlNumber, $acquisitionYear = null, $condition = null)
    {
        return $this->copies()->create([
            'control_number' => $controlNumber,
            'acquisition_year' => $acquisitionYear,
            'status' => 'available',
            'condition' => $condition,
            'is_lost_damaged' => false,
        ]);
    }

    /**
     * Get all available copies (normalized structure)
     */
    public function getAvailableCopies()
    {
        return $this->copies()
            ->where('status', 'available')
            ->where('is_lost_damaged', false)
            ->get();
    }

    /**
     * Get all borrowed copies
     */
    public function getBorrowedCopies()
    {
        return $this->copies()
            ->where('status', 'borrowed')
            ->get();
    }

    /**
     * Get all lost or damaged copies
     */
    public function getLostOrDamagedCopies()
    {
        return $this->copies()
            ->where('is_lost_damaged', true)
            ->get();
    }

    /**
     * Mark a copy as lost or damaged
     */
    public function markCopyAsLostOrDamaged($controlNumber, $type = 'lost')
    {
        $copy = $this->getCopyByControlNumber($controlNumber);
        if ($copy) {
            $status = ($type === 'damaged') ? 'damaged' : 'lost';
            $copy->update([
                'status' => $status,
                'is_lost_damaged' => true,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Mark a copy as available (restore from lost/damaged)
     */
    public function restoreCopy($controlNumber)
    {
        $copy = $this->getCopyByControlNumber($controlNumber);
        if ($copy) {
            $copy->markAsAvailable();
            return true;
        }
        return false;
    }

    /**
     * Get total number of copies (normalized) - ALL copies regardless of status
     * Matches the total_copies accessor
     */
    public function getTotalCopiesCount()
    {
        return $this->total_copies;
    }

    /**
     * Get count of available copies only
     * Matches the available_copies accessor
     */
    public function getAvailableCopiesCount()
    {
        return $this->available_copies;
    }

    /**
     * Get breakdown of copy statuses for detailed inventory reporting
     */
    public function getCopyStatusBreakdown()
    {
        return [
            'total' => $this->total_copies,
            'available' => $this->copies()->where('status', 'available')->where('is_lost_damaged', false)->count(),
            'borrowed' => $this->copies()->where('status', 'borrowed')->where('is_lost_damaged', false)->count(),
            'lost' => $this->copies()->where('status', 'lost')->where('is_lost_damaged', true)->count(),
            'damaged' => $this->copies()->where('status', 'damaged')->where('is_lost_damaged', true)->count(),
            'found' => $this->copies()->where('status', 'found')->where('is_lost_damaged', false)->count(),
            'repaired' => $this->copies()->where('status', 'repaired')->where('is_lost_damaged', false)->count(),
        ];
    }
}
