<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//Spot model — a geo-located sports venue.

class Spot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'city',
        'sport_category_id',
        'created_by',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    //The sport category of this spot.
    
    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class);
    }

    //The user who created this spot.
  
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //Questions linked to this spot.
     
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    //Reviews for this spot.
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    //Users who favorited this spot.
   
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_spots')->withTimestamps();
    }

    //Events at this spot.
   
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Scope: filter spots within a given radius (in km) from a point.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $lat
     * @param  float  $lng
     * @param  float  $radius  in kilometers
     */
    public function scopeNearby($query, float $lat, float $lng, float $radius = 10)
    {
        $haversine = "(6371 * acos(cos(radians(?))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?))
            * sin(radians(latitude))))";

        return $query
            ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->havingRaw("distance < ?", [$radius])
            ->orderBy('distance');
    }
}
