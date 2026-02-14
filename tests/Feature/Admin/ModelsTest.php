<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Models;
use App\Models\User;
use App\Models\UserAiSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_models_page_requires_authentication(): void
    {
        $this->get(route('admin.models'))
            ->assertRedirect('/login');
    }

    public function test_models_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Models::class)
            ->assertStatus(200)
            ->assertSee('AI Models');
    }

    public function test_can_save_model_assignments(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'openai')
            ->set('data.default_model', 'gpt-4o')
            ->set('data.default_for_images', 'gemini')
            ->set('data.default_model_for_images', 'imagen-3.0-generate-002')
            ->call('save');

        $this->assertDatabaseHas('user_ai_settings', [
            'user_id' => $this->user->id,
            'default_provider' => 'openai',
            'default_model' => 'gpt-4o',
            'default_for_images' => 'gemini',
            'default_model_for_images' => 'imagen-3.0-generate-002',
        ]);
    }

    public function test_existing_assignments_loaded_on_mount(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'default_provider' => 'anthropic',
            'default_model' => 'claude-sonnet-4-5-20250929',
            'default_for_embeddings' => 'openai',
            'default_model_for_embeddings' => 'text-embedding-3-small',
        ]);

        Livewire::test(Models::class)
            ->assertSet('data.default_provider', 'anthropic')
            ->assertSet('data.default_model', 'claude-sonnet-4-5-20250929')
            ->assertSet('data.default_for_embeddings', 'openai')
            ->assertSet('data.default_model_for_embeddings', 'text-embedding-3-small');
    }

    public function test_can_update_model_assignments(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'default_provider' => 'openai',
            'default_model' => 'gpt-4o-mini',
        ]);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'anthropic')
            ->set('data.default_model', 'claude-opus-4-6')
            ->call('save');

        $settings = $this->user->fresh()->aiSetting;
        $this->assertEquals('anthropic', $settings->default_provider);
        $this->assertEquals('claude-opus-4-6', $settings->default_model);
        $this->assertDatabaseCount('user_ai_settings', 1);
    }

    public function test_capability_models_returns_all_capabilities(): void
    {
        $capabilities = Models::capabilityModels();

        $this->assertArrayHasKey('text', $capabilities);
        $this->assertArrayHasKey('images', $capabilities);
        $this->assertArrayHasKey('audio', $capabilities);
        $this->assertArrayHasKey('transcription', $capabilities);
        $this->assertArrayHasKey('embeddings', $capabilities);
        $this->assertArrayHasKey('reranking', $capabilities);
    }
}
