<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]

//Question model — the core Q&A entity.
 
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'spot_id',
        'sport_category_id',
        'likes_count',
    ];

    //The user who asked this question.
     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //The spot linked to this question.
   
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    //The sport category of this question.
     
    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class);
    }

    //Responses to this question.
     
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    //The event created from this question.
     
    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    //Users who liked this question.
     
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'question_likes')->withTimestamps();
    }
}
