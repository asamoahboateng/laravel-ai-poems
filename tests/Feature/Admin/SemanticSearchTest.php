<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SemanticSearch;
use App\Models\Poem;
use App\Models\PoemEmbedding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Embeddings;
use Livewire\Livewire;
use Tests\TestCase;

class SemanticSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_semantic_search_requires_authentication(): void
    {
        $this->get(route('admin.semantic-search'))
            ->assertRedirect(route('login'));
    }

    public function test_semantic_search_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        Livewire::test(SemanticSearch::class)
            ->assertStatus(200)
            ->assertSee('Semantic Search');
    }

    public function test_search_validates_query_is_required(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        Livewire::test(SemanticSearch::class)
            ->set('query', '')
            ->call('search')
            ->assertHasErrors(['query' => 'required']);

        Embeddings::assertNothingGenerated();
    }

    public function test_search_validates_minimum_length(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        Livewire::test(SemanticSearch::class)
            ->set('query', 'a')
            ->call('search')
            ->assertHasErrors(['query' => 'min']);
    }

    public function test_search_returns_results(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        $poem = Poem::factory()->published()->create(['title' => 'Spring Morning']);
        $embedding = Embeddings::fakeEmbedding(1536);

        PoemEmbedding::create([
            'poem_id' => $poem->id,
            'embedding' => $embedding,
            'content_hash' => hash('sha256', 'test'),
        ]);

        Livewire::test(SemanticSearch::class)
            ->set('query', 'nature and spring')
            ->call('search')
            ->assertSet('hasSearched', true)
            ->assertSee('Spring Morning');
    }

    public function test_search_shows_no_results_when_no_embeddings_exist(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        Livewire::test(SemanticSearch::class)
            ->set('query', 'poems about love')
            ->call('search')
            ->assertSet('hasSearched', true)
            ->assertSet('results', [])
            ->assertSee('No results found');
    }

    public function test_search_returns_results_sorted_by_similarity(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        $poem1 = Poem::factory()->published()->create(['title' => 'First Poem']);
        $poem2 = Poem::factory()->published()->create(['title' => 'Second Poem']);

        PoemEmbedding::create([
            'poem_id' => $poem1->id,
            'embedding' => Embeddings::fakeEmbedding(1536),
            'content_hash' => hash('sha256', 'first'),
        ]);

        PoemEmbedding::create([
            'poem_id' => $poem2->id,
            'embedding' => Embeddings::fakeEmbedding(1536),
            'content_hash' => hash('sha256', 'second'),
        ]);

        $component = Livewire::test(SemanticSearch::class)
            ->set('query', 'test search')
            ->call('search');

        $results = $component->get('results');

        $this->assertCount(2, $results);
        $this->assertGreaterThanOrEqual($results[1]['similarity'], $results[0]['similarity']);
    }

    public function test_search_limits_results_to_ten(): void
    {
        $this->actingAs($this->user);

        Embeddings::fake();

        Poem::factory()->published()->count(15)->create()->each(function (Poem $poem) {
            PoemEmbedding::create([
                'poem_id' => $poem->id,
                'embedding' => Embeddings::fakeEmbedding(1536),
                'content_hash' => hash('sha256', "poem-{$poem->id}"),
            ]);
        });

        $component = Livewire::test(SemanticSearch::class)
            ->set('query', 'test search')
            ->call('search');

        $this->assertCount(10, $component->get('results'));
    }
}
