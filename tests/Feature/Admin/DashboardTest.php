<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}
