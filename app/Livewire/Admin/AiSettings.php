<?php

namespace App\Livewire\Admin;

use App\Ai\Agents\ChatAssistant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class AiSettings extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    /**
     * @var array<string, string>
     */
    protected static array $testModels = [
        'openai' => 'gpt-4o-mini',
        'anthropic' => 'claude-haiku-4-5-20251001',
        'gemini' => 'gemini-2.0-flash-lite',
        'groq' => 'llama-3.1-8b-instant',
        'xai' => 'grok-3-mini',
        'deepseek' => 'deepseek-chat',
        'mistral' => 'mistral-small-latest',
        'ollama' => 'llama3.2',
        'lm_studio' => 'default',
    ];

    public function mount(): void
    {
        $setting = auth()->user()->aiSetting;

        $this->form->fill($setting?->only([
            'openai_key',
            'anthropic_key',
            'gemini_key',
            'groq_key',
            'xai_key',
            'deepseek_key',
            'mistral_key',
            'ollama_key',
            'ollama_url',
            'lm_studio_key',
            'lm_studio_url',
            'cache_embeddings',
        ]) ?? []);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Caching')
                    ->schema([
                        Toggle::make('cache_embeddings')
                            ->label('Cache Embeddings')
                            ->helperText('Cache embedding results to reduce API calls.'),
                    ]),

                Section::make('OpenAI')
                    ->schema([
                        TextInput::make('openai_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('sk-...'),
                    ])
                    ->headerActions([
                        $this->makeTestAction('openai'),
                    ])
                    ->collapsible(),

                Section::make('Anthropic')
                    ->schema([
                        TextInput::make('anthropic_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('sk-ant-...'),
                    ])
                    ->headerActions([
                        $this->makeTestAction('anthropic'),
                    ])
                    ->collapsible(),

                Section::make('Google Gemini')
                    ->schema([
                        TextInput::make('gemini_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('AI...'),
                    ])
                    ->headerActions([
                        $this->makeTestAction('gemini'),
                    ])
                    ->collapsible(),

                Section::make('Groq')
                    ->schema([
                        TextInput::make('groq_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('gsk_...'),
                    ])
                    ->headerActions([
                        $this->makeTestAction('groq'),
                    ])
                    ->collapsible(),

                Section::make('xAI')
                    ->schema([
                        TextInput::make('xai_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('xai-...'),
                    ])
                    ->headerActions([
                        $this->makeTestAction('xai'),
                    ])
                    ->collapsible(),

                Section::make('DeepSeek')
                    ->schema([
                        TextInput::make('deepseek_key')
                            ->label('API Key')
                            ->password()
                            ->revealable(),
                    ])
                    ->headerActions([
                        $this->makeTestAction('deepseek'),
                    ])
                    ->collapsible(),

                Section::make('Mistral')
                    ->schema([
                        TextInput::make('mistral_key')
                            ->label('API Key')
                            ->password()
                            ->revealable(),
                    ])
                    ->headerActions([
                        $this->makeTestAction('mistral'),
                    ])
                    ->collapsible(),

                Section::make('Ollama')
                    ->description('Run models locally. No API key required by default.')
                    ->schema([
                        TextInput::make('ollama_key')
                            ->label('API Key (optional)')
                            ->password()
                            ->revealable(),
                        TextInput::make('ollama_url')
                            ->label('Base URL')
                            ->placeholder('http://localhost:11434')
                            ->url(),
                    ])
                    ->headerActions([
                        $this->makeTestAction('ollama'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('LM Studio')
                    ->description('Run models locally via LM Studio. Uses OpenAI-compatible API.')
                    ->schema([
                        TextInput::make('lm_studio_key')
                            ->label('API Key (optional)')
                            ->password()
                            ->revealable(),
                        TextInput::make('lm_studio_url')
                            ->label('Base URL')
                            ->placeholder('http://localhost:1234/v1')
                            ->url(),
                    ])
                    ->headerActions([
                        $this->makeTestAction('lm_studio'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->aiSetting()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        Notification::make()
            ->title('AI settings saved successfully.')
            ->success()
            ->send();
    }

    public function testConnection(string $provider, string $model): void
    {
        $this->save();

        $settings = auth()->user()->fresh()->aiSetting;

        if ($settings) {
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
        }

        try {
            $agent = ChatAssistant::make();
            $agent->forUser(auth()->user());
            $response = $agent->prompt('Say "Connection successful" in one short sentence.', provider: $provider, model: $model);

            Notification::make()
                ->title('Connection successful!')
                ->body(str($response->text)->limit(100)->toString())
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Connection failed')
                ->body(str($e->getMessage())->limit(200)->toString())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-settings');
    }

    protected function makeTestAction(string $provider): Action
    {
        $model = static::$testModels[$provider];

        return Action::make("test_{$provider}")
            ->label('Test')
            ->icon('heroicon-o-signal')
            ->color('gray')
            ->size('sm')
            ->action(fn () => $this->testConnection($provider, $model));
    }
}
