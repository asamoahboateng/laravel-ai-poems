<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_displayed(): void
    {
        Livewire::test(Register::class)
            ->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_registration_requires_name(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', '')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors('data.name');
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'taken@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors('data.email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors('data.password');
    }

    public function test_authenticated_user_is_redirected_from_register_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Register::class)
            ->assertRedirect(route('admin.dashboard'));
    }
}
