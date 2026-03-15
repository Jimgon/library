<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate copy_number for borrows that have null values
        // Use the book's call_number as reference, or generate a default format
        DB::table('borrows')
            ->whereNull('copy_number')
            ->update([
                'copy_number' => DB::raw("COALESCE((SELECT call_number FROM books WHERE books.id = borrows.book_id), CONCAT('BK-', book_id))")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: revert to NULL if needed
        // DB::table('borrows')->whereNotNull('copy_number')->update(['copy_number' => null]);
    }
};
