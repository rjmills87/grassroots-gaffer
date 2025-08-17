<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'age_group',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}