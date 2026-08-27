<?php

namespace App\Models;

use App\Enums\JoinRequestStatus;
use App\Notifications\WelcomeToTeamNotification;
use App\Support\InviteCode;
use Database\Factories\TeamJoinRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TeamJoinRequest extends Model
{
    /** @use HasFactory<TeamJoinRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'player_id',
        'user_id',
        'requested_player_name',
        'guardian_name',
        'guardian_email',
        'guardian_phone',
        'status',
        'approved_at',
        'rejected_at',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => JoinRequestStatus::class,
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TeamJoinRequest $request): void {
            if ($request->status === null) {
                $request->status = JoinRequestStatus::Pending;
            }

            if ($request->expires_at === null) {
                $request->expires_at = InviteCode::joinRequestExpiresAt();
            }

            $request->guardian_email = strtolower($request->guardian_email);
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', JoinRequestStatus::Pending)
            ->where(function (Builder $expires) {
                $expires->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isPending(): bool
    {
        return $this->status === JoinRequestStatus::Pending;
    }

    public function hasExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function childName(): string
    {
        return $this->player?->name ?? (string) $this->requested_player_name;
    }

    public function approve(): Player
    {
        if (! $this->isPending()) {
            throw ValidationException::withMessages([
                'status' => 'This join request has already been processed.',
            ]);
        }

        if ($this->hasExpired()) {
            throw ValidationException::withMessages([
                'status' => 'This join request has expired. Ask the parent to send a new one.',
            ]);
        }

        return DB::transaction(function () {
            $guardian = $this->resolveGuardianUser();
            $player = $this->resolvePlayer($guardian);

            if ((int) $player->team_id !== (int) $this->team_id) {
                abort(404);
            }

            if (! $player->isGuardedBy($guardian)) {
                $player->attachGuardian($guardian, $player->guardian_id === null);
            }

            $this->update([
                'status' => JoinRequestStatus::Approved,
                'user_id' => $guardian->id,
                'approved_at' => now(),
            ]);

            $guardian->notify(new WelcomeToTeamNotification($this->team, $player));

            return $player;
        });
    }

    public function reject(): void
    {
        if (! $this->isPending()) {
            throw ValidationException::withMessages([
                'status' => 'This join request has already been processed.',
            ]);
        }

        $this->update([
            'status' => JoinRequestStatus::Rejected,
            'rejected_at' => now(),
        ]);
    }

    protected function resolveGuardianUser(): User
    {
        $existing = User::query()->where('email', $this->guardian_email)->first();

        if ($existing) {
            if ($existing->role === 'coach') {
                throw ValidationException::withMessages([
                    'guardian_email' => 'This email belongs to a coach account.',
                ]);
            }

            return $existing;
        }

        return User::create([
            'name' => $this->guardian_name,
            'email' => $this->guardian_email,
            'password' => Hash::make(Str::random(16)),
            'role' => 'guardian',
        ]);
    }

    protected function resolvePlayer(User $guardian): Player
    {
        if ($this->player_id) {
            $player = Player::query()->lockForUpdate()->findOrFail($this->player_id);

            return $player;
        }

        $player = $this->team->players()->create([
            'name' => $this->requested_player_name,
            'guardian_name' => $this->guardian_name,
            'guardian_email' => $this->guardian_email,
            'guardian_phone' => $this->guardian_phone,
            'guardian_id' => $guardian->id,
        ]);

        $futureEventIds = $this->team->events()->where('starts_at', '>', now())->pluck('id');

        if ($futureEventIds->isNotEmpty()) {
            $player->events()->attach($futureEventIds);
        }

        return $player;
    }
}
