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
        Schema::create('lost_damaged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_id')->constrained('borrows')->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable(); // References either users or teachers based on borrow.role
            $table->enum('type', ['lost', 'damaged']);
            $table->string('copy_number')->nullable();
            $table->text('remarks')->nullable();
            $table->string('penalty')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('active'); // active, returned, replaced
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_damaged_items');
    }
};
