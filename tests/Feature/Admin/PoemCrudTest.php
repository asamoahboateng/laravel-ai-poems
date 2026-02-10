<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Poems\Index;
use App\Models\Genre;
use App\Models\Poem;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PoemCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_poem_index_is_displayed(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }

    public function test_poem_can_be_created(): void
    {
        $genre = Genre::factory()->create();
        $subject = Subject::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'title' => 'Test Poem Title',
                'content' => 'Test poem content here.',
                'author' => 'Test Author',
                'genre_id' => $genre->id,
                'subject_id' => $subject->id,
                'is_featured' => false,
                'published_at' => now()->toDateTimeString(),
            ]);

        $this->assertDatabaseHas('poems', ['title' => 'Test Poem Title']);
    }

    public function test_poem_creation_requires_title(): void
    {
        $genre = Genre::factory()->create();
        $subject = Subject::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'title' => '',
                'content' => 'Content',
                'genre_id' => $genre->id,
                'subject_id' => $subject->id,
            ])
            ->assertHasTableActionErrors(['title']);
    }

    public function test_poem_can_be_updated(): void
    {
        $poem = Poem::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('edit', $poem, data: [
                'title' => 'Updated Title',
                'content' => $poem->content,
                'genre_id' => $poem->genre_id,
                'subject_id' => $poem->subject_id,
            ]);

        $this->assertDatabaseHas('poems', ['id' => $poem->id, 'title' => 'Updated Title']);
    }

    public function test_poem_can_be_deleted(): void
    {
        $poem = Poem::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('delete', $poem);

        $this->assertDatabaseMissing('poems', ['id' => $poem->id]);
    }

    public function test_poem_slug_is_auto_generated(): void
    {
        $genre = Genre::factory()->create();
        $subject = Subject::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'title' => 'My Special Poem',
                'content' => 'Content',
                'genre_id' => $genre->id,
                'subject_id' => $subject->id,
                'is_featured' => false,
            ]);

        $this->assertDatabaseHas('poems', ['slug' => 'my-special-poem']);
    }
}
