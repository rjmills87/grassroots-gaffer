<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'occurs_at',
        'location',
        'details',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected function casts(): array {
        return [
            'occurs_at' => 'datetime',
        ];
    }

    public function players() {
        return $this->belongsToMany(Player::class)->withPivot('player_response');
    }
}