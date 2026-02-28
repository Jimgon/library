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
        Schema::create('penalty_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('borrow_days_allowed')->default(7); // How many days allowed
            $table->decimal('penalty_per_day', 8, 2)->default(5); // Penalty per day
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_settings');
    }
};
