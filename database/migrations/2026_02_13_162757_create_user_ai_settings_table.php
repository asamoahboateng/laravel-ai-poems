<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Provider API Keys
            $table->text('openai_key')->nullable();
            $table->text('anthropic_key')->nullable();
            $table->text('gemini_key')->nullable();
            $table->text('groq_key')->nullable();
            $table->text('xai_key')->nullable();
            $table->text('deepseek_key')->nullable();
            $table->text('mistral_key')->nullable();
            $table->text('ollama_key')->nullable();
            $table->string('ollama_url')->nullable();
            $table->text('lm_studio_key')->nullable();
            $table->string('lm_studio_url')->nullable();

            // AI Config Defaults
            $table->string('default_provider')->nullable();
            $table->string('default_for_images')->nullable();
            $table->string('default_for_audio')->nullable();
            $table->string('default_for_transcription')->nullable();
            $table->string('default_for_embeddings')->nullable();
            $table->string('default_for_reranking')->nullable();
            $table->boolean('cache_embeddings')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ai_settings');
    }
};
