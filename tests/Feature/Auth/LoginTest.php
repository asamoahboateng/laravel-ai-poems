<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed(): void
    {
        Livewire::test(Login::class)
            ->assertStatus(200);
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('data.email');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Login::class)
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}
