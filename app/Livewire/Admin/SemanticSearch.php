<?php

namespace App\Livewire\Admin;

use App\Models\PoemEmbedding;
use Filament\Notifications\Notification;
use Laravel\Ai\Embeddings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class SemanticSearch extends Component
{
    public string $query = '';

    public bool $isSearching = false;

    public bool $hasSearched = false;

    /** @var array<int, array{poem: array, similarity: float}> */
    public array $results = [];

    public function search(): void
    {
        $this->validate([
            'query' => 'required|string|min:2|max:1000',
        ]);

        $this->isSearching = true;
        $this->results = [];

        $this->applyUserAiSettings();

        try {
            $settings = auth()->user()->aiSetting;
            $provider = $settings?->default_for_embeddings;
            $model = $settings?->default_model_for_embeddings;

            $response = Embeddings::for([$this->query])->generate($provider, $model);
            $queryEmbedding = $response->first();

            $poemEmbeddings = PoemEmbedding::with(['poem.genre', 'poem.subject'])->get();

            $scored = [];

            foreach ($poemEmbeddings as $poemEmbedding) {
                if (! $poemEmbedding->poem) {
                    continue;
                }

                $similarity = $this->cosineSimilarity($queryEmbedding, $poemEmbedding->embedding);

                $scored[] = [
                    'poem' => [
                        'id' => $poemEmbedding->poem->id,
                        'title' => $poemEmbedding->poem->title,
                        'slug' => $poemEmbedding->poem->slug,
                        'author' => $poemEmbedding->poem->author,
                        'content' => str($poemEmbedding->poem->content)->limit(200)->toString(),
                        'genre' => $poemEmbedding->poem->genre?->name,
                        'subject' => $poemEmbedding->poem->subject?->name,
                    ],
                    'similarity' => round($similarity * 100, 1),
                ];
            }

            usort($scored, fn (array $a, array $b) => $b['similarity'] <=> $a['similarity']);

            $this->results = array_slice($scored, 0, 10);
            $this->hasSearched = true;
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Search failed')
                ->body(str($e->getMessage())->limit(200)->toString())
                ->danger()
                ->send();

            $this->hasSearched = false;
        } finally {
            $this->isSearching = false;
        }
    }

    public function render()
    {
        return view('livewire.admin.semantic-search');
    }

    /**
     * @param  array<float>  $a
     * @param  array<float>  $b
     */
    protected function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator === 0.0) {
            return 0.0;
        }

        return $dot / $denominator;
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

        if ($settings->default_for_embeddings) {
            config(['ai.default_for_embeddings' => $settings->default_for_embeddings]);
        }
    }
}
