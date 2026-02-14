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
        Schema::table('user_ai_settings', function (Blueprint $table) {
            $table->string('default_model')->nullable()->after('default_provider');
            $table->string('default_model_for_images')->nullable()->after('default_for_images');
            $table->string('default_model_for_audio')->nullable()->after('default_for_audio');
            $table->string('default_model_for_transcription')->nullable()->after('default_for_transcription');
            $table->string('default_model_for_embeddings')->nullable()->after('default_for_embeddings');
            $table->string('default_model_for_reranking')->nullable()->after('default_for_reranking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_model',
                'default_model_for_images',
                'default_model_for_audio',
                'default_model_for_transcription',
                'default_model_for_embeddings',
                'default_model_for_reranking',
            ]);
        });
    }
};
