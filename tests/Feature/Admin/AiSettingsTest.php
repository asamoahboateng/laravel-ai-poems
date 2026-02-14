<?php

namespace Tests\Feature\Admin;

use App\Ai\Agents\ChatAssistant;
use App\Livewire\Admin\AiSettings;
use App\Livewire\Chat;
use App\Models\User;
use App\Models\UserAiSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_settings_requires_authentication(): void
    {
        $this->get(route('admin.ai-settings'))
            ->assertRedirect('/login');
    }

    public function test_settings_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        Livewire::test(AiSettings::class)
            ->assertStatus(200)
            ->assertSee('AI Settings');
    }

    public function test_can_save_api_keys(): void
    {
        $this->actingAs($this->user);

        Livewire::test(AiSettings::class)
            ->set('data.openai_key', 'sk-test-openai-key-123')
            ->set('data.anthropic_key', 'sk-ant-test-key-456')
            ->call('save');

        $this->assertDatabaseHas('user_ai_settings', [
            'user_id' => $this->user->id,
        ]);

        $settings = $this->user->fresh()->aiSetting;
        $this->assertEquals('sk-test-openai-key-123', $settings->openai_key);
        $this->assertEquals('sk-ant-test-key-456', $settings->anthropic_key);
    }

    public function test_keys_are_encrypted_in_database(): void
    {
        $this->actingAs($this->user);

        Livewire::test(AiSettings::class)
            ->set('data.openai_key', 'sk-test-plaintext-key')
            ->call('save');

        $raw = \DB::table('user_ai_settings')
            ->where('user_id', $this->user->id)
            ->value('openai_key');

        $this->assertNotEquals('sk-test-plaintext-key', $raw);
    }

    public function test_existing_settings_are_loaded_on_mount(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'openai_key' => 'sk-existing-key',
            'cache_embeddings' => true,
        ]);

        Livewire::test(AiSettings::class)
            ->assertSet('data.openai_key', 'sk-existing-key')
            ->assertSet('data.cache_embeddings', true);
    }

    public function test_can_update_existing_settings(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'openai_key' => 'sk-old-key',
        ]);

        Livewire::test(AiSettings::class)
            ->set('data.openai_key', 'sk-new-key')
            ->call('save');

        $settings = $this->user->fresh()->aiSetting;
        $this->assertEquals('sk-new-key', $settings->openai_key);
        $this->assertDatabaseCount('user_ai_settings', 1);
    }

    public function test_can_save_ollama_base_url(): void
    {
        $this->actingAs($this->user);

        Livewire::test(AiSettings::class)
            ->set('data.ollama_url', 'http://192.168.1.100:11434')
            ->call('save');

        $this->assertDatabaseHas('user_ai_settings', [
            'user_id' => $this->user->id,
            'ollama_url' => 'http://192.168.1.100:11434',
        ]);
    }

    public function test_can_save_cache_embeddings_toggle(): void
    {
        $this->actingAs($this->user);

        Livewire::test(AiSettings::class)
            ->set('data.cache_embeddings', true)
            ->call('save');

        $settings = $this->user->fresh()->aiSetting;
        $this->assertTrue($settings->cache_embeddings);
    }

    public function test_test_connection_sends_prompt(): void
    {
        $this->actingAs($this->user);

        ChatAssistant::fake(['Connection successful!']);

        Livewire::test(AiSettings::class)
            ->set('data.openai_key', 'sk-test-key')
            ->call('testConnection', 'openai', 'gpt-4o-mini');

        ChatAssistant::assertPrompted('Say "Connection successful" in one short sentence.');

        $this->assertDatabaseHas('user_ai_settings', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_test_connection_handles_failure(): void
    {
        $this->actingAs($this->user);

        ChatAssistant::fake(function (): never {
            throw new \RuntimeException('Invalid API key');
        });

        Livewire::test(AiSettings::class)
            ->call('testConnection', 'openai', 'gpt-4o-mini')
            ->assertNotified('Connection failed');
    }

    public function test_user_keys_injected_into_chat_config(): void
    {
        $this->actingAs($this->user);

        UserAiSetting::factory()->create([
            'user_id' => $this->user->id,
            'anthropic_key' => 'sk-ant-user-key',
            'default_provider' => 'gemini',
            'cache_embeddings' => true,
        ]);

        ChatAssistant::fake(['AI response']);

        Livewire::test(Chat::class)
            ->set('messageInput', 'Hello')
            ->call('sendMessage')
            ->call('ask', 'Hello');

        $this->assertEquals('sk-ant-user-key', config('ai.providers.anthropic.key'));
        $this->assertEquals('gemini', config('ai.default'));
        $this->assertTrue(config('ai.caching.embeddings.cache'));
    }
}
