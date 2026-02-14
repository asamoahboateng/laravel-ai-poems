<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Poem extends Model
{
    /** @use HasFactory<\Database\Factories\PoemFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'author',
        'genre_id',
        'subject_id',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(PoemEmbedding::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeaturedFirst(Builder $query): void
    {
        $query->orderByDesc('is_featured')
            ->latest('published_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
