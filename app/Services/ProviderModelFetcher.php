<?php

namespace App\Services;

use App\Models\UserAiSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProviderModelFetcher
{
    /**
     * @var array<string, array{url: string, auth: string, key_field: string, url_field?: string}>
     */
    protected static array $providerEndpoints = [
        'openai' => [
            'url' => 'https://api.openai.com/v1/models',
            'auth' => 'bearer',
            'key_field' => 'openai_key',
        ],
        'anthropic' => [
            'url' => 'https://api.anthropic.com/v1/models',
            'auth' => 'anthropic',
            'key_field' => 'anthropic_key',
        ],
        'gemini' => [
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models',
            'auth' => 'query',
            'key_field' => 'gemini_key',
        ],
        'groq' => [
            'url' => 'https://api.groq.com/openai/v1/models',
            'auth' => 'bearer',
            'key_field' => 'groq_key',
        ],
        'xai' => [
            'url' => 'https://api.x.ai/v1/models',
            'auth' => 'bearer',
            'key_field' => 'xai_key',
        ],
        'deepseek' => [
            'url' => 'https://api.deepseek.com/models',
            'auth' => 'bearer',
            'key_field' => 'deepseek_key',
        ],
        'mistral' => [
            'url' => 'https://api.mistral.ai/v1/models',
            'auth' => 'bearer',
            'key_field' => 'mistral_key',
        ],
        'ollama' => [
            'url' => 'http://localhost:11434/api/tags',
            'auth' => 'none',
            'key_field' => 'ollama_key',
            'url_field' => 'ollama_url',
        ],
        'lm_studio' => [
            'url' => 'http://localhost:1234/v1/models',
            'auth' => 'bearer',
            'key_field' => 'lm_studio_key',
            'url_field' => 'lm_studio_url',
        ],
        'cohere' => [
            'url' => 'https://api.cohere.com/v2/models',
            'auth' => 'bearer',
            'key_field' => 'cohere_key',
        ],
        'openrouter' => [
            'url' => 'https://openrouter.ai/api/v1/models',
            'auth' => 'bearer',
            'key_field' => 'openrouter_key',
        ],
    ];

    /**
     * Fetch models from a provider's API.
     *
     * @return array<string, string>
     */
    public function fetch(string $provider, UserAiSetting $settings): array
    {
        $endpoint = static::$providerEndpoints[$provider] ?? null;

        if (! $endpoint) {
            return [];
        }

        $apiKey = $settings->{$endpoint['key_field']} ?? null;
        $baseUrl = isset($endpoint['url_field']) ? ($settings->{$endpoint['url_field']} ?? null) : null;
        $url = $this->buildUrl($provider, $endpoint, $baseUrl);

        $response = match ($endpoint['auth']) {
            'bearer' => Http::withToken($apiKey ?? '')->timeout(15)->get($url),
            'anthropic' => Http::withHeaders([
                'x-api-key' => $apiKey ?? '',
                'anthropic-version' => '2023-06-01',
            ])->timeout(15)->get($url),
            'query' => Http::timeout(15)->get($url, ['key' => $apiKey]),
            'none' => Http::timeout(15)->get($url),
            default => throw new \RuntimeException("Unknown auth type: {$endpoint['auth']}"),
        };

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Failed to fetch models from {$provider}: {$response->status()} {$response->body()}"
            );
        }

        return $this->parseResponse($provider, $response->json());
    }

    /**
     * Fetch models and cache the result.
     *
     * @return array<string, string>
     */
    public function fetchAndCache(string $provider, UserAiSetting $settings, int $userId): array
    {
        $models = $this->fetch($provider, $settings);

        $cacheKey = "provider_models.{$userId}.{$provider}";
        Cache::put($cacheKey, $models, now()->addDay());

        return $models;
    }

    /**
     * Get cached models for a provider, if available.
     *
     * @return array<string, string>|null
     */
    public static function cached(string $provider, int $userId): ?array
    {
        return Cache::get("provider_models.{$userId}.{$provider}");
    }

    /**
     * Check if a provider supports model listing.
     */
    public static function supports(string $provider): bool
    {
        return isset(static::$providerEndpoints[$provider]);
    }

    /**
     * @return array<string>
     */
    public static function supportedProviders(): array
    {
        return array_keys(static::$providerEndpoints);
    }

    protected function buildUrl(string $provider, array $endpoint, ?string $baseUrl): string
    {
        if ($provider === 'ollama' && $baseUrl) {
            return rtrim($baseUrl, '/') . '/api/tags';
        }

        if ($provider === 'lm_studio' && $baseUrl) {
            return rtrim($baseUrl, '/') . '/models';
        }

        return $endpoint['url'];
    }

    /**
     * @return array<string, string>
     */
    protected function parseResponse(string $provider, ?array $json): array
    {
        if (! $json) {
            return [];
        }

        return match ($provider) {
            'gemini' => $this->parseGeminiModels($json),
            'ollama' => $this->parseOllamaModels($json),
            'anthropic' => $this->parseAnthropicModels($json),
            'cohere' => $this->parseCohereModels($json),
            default => $this->parseOpenAiCompatibleModels($json),
        };
    }

    /**
     * @return array<string, string>
     */
    protected function parseOpenAiCompatibleModels(array $json): array
    {
        $models = [];

        foreach ($json['data'] ?? [] as $model) {
            $id = $model['id'] ?? null;
            if ($id) {
                $models[$id] = $id;
            }
        }

        ksort($models);

        return $models;
    }

    /**
     * @return array<string, string>
     */
    protected function parseAnthropicModels(array $json): array
    {
        $models = [];

        foreach ($json['data'] ?? [] as $model) {
            $id = $model['id'] ?? null;
            $name = $model['display_name'] ?? $id;
            if ($id) {
                $models[$id] = $name;
            }
        }

        ksort($models);

        return $models;
    }

    /**
     * @return array<string, string>
     */
    protected function parseGeminiModels(array $json): array
    {
        $models = [];

        foreach ($json['models'] ?? [] as $model) {
            $name = $model['name'] ?? null;
            $displayName = $model['displayName'] ?? $name;

            if ($name) {
                $id = str_replace('models/', '', $name);
                $models[$id] = $displayName;
            }
        }

        ksort($models);

        return $models;
    }

    /**
     * @return array<string, string>
     */
    protected function parseOllamaModels(array $json): array
    {
        $models = [];

        foreach ($json['models'] ?? [] as $model) {
            $name = $model['name'] ?? null;
            if ($name) {
                $models[$name] = $name;
            }
        }

        ksort($models);

        return $models;
    }

    /**
     * @return array<string, string>
     */
    protected function parseCohereModels(array $json): array
    {
        $models = [];

        foreach ($json['models'] ?? [] as $model) {
            $name = $model['name'] ?? null;
            if ($name) {
                $models[$name] = $model['display_name'] ?? $name;
            }
        }

        ksort($models);

        return $models;
    }
}
