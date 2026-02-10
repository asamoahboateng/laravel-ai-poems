<?php

namespace Tests\Feature;

use App\Livewire\Home;
use App\Models\Poem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_displayed(): void
    {
        Livewire::test(Home::class)
            ->assertStatus(200);
    }

    public function test_home_page_shows_featured_poems(): void
    {
        $featured = Poem::factory()->featured()->create();

        Livewire::test(Home::class)
            ->assertSee($featured->title);
    }

    public function test_home_page_does_not_show_draft_poems(): void
    {
        $draft = Poem::factory()->draft()->create();

        Livewire::test(Home::class)
            ->assertDontSee($draft->title);
    }
}
