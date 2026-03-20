<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class DebugAvailableCopies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:debug-copies {title?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug available copies calculation for a specific book';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('title') ?? 'El filibusterismo';
        
        $book = Book::where('title', 'like', "%{$title}%")->first();
        
        if (!$book) {
            $this->error("Book not found: {$title}");
            return;
        }

        $this->line("\n📚 <info>Book Details:</info>");
        $this->line("  Title: {$book->title}");
        $this->line("  ID: {$book->id}");
        
        $this->line("\n📊 <info>Copies Data:</info>");
        $this->line("  Total Copies (DB): {$book->copies}");
        $this->line("  Available Copies (DB column): {$book->getRawOriginal('available_copies')}");
        
        $this->line("\n📋 <info>Control Numbers:</info>");
        $controlNumbers = $book->control_numbers ?? [];
        $this->line("  Total Control Numbers: " . count($controlNumbers));
        if (!empty($controlNumbers)) {
            $this->line("  Control Numbers: " . implode(', ', $controlNumbers));
        }
        
        $this->line("\n❌ <info>Lost/Damaged Control Numbers:</info>");
        $lostNumbers = $book->lost_control_numbers ?? [];
        $this->line("  Count: " . count($lostNumbers));
        if (!empty($lostNumbers)) {
            $this->line("  Lost Numbers: " . implode(', ', $lostNumbers));
        }
        
        $this->line("\n🔄 <info>Active Borrows:</info>");
        $activeBorrows = $book->borrows()
            ->whereNull('returned_at')
            ->with('user')
            ->get();
        $this->line("  Active Borrows Count: " . $activeBorrows->count());
        foreach ($activeBorrows as $borrow) {
            $this->line("    - Copy #{$borrow->copy_number} → {$borrow->user->name} (borrowed: {$borrow->borrowed_at})");
        }
        
        $this->line("\n✅ <info>Calculation:</info>");
        $totalCopies = (int) $book->copies;
        $borrowedCount = $activeBorrows->count();
        $lostCount = count($lostNumbers);
        $available = max(0, $totalCopies - $borrowedCount - $lostCount);
        
        $this->line("  Total: {$totalCopies}");
        $this->line("  - Borrowed: {$borrowedCount}");
        $this->line("  - Lost/Damaged: {$lostCount}");
        $this->line("  = Available: {$available}");
        
        $this->line("\n🔍 <info>Accessor Result:</info>");
        $this->line("  book->available_copies = " . $book->available_copies);
        
        $this->line("");
    }
}
