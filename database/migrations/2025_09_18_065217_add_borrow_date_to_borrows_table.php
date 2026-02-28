<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'borrowed_at')) {
                $table->date('borrowed_at')->nullable()->after('book_id');
            }

            if (!Schema::hasColumn('borrows', 'due_date')) {
                $table->date('due_date')->nullable()->after('borrowed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'borrowed_at')) {
                $table->dropColumn('borrowed_at');
            }

            if (Schema::hasColumn('borrows', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
