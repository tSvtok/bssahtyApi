<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
//User model — the main actor of the Bssahty platform.
 
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'city',
        'level',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Questions asked by this user.
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    //Responses written by this user.
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    //Sport categories the user is interested in.
    
    public function sportCategories(): BelongsToMany
    {
        return $this->belongsToMany(SportCategory::class, 'sport_category_user');
    }

    // Reviews written by this user.
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    //Events the user is participating in.
     
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')->withTimestamps();
    }

    //Conversations the user is part of.
   
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')->withTimestamps();
    }

    //Messages sent by this user.
   
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    //Questions liked by this user.
    
    public function likedQuestions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_likes')->withTimestamps();
    }

    // Spots marked as favorite by this user.
    public function favoriteSpots(): BelongsToMany
    {
        return $this->belongsToMany(Spot::class, 'favorite_spots')->withTimestamps();
    }

    // Check if this user has admin role.
     
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
