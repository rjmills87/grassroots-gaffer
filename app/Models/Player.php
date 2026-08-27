<?php

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

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

    protected static function booted(): void
    {
        static::created(function (Player $player): void {
            if ($player->guardian_id) {
                $player->guardians()->syncWithoutDetaching([
                    $player->guardian_id => ['is_primary' => true],
                ]);
            }
        });
    }

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

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_guardians')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function attachGuardian(User $user, bool $primary = false): void
    {
        $this->guardians()->syncWithoutDetaching([
            $user->id => ['is_primary' => $primary],
        ]);

        if ($primary || $this->guardian_id === null) {
            $this->forceFill([
                'guardian_id' => $user->id,
                'guardian_name' => $this->guardian_name ?: $user->name,
                'guardian_email' => $this->guardian_email ?: $user->email,
            ])->save();
        }
    }

    public function isGuardedBy(User $user): bool
    {
        if ((int) $this->guardian_id === (int) $user->id) {
            return true;
        }

        return $this->guardians()->where('users.id', $user->id)->exists();
    }

    public function allGuardians(): Collection
    {
        $guardians = $this->relationLoaded('guardians')
            ? $this->guardians
            : $this->guardians()->get();

        if ($this->guardian_id && ! $guardians->contains('id', $this->guardian_id)) {
            $primary = $this->relationLoaded('guardian')
                ? $this->guardian
                : $this->guardian()->first();

            if ($primary) {
                $guardians = $guardians->prepend($primary);
            }
        }

        return $guardians->unique('id')->filter()->values();
    }
}
