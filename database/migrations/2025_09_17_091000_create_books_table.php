<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('isbn')->unique();
            $table->string('category')->nullable();
            $table->integer('copies')->default(1);
            $table->integer('available_copies')->default(0);
            $table->string('status')->default('available');
            $table->string('edition')->nullable();
            $table->integer('pages')->nullable();
            $table->string('source_of_funds')->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('published_year')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('acquisition_type')->nullable();
            $table->string('condition')->nullable();
            $table->string('copy_status')->nullable();
            $table->string('call_number')->nullable();
            $table->string('dewey_decimal')->nullable();
            $table->string('cutter_number')->nullable();
            $table->json('control_numbers')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
