<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//Review model — user reviews on spots.

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'rating',
        'user_id',
        'spot_id',
    ];

    //The user who wrote this review.
   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //The spot being reviewed.
  
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }
}
