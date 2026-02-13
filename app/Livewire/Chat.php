<?php

namespace App\Livewire;

use App\Ai\Agents\ChatAssistant;
use App\Models\ChatConversation;
use Illuminate\Support\Collection;
use Laravel\Ai\Streaming\Events\TextDelta;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.chat')]
class Chat extends Component
{
    public ?int $conversationId = null;

    public string $messageInput = '';

    public string $selectedProvider = 'openai';

    public string $selectedModel = 'gpt-4o-mini';

    public string $streamedAnswer = '';

    public bool $isStreaming = false;

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    /**
     * @return array<string, array<string, string>>
     */
    public static function providerModels(): array
    {
        return [
            'openai' => [
                'gpt-4o-mini' => 'GPT-4o Mini',
                'gpt-4o' => 'GPT-4o',
                'gpt-4.1' => 'GPT-4.1',
                'gpt-4.1-mini' => 'GPT-4.1 Mini',
                'gpt-4.1-nano' => 'GPT-4.1 Nano',
                'o3-mini' => 'o3 Mini',
            ],
            'anthropic' => [
                'claude-sonnet-4-5-20250929' => 'Claude Sonnet 4.5',
                'claude-haiku-4-5-20251001' => 'Claude Haiku 4.5',
                'claude-opus-4-6' => 'Claude Opus 4.6',
            ],
            'gemini' => [
                'gemini-2.0-flash' => 'Gemini 2.0 Flash',
                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite',
                'gemini-2.5-pro-preview-06-05' => 'Gemini 2.5 Pro',
            ],
            'groq' => [
                'llama-3.3-70b-versatile' => 'Llama 3.3 70B',
                'llama-3.1-8b-instant' => 'Llama 3.1 8B',
                'mixtral-8x7b-32768' => 'Mixtral 8x7B',
            ],
            'xai' => [
                'grok-3' => 'Grok 3',
                'grok-3-mini' => 'Grok 3 Mini',
            ],
            'deepseek' => [
                'deepseek-chat' => 'DeepSeek V3',
                'deepseek-reasoner' => 'DeepSeek R1',
            ],
            'mistral' => [
                'mistral-large-latest' => 'Mistral Large',
                'mistral-small-latest' => 'Mistral Small',
            ],
            'ollama' => [
                'llama3.2' => 'Llama 3.2',
                'mistral' => 'Mistral',
                'phi3' => 'Phi-3',
            ],
            'lm_studio' => [
                'default' => 'Default Model',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function providerLabels(): array
    {
        return [
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic',
            'gemini' => 'Google Gemini',
            'groq' => 'Groq',
            'xai' => 'xAI',
            'deepseek' => 'DeepSeek',
            'mistral' => 'Mistral',
            'ollama' => 'Ollama',
            'lm_studio' => 'LM Studio',
        ];
    }

    public function mount(): void
    {
        //
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageInput' => 'required|string|max:10000',
        ]);

        $prompt = $this->messageInput;
        $this->messageInput = '';

        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        $this->js('$wire.ask('.json_encode($prompt).')');
    }

    public function ask(string $prompt): void
    {
        $this->isStreaming = true;
        $this->streamedAnswer = '';

        $this->applyUserAiSettings();

        $user = auth()->user();
        $agent = ChatAssistant::make();

        $conversation = $this->conversationId
            ? ChatConversation::where('user_id', $user->id)->find($this->conversationId)
            : null;

        if ($conversation && $conversation->agent_conversation_id) {
            $agent->continue($conversation->agent_conversation_id, as: $user);
        } else {
            $agent->forUser($user);
        }

        $fullResponse = '';

        $response = $agent->stream($prompt, provider: $this->selectedProvider, model: $this->selectedModel);

        foreach ($response as $event) {
            if ($event instanceof TextDelta) {
                $fullResponse .= $event->delta;
                $this->stream(to: 'answer', content: $event->delta);
            }
        }

        $agentConversationId = $agent->currentConversation();

        if (! $conversation) {
            $title = str($prompt)->limit(50)->toString();

            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'agent_conversation_id' => $agentConversationId,
                'title' => $title,
                'provider' => $this->selectedProvider,
                'model' => $this->selectedModel,
            ]);

            $this->conversationId = $conversation->id;
        } else {
            $conversation->update([
                'agent_conversation_id' => $agentConversationId,
                'provider' => $this->selectedProvider,
                'model' => $this->selectedModel,
            ]);
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $fullResponse,
        ];

        $this->streamedAnswer = '';
        $this->isStreaming = false;
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->streamedAnswer = '';
        $this->isStreaming = false;
    }

    public function selectConversation(int $id): void
    {
        $conversation = ChatConversation::where('user_id', auth()->id())->findOrFail($id);

        $this->conversationId = $conversation->id;
        $this->selectedProvider = $conversation->provider ?? 'openai';
        $this->selectedModel = $conversation->model ?? 'gpt-4o-mini';

        $this->loadMessages($conversation);
    }

    public function deleteConversation(int $id): void
    {
        ChatConversation::where('user_id', auth()->id())->where('id', $id)->delete();

        if ($this->conversationId === $id) {
            $this->newConversation();
        }
    }

    public function updatedSelectedProvider(): void
    {
        $models = static::providerModels()[$this->selectedProvider] ?? [];
        $this->selectedModel = array_key_first($models) ?? '';
    }

    public function getConversationsProperty(): Collection
    {
        return ChatConversation::where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.chat');
    }

    protected function applyUserAiSettings(): void
    {
        $settings = auth()->user()->aiSetting;

        if (! $settings) {
            return;
        }

        $keyMap = [
            'openai_key' => 'ai.providers.openai.key',
            'anthropic_key' => 'ai.providers.anthropic.key',
            'gemini_key' => 'ai.providers.gemini.key',
            'groq_key' => 'ai.providers.groq.key',
            'xai_key' => 'ai.providers.xai.key',
            'deepseek_key' => 'ai.providers.deepseek.key',
            'mistral_key' => 'ai.providers.mistral.key',
            'ollama_key' => 'ai.providers.ollama.key',
            'ollama_url' => 'ai.providers.ollama.url',
            'lm_studio_key' => 'ai.providers.lm_studio.key',
            'lm_studio_url' => 'ai.providers.lm_studio.url',
        ];

        foreach ($keyMap as $settingField => $configKey) {
            if ($settings->$settingField) {
                config([$configKey => $settings->$settingField]);
            }
        }

        $defaultMap = [
            'default_provider' => 'ai.default',
            'default_for_images' => 'ai.default_for_images',
            'default_for_audio' => 'ai.default_for_audio',
            'default_for_transcription' => 'ai.default_for_transcription',
            'default_for_embeddings' => 'ai.default_for_embeddings',
            'default_for_reranking' => 'ai.default_for_reranking',
        ];

        foreach ($defaultMap as $settingField => $configKey) {
            if ($settings->$settingField) {
                config([$configKey => $settings->$settingField]);
            }
        }

        config(['ai.caching.embeddings.cache' => $settings->cache_embeddings]);
    }

    protected function loadMessages(ChatConversation $conversation): void
    {
        $this->messages = [];

        if (! $conversation->agent_conversation_id) {
            return;
        }

        $agent = ChatAssistant::make();
        $agent->continue($conversation->agent_conversation_id, as: auth()->user());

        foreach ($agent->messages() as $message) {
            $this->messages[] = [
                'role' => $message->role->value,
                'content' => $message->content,
            ];
        }
    }
}
