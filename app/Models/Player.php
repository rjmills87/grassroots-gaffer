<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Player extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'guardian_name',
        'guardian_email',
        'guardian_phone'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function events() 
    {
        return $this->belongsToMany(Event::class)->withPivot('player_response');

    }
}