<?php

namespace App\Livewire\Admin;

use App\Livewire\Chat;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Models extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public static function capabilityModels(): array
    {
        return [
            'text' => Chat::providerModels(),
            'images' => [
                'openai' => [
                    'dall-e-3' => 'DALL-E 3',
                    'dall-e-2' => 'DALL-E 2',
                ],
                'gemini' => [
                    'imagen-3.0-generate-002' => 'Imagen 3.0',
                ],
                'xai' => [
                    'grok-2-image' => 'Grok 2 Image',
                ],
            ],
            'audio' => [
                'openai' => [
                    'tts-1' => 'TTS-1',
                    'tts-1-hd' => 'TTS-1 HD',
                ],
                'eleven' => [
                    'eleven_multilingual_v2' => 'Multilingual v2',
                    'eleven_turbo_v2' => 'Turbo v2',
                ],
            ],
            'transcription' => [
                'openai' => [
                    'whisper-1' => 'Whisper',
                ],
                'mistral' => [
                    'mistral-large-latest' => 'Mistral Large',
                ],
                'eleven' => [
                    'scribe_v1' => 'Scribe v1',
                ],
            ],
            'embeddings' => [
                'openai' => [
                    'text-embedding-3-small' => 'Embedding 3 Small',
                    'text-embedding-3-large' => 'Embedding 3 Large',
                ],
                'gemini' => [
                    'text-embedding-004' => 'Embedding 004',
                ],
                'mistral' => [
                    'mistral-embed' => 'Mistral Embed',
                ],
                'ollama' => [
                    'nomic-embed-text' => 'Nomic Embed Text',
                ],
                'cohere' => [
                    'embed-english-v3.0' => 'Embed English v3',
                    'embed-multilingual-v3.0' => 'Embed Multilingual v3',
                ],
            ],
            'reranking' => [
                'cohere' => [
                    'rerank-english-v3.0' => 'Rerank English v3',
                    'rerank-multilingual-v3.0' => 'Rerank Multilingual v3',
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function capabilityLabels(): array
    {
        return [
            'text' => 'Text / Chat',
            'images' => 'Image Generation',
            'audio' => 'Audio (TTS)',
            'transcription' => 'Transcription (STT)',
            'embeddings' => 'Embeddings',
            'reranking' => 'Reranking',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function allProviderLabels(): array
    {
        return array_merge(Chat::providerLabels(), [
            'eleven' => 'ElevenLabs',
            'cohere' => 'Cohere',
        ]);
    }

    public function mount(): void
    {
        $setting = auth()->user()->aiSetting;

        $this->form->fill($setting?->only([
            'default_provider',
            'default_model',
            'default_for_images',
            'default_model_for_images',
            'default_for_audio',
            'default_model_for_audio',
            'default_for_transcription',
            'default_model_for_transcription',
            'default_for_embeddings',
            'default_model_for_embeddings',
            'default_for_reranking',
            'default_model_for_reranking',
        ]) ?? []);
    }

    public function form(Schema $form): Schema
    {
        $capabilities = static::capabilityModels();
        $labels = static::capabilityLabels();
        $providerLabels = static::allProviderLabels();

        $sections = [];

        foreach ($capabilities as $capability => $providers) {
            $providerField = $capability === 'text' ? 'default_provider' : "default_for_{$capability}";
            $modelField = $capability === 'text' ? 'default_model' : "default_model_for_{$capability}";

            $providerOptions = collect($providers)
                ->keys()
                ->mapWithKeys(fn (string $key) => [$key => $providerLabels[$key] ?? $key])
                ->all();

            $sections[] = Section::make($labels[$capability])
                ->schema([
                    Select::make($providerField)
                        ->label('Provider')
                        ->options($providerOptions)
                        ->placeholder('— Select provider —')
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set($modelField, null)),
                    Select::make($modelField)
                        ->label('Model')
                        ->options(function (Get $get) use ($capability, $providerField) {
                            $provider = $get($providerField);

                            if (! $provider) {
                                return [];
                            }

                            return static::capabilityModels()[$capability][$provider] ?? [];
                        })
                        ->placeholder('— Select model —'),
                ])
                ->columns(2);
        }

        return $form
            ->schema($sections)
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
            ->title('Model assignments saved successfully.')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.admin.models');
    }
}
