<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    protected $fillable = [
        'team_id',
        'type',
        'occurs_at',
        'location',
        'details'
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

    