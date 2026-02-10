<?php

namespace Tests\Feature;

use App\Livewire\Poems\Index;
use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PoemBrowseTest extends TestCase
{
    use RefreshDatabase;

    public function test_browse_page_is_displayed(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }

    public function test_browse_page_shows_published_poems(): void
    {
        $poem = Poem::factory()->published()->create();

        Livewire::test(Index::class)
            ->assertSee($poem->title);
    }

    public function test_browse_page_hides_draft_poems(): void
    {
        $draft = Poem::factory()->draft()->create();

        Livewire::test(Index::class)
            ->assertDontSee($draft->title);
    }

    public function test_browse_page_filters_by_genre(): void
    {
        $genre = Genre::factory()->create();
        $poem = Poem::factory()->published()->create(['genre_id' => $genre->id]);
        $other = Poem::factory()->published()->create();

        Livewire::test(Index::class)
            ->set('genre', $genre->slug)
            ->assertSee($poem->title)
            ->assertDontSee($other->title);
    }

    public function test_browse_page_filters_by_subject(): void
    {
        $subject = Subject::factory()->create();
        $poem = Poem::factory()->published()->create(['subject_id' => $subject->id]);
        $other = Poem::factory()->published()->create();

        Livewire::test(Index::class)
            ->set('subject', $subject->slug)
            ->assertSee($poem->title)
            ->assertDontSee($other->title);
    }

    public function test_browse_page_searches_by_title(): void
    {
        $poem = Poem::factory()->published()->create(['title' => 'Twinkle Star Unique']);

        Livewire::test(Index::class)
            ->set('search', 'Twinkle Star Unique')
            ->assertSee('Twinkle Star Unique');
    }
}
