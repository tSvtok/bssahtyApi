<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//Response model — answers to questions.


class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'question_id',
        'user_id',
    ];

    //The question this response belongs to.
     
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    //The user who wrote this response.
     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
