<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//SportCategory model — taxonomy of sports.

class SportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
    ];

    //Questions in this category.
    
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    //Users interested in this sport.
  
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sport_category_user');
    }

    // Spots for this sport category.
    
    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }
}
