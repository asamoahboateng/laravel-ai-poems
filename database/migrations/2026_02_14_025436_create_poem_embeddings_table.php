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
        Schema::create('poem_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poem_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('embedding');
            $table->string('content_hash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poem_embeddings');
    }
};
