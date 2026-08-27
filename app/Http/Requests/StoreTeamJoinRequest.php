<?php

namespace App\Http\Requests;

use App\Enums\JoinRequestStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTeamJoinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'guardian_phone' => ['required', 'string', 'max:255'],
            'child_mode' => ['required', 'in:existing,new'],
            'player_id' => ['required_if:child_mode,existing', 'nullable', 'integer', 'exists:players,id'],
            'requested_player_name' => ['required_if:child_mode,new', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'guardian_name.required' => 'Please enter your name.',
            'guardian_email.required' => 'Please enter your own email address.',
            'guardian_phone.required' => 'Please enter a phone number so the coach can reach you.',
            'child_mode.required' => 'Please choose which child you are joining for.',
            'player_id.required_if' => 'Please choose your child from the squad list.',
            'requested_player_name.required_if' => "Please enter your child's name.",
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $resolved = Team::resolveInvite((string) $this->route('code'));

            if (! $resolved['valid'] || ! $resolved['team'] instanceof Team) {
                $validator->errors()->add('code', 'This join link is no longer valid. Ask your coach for a new one.');

                return;
            }

            $team = $resolved['team'];
            $email = strtolower((string) $this->input('guardian_email'));

            $existingUser = User::query()->where('email', $email)->first();

            if ($existingUser?->role === 'coach') {
                $validator->errors()->add('guardian_email', 'This email is already used by a coach account. Use a parent email instead.');
            }

            if ($this->input('child_mode') === 'existing') {
                $player = Player::query()->find($this->input('player_id'));

                if (! $player || (int) $player->team_id !== (int) $team->id) {
                    $validator->errors()->add('player_id', 'Please choose a child from this team.');

                    return;
                }

                if ($existingUser && $player->isGuardedBy($existingUser)) {
                    $validator->errors()->add('player_id', 'You are already linked to this child.');
                }

                $duplicate = TeamJoinRequest::query()
                    ->where('team_id', $team->id)
                    ->where('guardian_email', $email)
                    ->where('player_id', $player->id)
                    ->where('status', JoinRequestStatus::Pending)
                    ->exists();

                if ($duplicate) {
                    $validator->errors()->add('player_id', 'You already have a pending request for this child. The coach still needs to approve it.');
                }

                return;
            }

            $duplicateNewChild = TeamJoinRequest::query()
                ->where('team_id', $team->id)
                ->where('guardian_email', $email)
                ->whereNull('player_id')
                ->where('requested_player_name', $this->input('requested_player_name'))
                ->where('status', JoinRequestStatus::Pending)
                ->exists();

            if ($duplicateNewChild) {
                $validator->errors()->add('requested_player_name', 'You already have a pending request for this child. The coach still needs to approve it.');
            }
        });
    }
}
