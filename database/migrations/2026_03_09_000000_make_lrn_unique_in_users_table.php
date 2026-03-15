<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // LRN field is already unique in the database
        // This migration is a no-op as the constraint already exists
    }

    public function down(): void
    {
        // No-op: LRN unique constraint is managed elsewhere
        // This migration only documents that LRN is unique
    }
};
