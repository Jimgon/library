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
        Schema::create('lost_damaged_item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_damaged_item_id')->constrained('lost_damaged_items')->onDelete('cascade');
            $table->string('action'); // e.g., 'created', 'resolved', 'replaced', 'returned', 'pending', 'forwarded'
            $table->text('remarks')->nullable(); // Details about the action
            $table->unsignedBigInteger('created_by')->nullable(); // User who performed the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_damaged_item_histories');
    }
};
