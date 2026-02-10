<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Genres\Index;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GenreCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_genre_index_is_displayed(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }

    public function test_genre_can_be_created(): void
    {
        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'name' => 'New Genre',
                'description' => 'A test genre.',
            ]);

        $this->assertDatabaseHas('genres', ['name' => 'New Genre', 'slug' => 'new-genre']);
    }

    public function test_genre_name_must_be_unique(): void
    {
        Genre::factory()->create(['name' => 'Existing']);

        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'name' => 'Existing',
            ])
            ->assertHasTableActionErrors(['name']);
    }

    public function test_genre_can_be_updated(): void
    {
        $genre = Genre::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('edit', $genre, data: [
                'name' => 'Updated Name',
                'description' => $genre->description,
            ]);

        $this->assertDatabaseHas('genres', ['id' => $genre->id, 'name' => 'Updated Name']);
    }

    public function test_genre_can_be_deleted(): void
    {
        $genre = Genre::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('delete', $genre);

        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
