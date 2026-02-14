<?php

namespace Tests\Feature;

use App\Models\Poem;
use App\Models\PoemEmbedding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Embeddings;
use Tests\TestCase;

class IndexPoemEmbeddingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_indexes_published_poems(): void
    {
        Embeddings::fake();

        $poem = Poem::factory()->published()->create();

        $this->artisan('poems:embed')
            ->assertSuccessful();

        $this->assertDatabaseHas('poem_embeddings', [
            'poem_id' => $poem->id,
        ]);
    }

    public function test_command_skips_draft_poems(): void
    {
        Embeddings::fake();

        $draft = Poem::factory()->draft()->create();
        $published = Poem::factory()->published()->create();

        $this->artisan('poems:embed')
            ->assertSuccessful();

        $this->assertDatabaseMissing('poem_embeddings', [
            'poem_id' => $draft->id,
        ]);
        $this->assertDatabaseHas('poem_embeddings', [
            'poem_id' => $published->id,
        ]);
    }

    public function test_command_skips_unchanged_poems(): void
    {
        Embeddings::fake();

        $poem = Poem::factory()->published()->create();

        // First run indexes the poem
        $this->artisan('poems:embed')->assertSuccessful();

        $firstEmbedding = PoemEmbedding::where('poem_id', $poem->id)->first();
        $originalUpdatedAt = $firstEmbedding->updated_at->toDateTimeString();

        // Travel forward so updated_at would differ if re-indexed
        $this->travel(1)->minutes();

        // Second run skips unchanged poem
        $this->artisan('poems:embed')
            ->assertSuccessful()
            ->expectsOutputToContain('Skipped (unchanged): 1');
    }

    public function test_fresh_flag_reindexes_all_poems(): void
    {
        Embeddings::fake();

        $poem = Poem::factory()->published()->create();

        $this->artisan('poems:embed')->assertSuccessful();

        $this->artisan('poems:embed --fresh')
            ->assertSuccessful()
            ->expectsOutputToContain('Indexed: 1');
    }

    public function test_command_handles_no_poems(): void
    {
        Embeddings::fake();

        $this->artisan('poems:embed')
            ->assertSuccessful()
            ->expectsOutputToContain('No published poems found to index.');
    }

    public function test_command_indexes_multiple_poems(): void
    {
        Embeddings::fake();

        Poem::factory()->published()->count(3)->create();

        $this->artisan('poems:embed')
            ->assertSuccessful();

        $this->assertCount(3, PoemEmbedding::all());
    }

    public function test_command_updates_changed_poem(): void
    {
        Embeddings::fake();

        $poem = Poem::factory()->published()->create(['title' => 'Original Title']);

        $this->artisan('poems:embed')->assertSuccessful();

        $originalHash = PoemEmbedding::where('poem_id', $poem->id)->first()->content_hash;

        // Change the poem content
        $poem->update(['title' => 'Updated Title']);

        $this->artisan('poems:embed')->assertSuccessful();

        $newHash = PoemEmbedding::where('poem_id', $poem->id)->first()->content_hash;

        $this->assertNotEquals($originalHash, $newHash);
    }
}
