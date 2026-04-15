<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\TypeScriptTransformer\Attributes\TypeScript; 
#[TypeScript]
// Conversation model — container for private discussions.
 
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [];

    // Users participating in this conversation.
     
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')->withTimestamps();
    }

    // Messages in this conversation.
     
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    //Get the latest message in this conversation.
     
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
