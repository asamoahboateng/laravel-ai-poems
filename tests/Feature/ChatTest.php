<?php

namespace Tests\Feature;

use App\Ai\Agents\ChatAssistant;
use App\Livewire\Chat;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_chat_requires_authentication(): void
    {
        $this->get(route('admin.chat'))
            ->assertRedirect(route('login'));
    }

    public function test_chat_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Chat::class)
            ->assertStatus(200);
    }

    public function test_empty_state_when_no_conversations(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Chat::class)
            ->assertSee('Start a conversation');
    }

    public function test_sidebar_shows_user_conversations(): void
    {
        $this->actingAs($this->user);

        $conversation = ChatConversation::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Test Chat',
        ]);

        Livewire::test(Chat::class)
            ->assertSee('My Test Chat');
    }

    public function test_sidebar_hides_other_users_conversations(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        ChatConversation::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Chat',
        ]);

        Livewire::test(Chat::class)
            ->assertDontSee('Other User Chat');
    }

    public function test_new_conversation_resets_state(): void
    {
        $this->actingAs($this->user);

        $conversation = ChatConversation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(Chat::class)
            ->set('conversationId', $conversation->id)
            ->call('newConversation')
            ->assertSet('conversationId', null)
            ->assertSet('messages', []);
    }

    public function test_can_select_conversation(): void
    {
        $this->actingAs($this->user);

        $conversation = ChatConversation::factory()->create([
            'user_id' => $this->user->id,
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-4-5-20250929',
        ]);

        Livewire::test(Chat::class)
            ->call('selectConversation', $conversation->id)
            ->assertSet('conversationId', $conversation->id)
            ->assertSet('selectedProvider', 'anthropic')
            ->assertSet('selectedModel', 'claude-sonnet-4-5-20250929');
    }

    public function test_can_delete_conversation(): void
    {
        $this->actingAs($this->user);

        $conversation = ChatConversation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(Chat::class)
            ->call('deleteConversation', $conversation->id);

        $this->assertDatabaseMissing('chat_conversations', ['id' => $conversation->id]);
    }

    public function test_cannot_delete_other_users_conversation(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $conversation = ChatConversation::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Livewire::test(Chat::class)
            ->call('deleteConversation', $conversation->id);

        $this->assertDatabaseHas('chat_conversations', ['id' => $conversation->id]);
    }

    public function test_can_send_message(): void
    {
        $this->actingAs($this->user);

        ChatAssistant::fake(['This is a fake AI response.']);

        Livewire::test(Chat::class)
            ->set('messageInput', 'Hello AI')
            ->call('sendMessage')
            ->call('ask', 'Hello AI');

        ChatAssistant::assertPrompted('Hello AI');

        $this->assertDatabaseHas('chat_conversations', [
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
        ]);
    }

    public function test_empty_message_not_sent(): void
    {
        $this->actingAs($this->user);

        ChatAssistant::fake();

        Livewire::test(Chat::class)
            ->set('messageInput', '')
            ->call('sendMessage')
            ->assertHasErrors(['messageInput']);

        ChatAssistant::assertNeverPrompted();
    }

    public function test_conversation_stores_provider_and_model(): void
    {
        $this->actingAs($this->user);

        ChatAssistant::fake(['Response from Anthropic']);

        Livewire::test(Chat::class)
            ->set('selectedProvider', 'anthropic')
            ->set('selectedModel', 'claude-sonnet-4-5-20250929')
            ->set('messageInput', 'Test message')
            ->call('sendMessage')
            ->call('ask', 'Test message');

        $this->assertDatabaseHas('chat_conversations', [
            'user_id' => $this->user->id,
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-4-5-20250929',
        ]);
    }

    public function test_deleting_active_conversation_resets_state(): void
    {
        $this->actingAs($this->user);

        $conversation = ChatConversation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(Chat::class)
            ->set('conversationId', $conversation->id)
            ->call('deleteConversation', $conversation->id)
            ->assertSet('conversationId', null)
            ->assertSet('messages', []);
    }

    public function test_provider_change_resets_model(): void
    {
        $this->actingAs($this->user);

        Livewire::test(Chat::class)
            ->set('selectedProvider', 'anthropic')
            ->assertSet('selectedModel', 'claude-sonnet-4-5-20250929');
    }

    public function test_cached_models_used_in_provider_change(): void
    {
        $this->actingAs($this->user);

        Cache::put("provider_models.{$this->user->id}.openai", [
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-4o' => 'gpt-4o',
        ], now()->addDay());

        Livewire::test(Chat::class)
            ->set('selectedProvider', 'openai')
            ->assertSet('selectedModel', 'gpt-4-turbo');
    }

    public function test_provider_labels_include_openrouter(): void
    {
        $labels = Chat::providerLabels();

        $this->assertArrayHasKey('openrouter', $labels);
        $this->assertEquals('OpenRouter', $labels['openrouter']);
    }

    public function test_provider_models_include_openrouter(): void
    {
        $models = Chat::providerModels();

        $this->assertArrayHasKey('openrouter', $models);
    }
}
