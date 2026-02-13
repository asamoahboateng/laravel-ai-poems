<?php

namespace App\Livewire\Admin;

use App\Livewire\Chat;
use Filament\Forms\Components\Select;
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
class AiSettings extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

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
            'default_provider',
            'default_for_images',
            'default_for_audio',
            'default_for_transcription',
            'default_for_embeddings',
            'default_for_reranking',
            'cache_embeddings',
        ]) ?? []);
    }

    public function form(Schema $form): Schema
    {
        $providerOptions = array_merge(['' => '— Use .env default —'], Chat::providerLabels());

        return $form
            ->schema([
                Section::make('Default Providers')
                    ->description('Choose which provider to use by default for each AI operation. Leave blank to use the .env configuration.')
                    ->schema([
                        Select::make('default_provider')
                            ->label('Default Provider')
                            ->options($providerOptions),
                        Select::make('default_for_images')
                            ->label('Default for Images')
                            ->options($providerOptions),
                        Select::make('default_for_audio')
                            ->label('Default for Audio')
                            ->options($providerOptions),
                        Select::make('default_for_transcription')
                            ->label('Default for Transcription')
                            ->options($providerOptions),
                        Select::make('default_for_embeddings')
                            ->label('Default for Embeddings')
                            ->options($providerOptions),
                        Select::make('default_for_reranking')
                            ->label('Default for Reranking')
                            ->options($providerOptions),
                        Toggle::make('cache_embeddings')
                            ->label('Cache Embeddings')
                            ->helperText('Cache embedding results to reduce API calls.'),
                    ])
                    ->columns(2),

                Section::make('OpenAI')
                    ->schema([
                        TextInput::make('openai_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('sk-...'),
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
                    ->collapsible(),

                Section::make('Google Gemini')
                    ->schema([
                        TextInput::make('gemini_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('AI...'),
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
                    ->collapsible(),

                Section::make('xAI')
                    ->schema([
                        TextInput::make('xai_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('xai-...'),
                    ])
                    ->collapsible(),

                Section::make('DeepSeek')
                    ->schema([
                        TextInput::make('deepseek_key')
                            ->label('API Key')
                            ->password()
                            ->revealable(),
                    ])
                    ->collapsible(),

                Section::make('Mistral')
                    ->schema([
                        TextInput::make('mistral_key')
                            ->label('API Key')
                            ->password()
                            ->revealable(),
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

    public function render()
    {
        return view('livewire.admin.ai-settings');
    }
}
