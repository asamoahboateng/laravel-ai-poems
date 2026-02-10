<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Subjects\Index;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubjectCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_subject_index_is_displayed(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }

    public function test_subject_can_be_created(): void
    {
        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'name' => 'New Subject',
                'description' => 'A test subject.',
            ]);

        $this->assertDatabaseHas('subjects', ['name' => 'New Subject', 'slug' => 'new-subject']);
    }

    public function test_subject_name_must_be_unique(): void
    {
        Subject::factory()->create(['name' => 'Existing']);

        Livewire::test(Index::class)
            ->callTableAction('create', data: [
                'name' => 'Existing',
            ])
            ->assertHasTableActionErrors(['name']);
    }

    public function test_subject_can_be_updated(): void
    {
        $subject = Subject::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('edit', $subject, data: [
                'name' => 'Updated Name',
                'description' => $subject->description,
            ]);

        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Updated Name']);
    }

    public function test_subject_can_be_deleted(): void
    {
        $subject = Subject::factory()->create();

        Livewire::test(Index::class)
            ->callTableAction('delete', $subject);

        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }
}
