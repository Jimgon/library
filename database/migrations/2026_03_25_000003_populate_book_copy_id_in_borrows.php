<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Borrow;
use App\Models\BookCopy;

return new class extends Migration
{
    public function up(): void
    {
        // Populate book_copy_id in borrows by matching control_number
        $borrows = Borrow::all();
        
        foreach ($borrows as $borrow) {
            // The copy_number field actually contains the control_number
            if ($borrow->copy_number) {
                $bookCopy = BookCopy::where('book_id', $borrow->book_id)
                                     ->where('control_number', $borrow->copy_number)
                                     ->first();
                
                if ($bookCopy) {
                    $borrow->update(['book_copy_id' => $bookCopy->id]);
                }
            }
        }
    }

    public function down(): void
    {
        // Clear book_copy_id
        DB::table('borrows')->update(['book_copy_id' => null]);
    }
};
