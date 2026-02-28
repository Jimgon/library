<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_archives', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_archives');
    }
};
