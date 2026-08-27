<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'guardian_id');
    }

    public function guardedPlayers(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_guardians')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function isCoachOf(Team $team): bool
    {
        return $this->role === 'coach' && (int) $team->user_id === (int) $this->id;
    }

    public function accessiblePlayers(): Builder
    {
        return Player::query()->where(function (Builder $query) {
            $query->where('guardian_id', $this->id)
                ->orWhereHas('guardians', function (Builder $guardians) {
                    $guardians->where('users.id', $this->id);
                });
        });
    }

    public function isGuardianOf(Player $player): bool
    {
        return $this->accessiblePlayers()->where('players.id', $player->id)->exists();
    }

    public function isGuardianOnTeam(Team $team): bool
    {
        return $this->accessiblePlayers()->where('team_id', $team->id)->exists();
    }

    public function canAccessTeam(Team $team): bool
    {
        if ($this->isCoachOf($team)) {
            return true;
        }

        if ($this->role === 'guardian') {
            return $this->isGuardianOnTeam($team);
        }

        return false;
    }
}
