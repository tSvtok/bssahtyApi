<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]

//Event model — organized sports sessions.

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'question_id',
        'user_id',
        'spot_id',
        'date',
        'max_participants',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    //The question this event was created from.
     
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // The user who created this event.
     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //The spot where this event takes place.
    
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    //Users participating in this event.
     
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withTimestamps();
    }

    //Check if the event is full.
     
    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_participants;
    }
}
