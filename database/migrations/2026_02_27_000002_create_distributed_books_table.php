<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributed_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->string('category')->nullable();
            $table->integer('copies')->default(0);
            $table->integer('available_copies')->default(0);
            $table->string('status')->default('for_distribute');
            $table->string('edition')->nullable();
            $table->integer('pages')->nullable();
            $table->string('source_of_funds')->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('year')->nullable();
            $table->string('condition')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributed_books');
    }
};
