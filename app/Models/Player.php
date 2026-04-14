<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    public const POSITIONS = [
        'gk',
        'cb',
        'rb',
        'lb',
        'rwb',
        'lwb',
        'cm',
        'cdm',
        'amf',
        'rm',
        'lm',
        'lwf',
        'rwf',
        'cf',
        'st',
    ];

    protected $fillable = [
        'team_id',
        'name',
        'guardian_name',
        'guardian_email',
        'guardian_phone',
        'guardian_id',
        'squad_number',
        'position',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withPivot('player_response');

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }
}
