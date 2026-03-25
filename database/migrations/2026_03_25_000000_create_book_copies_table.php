<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->string('control_number')->unique();
            $table->year('acquisition_year')->nullable();
            $table->string('status')->default('available'); // available, borrowed, lost, damaged
            $table->string('condition')->nullable(); // good, fair, poor, etc.
            $table->boolean('is_lost_damaged')->default(false);
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('book_id');
            $table->index('status');
            $table->index('is_lost_damaged');
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
