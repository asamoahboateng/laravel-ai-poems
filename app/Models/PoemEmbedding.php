<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoemEmbedding extends Model
{
    protected $fillable = [
        'poem_id',
        'embedding',
        'content_hash',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }

    public function poem(): BelongsTo
    {
        return $this->belongsTo(Poem::class);
    }
}
