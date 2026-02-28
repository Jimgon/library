<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: name is already nullable in the base users table migration.
    }

    public function down(): void
    {
        // No-op
    }
};
