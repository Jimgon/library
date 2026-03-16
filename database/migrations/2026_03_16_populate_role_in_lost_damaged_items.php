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
        // Populate role field for existing lost_damaged_items by matching with borrow records
        DB::statement('
            UPDATE lost_damaged_items ldi
            SET ldi.role = (
                SELECT b.role
                FROM borrows b
                WHERE b.id = ldi.borrow_id
                LIMIT 1
            )
            WHERE ldi.role IS NULL
        ');

        // Populate origin field for existing lost_damaged_items by matching with borrow records
        DB::statement('
            UPDATE lost_damaged_items ldi
            SET ldi.origin = (
                SELECT b.origin
                FROM borrows b
                WHERE b.id = ldi.borrow_id
                LIMIT 1
            )
            WHERE ldi.origin IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset role and origin back to NULL
        DB::table('lost_damaged_items')->update([
            'role' => null,
            'origin' => null,
        ]);
    }
};
