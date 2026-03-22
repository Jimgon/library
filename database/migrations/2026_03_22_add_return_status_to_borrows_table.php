<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Add return_status to store different return conditions
            if (!Schema::hasColumn('borrows', 'return_status')) {
                $table->enum('return_status', [
                    'pending',              // Not yet returned
                    'returned_on_time',     // Returned On Time
                    'late_return',          // Late Return
                    'damaged_for_repair',   // Damaged / For Repair
                    'lost_and_found'        // Lost and Found
                ])->nullable()->after('returned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'return_status')) {
                $table->dropColumn('return_status');
            }
        });
    }
};
