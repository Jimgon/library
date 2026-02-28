<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed a default admin account (adjust in production)
        SystemUser::firstOrCreate(
            ['email' => 'admin@library.com'],
            ['password' => Hash::make('password'), 'role' => 'admin']
        );

        // Sample students
        User::factory()->count(5)->create();

        // Sample books
        Book::factory()->count(5)->create()->each(function ($book) {
            $book->available_copies = $book->copies;
            $book->save();
        });
    }
}
