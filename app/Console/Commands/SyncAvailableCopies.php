<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class SyncAvailableCopies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:sync-available-copies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and sync available_copies for all books based on actual borrows and lost items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to sync available copies for all books...');

        $books = Book::all();
        $updated = 0;

        foreach ($books as $book) {
            $totalCopies = $book->copies ?? 0;
            $borrowedCopies = $book->borrows()
                ->whereNull('returned_at')
                ->count();
            $lostDamagedCount = count($book->lost_control_numbers ?? []);
            
            $calculatedAvailable = max(0, $totalCopies - $borrowedCopies - $lostDamagedCount);

            // Update the database column if it differs
            if ($book->available_copies != $calculatedAvailable) {
                $book->update(['available_copies' => $calculatedAvailable]);
                $updated++;
                
                $this->line("✓ Updated '{$book->title}': {$book->available_copies} → {$calculatedAvailable}");
            }
        }

        $this->info("✅ Sync complete! Updated {$updated} books.");
    }
}
