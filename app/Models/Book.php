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
        'call_number',
        'available_copies',

        // ✅ NEW Dewey fields
        'dewey_decimal',
        'cutter_number',
    ];

    protected $casts = [];

    protected $dates = ['deleted_at'];

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
        // Use BookCopy records count as the single source of truth
        return $this->copies()->count();
    }

    /**
     * Mark a control number as lost/unavailable
     */
    public function markControlNumberAsLost($controlNumber)
    {
        // Update BookCopy (source of truth)
        $bookCopy = $this->getCopyByControlNumber($controlNumber);
        if ($bookCopy) {
            $bookCopy->update([
                'status' => 'lost',
                'is_lost_damaged' => true,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Check if a control number is marked as lost
     */
    public function isControlNumberLost($controlNumber)
    {
        $bookCopy = $this->getCopyByControlNumber($controlNumber);
        if (!$bookCopy) {
            return false;
        }
        return $bookCopy->status === 'lost' && $bookCopy->is_lost_damaged;
    }

    /**
     * Get available control numbers (not borrowed and not lost)
     */
    public function getAvailableControlNumbers()
    {
        // Use BookCopy as the single source of truth
        return $this->copies()
            ->where('status', 'available')
            ->where('is_lost_damaged', false)
            ->pluck('control_number')
            ->toArray();
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

    /**
     * Migrate JSON control numbers to BookCopy records (one-time migration)
     * Call this for books that still have JSON data but no BookCopy records
     */
    public function migrateJsonToCopies()
    {
        // If book already has copies in database, skip migration
        if ($this->copies()->count() > 0) {
            return ['skipped' => true, 'message' => 'Book already has BookCopy records'];
        }

        // Get the JSON data
        $controlNumbers = $this->control_numbers ?? [];
        $copyYears = $this->copy_years ?? [];
        $copyConditions = $this->copy_conditions ?? [];
        $copyStatus = $this->copy_status ?? [];
        $lostNumbers = $this->lost_control_numbers ?? [];

        // Ensure they are arrays
        if (is_string($controlNumbers)) {
            $controlNumbers = json_decode($controlNumbers, true) ?? [];
        }
        if (is_string($copyYears)) {
            $copyYears = json_decode($copyYears, true) ?? [];
        }
        if (is_string($copyConditions)) {
            $copyConditions = json_decode($copyConditions, true) ?? [];
        }
        if (is_string($copyStatus)) {
            $copyStatus = json_decode($copyStatus, true) ?? [];
        }
        if (is_string($lostNumbers)) {
            $lostNumbers = json_decode($lostNumbers, true) ?? [];
        }

        if (empty($controlNumbers)) {
            return ['skipped' => true, 'message' => 'No control numbers to migrate'];
        }

        // Create BookCopy records from JSON data
        $created = 0;
        foreach ($controlNumbers as $index => $controlNumber) {
            // Get corresponding year, condition, status
            $year = isset($copyYears[$index]) ? $copyYears[$index] : null;
            $condition = isset($copyConditions[$index]) ? $copyConditions[$index] : null;
            $status = isset($copyStatus[$index]) ? $copyStatus[$index] : 'available';
            $isLost = in_array($controlNumber, $lostNumbers);

            // Create the BookCopy record
            BookCopy::create([
                'book_id' => $this->id,
                'control_number' => $controlNumber,
                'acquisition_year' => $year ? (int)$year : null,
                'status' => $isLost ? 'lost' : $status,
                'condition' => $condition,
                'is_lost_damaged' => $isLost,
            ]);
            $created++;
        }

        return [
            'success' => true,
            'message' => "Migrated {$created} copies from JSON to BookCopy table",
            'count' => $created
        ];
    }
}

