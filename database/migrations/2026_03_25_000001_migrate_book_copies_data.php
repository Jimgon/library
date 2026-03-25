<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Book;
use App\Models\BookCopy;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing copy data from JSON arrays to book_copies table
        $books = Book::all();
        
        foreach ($books as $book) {
            $controlNumbers = $book->control_numbers ?? [];
            $copyYears = $book->copy_years ?? [];
            $copyStatus = $book->copy_status ?? [];
            $copyConditions = $book->copy_conditions ?? [];
            $lostDamagedNumbers = $book->lost_control_numbers ?? [];
            
            $totalCopies = count($controlNumbers);
            
            for ($i = 0; $i < $totalCopies; $i++) {
                $controlNumber = $controlNumbers[$i] ?? null;
                
                if (is_null($controlNumber) || empty($controlNumber)) {
                    continue;
                }
                
                $isLostDamaged = in_array($controlNumber, $lostDamagedNumbers);
                $status = $copyStatus[$i] ?? 'available';
                
                // If it's marked as lost/damaged, ensure the flag is set
                if ($isLostDamaged && $status === 'available') {
                    $status = 'lost';
                }
                
                BookCopy::create([
                    'book_id' => $book->id,
                    'control_number' => $controlNumber,
                    'acquisition_year' => $copyYears[$i] ?? null,
                    'status' => $status,
                    'condition' => $copyConditions[$i] ?? null,
                    'is_lost_damaged' => $isLostDamaged,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Clear all book copies created during migration
        BookCopy::truncate();
    }
};
