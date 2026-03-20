<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class FixControlNumbersSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:fix-copies-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix books where copies column is out of sync with control_numbers array';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing copies column with control_numbers array...');

        $books = Book::all();
        $fixed = 0;

        foreach ($books as $book) {
            $controlNumbersCount = count($book->control_numbers ?? []);
            $currentCopies = $book->copies ?? 0;

            // If control_numbers count doesn't match copies, fix it
            if ($controlNumbersCount > 0 && $controlNumbersCount != $currentCopies) {
                $this->line("Fixing '{$book->title}':");
                $this->line("  Control Numbers: {$controlNumbersCount}");
                $this->line("  Copies (before): {$currentCopies}");
                
                // Update copies to match control numbers
                $book->update(['copies' => $controlNumbersCount]);
                $fixed++;
                
                $this->line("  Copies (after): {$controlNumbersCount}");
                $this->line("");
            }
        }

        $this->info("✅ Fixed {$fixed} books!");
    }
}
