<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name');
            }
            if (!Schema::hasColumn('users', 'grade_section')) {
                $table->string('grade_section')->nullable();
            }
            if (!Schema::hasColumn('users', 'lrn')) {
                $table->string('lrn')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['first_name', 'last_name', 'grade_section', 'lrn', 'phone_number', 'address'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
