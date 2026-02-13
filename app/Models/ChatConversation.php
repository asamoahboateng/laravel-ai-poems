<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    /** @use HasFactory<\Database\Factories\ChatConversationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_conversation_id',
        'title',
        'provider',
        'model',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
