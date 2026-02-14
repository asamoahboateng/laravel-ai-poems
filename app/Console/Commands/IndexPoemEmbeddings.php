<?php

namespace App\Console\Commands;

use App\Models\Poem;
use App\Models\PoemEmbedding;
use Illuminate\Console\Command;
use Laravel\Ai\Embeddings;

class IndexPoemEmbeddings extends Command
{
    protected $signature = 'poems:embed {--fresh : Re-index all poems, even if unchanged}';

    protected $description = 'Generate and store embeddings for all published poems';

    public function handle(): int
    {
        $poems = Poem::query()
            ->published()
            ->with(['genre', 'subject'])
            ->get();

        if ($poems->isEmpty()) {
            $this->info('No published poems found to index.');

            return self::SUCCESS;
        }

        $this->info("Indexing {$poems->count()} poems...");
        $bar = $this->output->createProgressBar($poems->count());
        $bar->start();

        $indexed = 0;
        $skipped = 0;

        foreach ($poems as $poem) {
            $text = $this->buildEmbeddableText($poem);
            $hash = hash('sha256', $text);

            if (! $this->option('fresh')) {
                $existing = PoemEmbedding::where('poem_id', $poem->id)->first();

                if ($existing && $existing->content_hash === $hash) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }
            }

            $response = Embeddings::for([$text])->generate();

            PoemEmbedding::updateOrCreate(
                ['poem_id' => $poem->id],
                [
                    'embedding' => $response->first(),
                    'content_hash' => $hash,
                ]
            );

            $indexed++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Indexed: {$indexed}, Skipped (unchanged): {$skipped}");

        return self::SUCCESS;
    }

    protected function buildEmbeddableText(Poem $poem): string
    {
        $parts = [
            $poem->title,
            "by {$poem->author}",
        ];

        if ($poem->genre) {
            $parts[] = "Genre: {$poem->genre->name}";
        }

        if ($poem->subject) {
            $parts[] = "Subject: {$poem->subject->name}";
        }

        $parts[] = $poem->content;

        return implode('. ', $parts);
    }
}
