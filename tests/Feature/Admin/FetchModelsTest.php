<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Models;
use App\Models\User;
use App\Models\UserAiSetting;
use App\Services\ProviderModelFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class FetchModelsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_fetch_models_requires_provider_selection(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Models::class)
            ->call('fetchModels', 'text', 'default_provider')
            ->assertNotified('Please select a provider first.');
    }

    public function test_fetch_models_requires_api_settings(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'openai')
            ->call('fetchModels', 'text', 'default_provider')
            ->assertNotified('Please configure your API keys in AI Settings first.');
    }

    public function test_fetch_models_from_openai(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'openai_key' => 'sk-test-key',
        ]);

        Http::fake([
            'api.openai.com/v1/models' => Http::response([
                'data' => [
                    ['id' => 'gpt-4o'],
                    ['id' => 'gpt-4o-mini'],
                    ['id' => 'gpt-3.5-turbo'],
                ],
            ]),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'openai')
            ->call('fetchModels', 'text', 'default_provider')
            ->assertNotified('Models fetched successfully!');

        $cached = ProviderModelFetcher::cached('openai', $this->user->id);
        $this->assertNotNull($cached);
        $this->assertArrayHasKey('gpt-4o', $cached);
        $this->assertArrayHasKey('gpt-4o-mini', $cached);
        $this->assertArrayHasKey('gpt-3.5-turbo', $cached);
    }

    public function test_fetch_models_from_anthropic(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'anthropic_key' => 'sk-ant-test',
        ]);

        Http::fake([
            'api.anthropic.com/v1/models' => Http::response([
                'data' => [
                    ['id' => 'claude-sonnet-4-5-20250929', 'display_name' => 'Claude Sonnet 4.5'],
                    ['id' => 'claude-haiku-4-5-20251001', 'display_name' => 'Claude Haiku 4.5'],
                ],
            ]),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'anthropic')
            ->call('fetchModels', 'text', 'default_provider')
            ->assertNotified('Models fetched successfully!');

        $cached = ProviderModelFetcher::cached('anthropic', $this->user->id);
        $this->assertNotNull($cached);
        $this->assertEquals('Claude Sonnet 4.5', $cached['claude-sonnet-4-5-20250929']);
    }

    public function test_fetch_models_from_gemini(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'gemini_key' => 'ai-test-key',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'models' => [
                    ['name' => 'models/gemini-2.0-flash', 'displayName' => 'Gemini 2.0 Flash'],
                    ['name' => 'models/gemini-2.5-pro', 'displayName' => 'Gemini 2.5 Pro'],
                ],
            ]),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_for_images', 'gemini')
            ->call('fetchModels', 'images', 'default_for_images')
            ->assertNotified('Models fetched successfully!');

        $cached = ProviderModelFetcher::cached('gemini', $this->user->id);
        $this->assertNotNull($cached);
        $this->assertEquals('Gemini 2.0 Flash', $cached['gemini-2.0-flash']);
    }

    public function test_fetch_models_from_ollama(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Http::fake([
            'localhost:11434/api/tags' => Http::response([
                'models' => [
                    ['name' => 'llama3.2:latest'],
                    ['name' => 'mistral:latest'],
                ],
            ]),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_for_embeddings', 'ollama')
            ->call('fetchModels', 'embeddings', 'default_for_embeddings')
            ->assertNotified('Models fetched successfully!');

        $cached = ProviderModelFetcher::cached('ollama', $this->user->id);
        $this->assertNotNull($cached);
        $this->assertArrayHasKey('llama3.2:latest', $cached);
    }

    public function test_fetch_models_handles_api_failure(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'openai_key' => 'sk-invalid',
        ]);

        Http::fake([
            'api.openai.com/v1/models' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_provider', 'openai')
            ->call('fetchModels', 'text', 'default_provider')
            ->assertNotified('Failed to fetch models');
    }

    public function test_cached_models_used_in_dropdown(): void
    {
        $this->actingAs($this->user);

        Cache::put("provider_models.{$this->user->id}.openai", [
            'gpt-4o' => 'gpt-4o',
            'gpt-4o-mini' => 'gpt-4o-mini',
            'gpt-4-turbo' => 'gpt-4-turbo',
        ], now()->addDay());

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'default_provider' => 'openai',
            'default_model' => 'gpt-4o',
        ]);

        $cached = ProviderModelFetcher::cached('openai', $this->user->id);
        $this->assertArrayHasKey('gpt-4-turbo', $cached);
    }

    public function test_unsupported_provider_shows_warning(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(Models::class)
            ->set('data.default_for_audio', 'eleven')
            ->call('fetchModels', 'audio', 'default_for_audio')
            ->assertNotified('Model fetching is not supported for this provider.');
    }

    public function test_provider_model_fetcher_supports_check(): void
    {
        $this->assertTrue(ProviderModelFetcher::supports('openai'));
        $this->assertTrue(ProviderModelFetcher::supports('anthropic'));
        $this->assertTrue(ProviderModelFetcher::supports('ollama'));
        $this->assertFalse(ProviderModelFetcher::supports('eleven'));
        $this->assertFalse(ProviderModelFetcher::supports('nonexistent'));
    }

    public function test_fetch_models_section_has_fetch_button(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Models::class)
            ->assertStatus(200)
            ->assertSee('AI Models');
    }

    public function test_ollama_uses_custom_url(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'ollama_url' => 'http://192.168.1.50:11434',
        ]);

        Http::fake([
            '192.168.1.50:11434/api/tags' => Http::response([
                'models' => [
                    ['name' => 'llama3.2:latest'],
                ],
            ]),
        ]);

        Livewire::test(Models::class)
            ->set('data.default_for_embeddings', 'ollama')
            ->call('fetchModels', 'embeddings', 'default_for_embeddings')
            ->assertNotified('Models fetched successfully!');
    }
}
