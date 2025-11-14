<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class Team extends Model
{
    protected $fillable = [
        'name',
        'age_group',
        'team_badge_url',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    protected function teamBadgeUrl(): Attribute
    {
        return Attribute::make(
                get: function ($value) {
                    log::info($value);
                    if($value) {
                        return Storage::url($value);
                    } else {
                        return Storage::url('placeholder-badge.jpeg');
                    }
                }    
            );
    }
}
