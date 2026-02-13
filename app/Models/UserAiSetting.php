<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAiSetting extends Model
{
    /** @use HasFactory<\Database\Factories\UserAiSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'openai_key' => 'encrypted',
            'anthropic_key' => 'encrypted',
            'gemini_key' => 'encrypted',
            'groq_key' => 'encrypted',
            'xai_key' => 'encrypted',
            'deepseek_key' => 'encrypted',
            'mistral_key' => 'encrypted',
            'ollama_key' => 'encrypted',
            'lm_studio_key' => 'encrypted',
            'cache_embeddings' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
