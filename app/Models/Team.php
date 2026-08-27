<?php

namespace App\Models;

use App\Support\InviteCode;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'age_group',
        'team_badge_url',
    ];

    protected $hidden = [
        'invite_code',
        'invite_code_generated_at',
        'invite_code_expires_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Team $team): void {
            if (blank($team->invite_code)) {
                $team->assignNewInviteCode();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invite_code_generated_at' => 'datetime',
            'invite_code_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    public function pendingJoinRequests(): HasMany
    {
        return $this->joinRequests()->pending();
    }

    public function assignNewInviteCode(): void
    {
        $this->invite_code = InviteCode::unique();
        $this->invite_code_generated_at = now();
        $this->invite_code_expires_at = InviteCode::expiresAt();
    }

    public function regenerateInviteCode(): void
    {
        $this->assignNewInviteCode();
        $this->save();
    }

    public function joinUrl(): string
    {
        return route('join.show', ['code' => $this->invite_code], true);
    }

    public function whatsappMessage(): string
    {
        $appName = config('app.name');

        return "Hi parents — please join {$this->name} on {$appName} so you can see events and RSVP for your child.\n\nUse your own email (not your child's). If another parent is already linked, you can still join as a second guardian.\n\nJoin here:\n{$this->joinUrl()}\n\nOr enter code: {$this->invite_code}";
    }

    /**
     * @return array{code: string, url: string, whatsapp_message: string, expires_at: string|null}
     */
    public function invitePayload(): array
    {
        return [
            'code' => $this->invite_code,
            'url' => $this->joinUrl(),
            'whatsapp_message' => $this->whatsappMessage(),
            'expires_at' => $this->invite_code_expires_at?->toIso8601String(),
        ];
    }

    public function hasActiveInviteCode(): bool
    {
        if (blank($this->invite_code)) {
            return false;
        }

        if ($this->invite_code_expires_at && $this->invite_code_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * @return array{team: ?Team, valid: bool, reason: ?string}
     */
    public static function resolveInvite(string $code): array
    {
        $normalized = InviteCode::normalize($code);

        if ($normalized === '') {
            return ['team' => null, 'valid' => false, 'reason' => 'invalid'];
        }

        $team = static::query()->where('invite_code', $normalized)->first();

        if (! $team) {
            return ['team' => null, 'valid' => false, 'reason' => 'invalid'];
        }

        if (! $team->hasActiveInviteCode()) {
            return ['team' => $team, 'valid' => false, 'reason' => 'expired'];
        }

        return ['team' => $team, 'valid' => true, 'reason' => null];
    }

    public function guardianUsers(): Collection
    {
        $this->loadMissing(['players.guardians', 'players.guardian']);

        return $this->players
            ->flatMap(fn (Player $player) => $player->allGuardians())
            ->unique('id')
            ->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function pendingJoinPayload(): array
    {
        return $this->joinRequests()
            ->pending()
            ->with('player:id,name,squad_number')
            ->latest()
            ->get()
            ->map(fn (TeamJoinRequest $request) => [
                'id' => $request->id,
                'guardian_name' => $request->guardian_name,
                'guardian_email' => $request->guardian_email,
                'guardian_phone' => $request->guardian_phone,
                'player_id' => $request->player_id,
                'player_name' => $request->childName(),
                'is_new_player' => $request->player_id === null,
                'created_at' => $request->created_at?->toIso8601String(),
            ])
            ->all();
    }

    protected function teamBadgeUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) {
                    return Storage::disk('public')->url($value);
                } else {
                    return asset('placeholder-badge.jpeg');
                }
            }
        );
    }
}
