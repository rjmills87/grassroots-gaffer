<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}