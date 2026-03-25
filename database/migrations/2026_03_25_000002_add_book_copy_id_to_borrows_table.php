<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Add book_copy_id foreign key
            $table->foreignId('book_copy_id')->nullable()->after('book_id')->constrained('book_copies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['book_copy_id']);
            $table->dropColumn('book_copy_id');
        });
    }
};
