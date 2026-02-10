<?php

namespace Tests\Feature;

use App\Livewire\Poems\Show;
use App\Models\Poem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PoemShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_poem_is_displayed(): void
    {
        $poem = Poem::factory()->published()->create();

        Livewire::test(Show::class, ['poem' => $poem])
            ->assertStatus(200)
            ->assertSee($poem->title);
    }

    public function test_draft_poem_returns_404(): void
    {
        $poem = Poem::factory()->draft()->create();

        Livewire::test(Show::class, ['poem' => $poem])
            ->assertStatus(404);
    }
}
