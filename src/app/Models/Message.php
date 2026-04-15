<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//Message model — real-time chat messages.

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'conversation_id',
        'user_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    //The conversation this message belongs to.
     
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    //The user who sent this message.
     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
