<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}