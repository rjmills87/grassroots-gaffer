<?php

namespace App\Notifications;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JoinRequestReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TeamJoinRequest $joinRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->joinRequest->loadMissing(['team', 'player']);

        $team = $this->joinRequest->team;
        $childName = $this->joinRequest->childName();

        return (new MailMessage)
            ->subject('New parent join request for '.$team->name)
            ->line($this->joinRequest->guardian_name.' has asked to join '.$team->name.' as a parent of '.$childName.'.')
            ->line('Their email is '.$this->joinRequest->guardian_email.'.')
            ->action('Review join requests', route('squad.index', ['team' => $team->id], true))
            ->line('Approve the request on the Squad page so they can see events and RSVP for '.$childName.'.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'join_request_id' => $this->joinRequest->id,
            'team_id' => $this->joinRequest->team_id,
        ];
    }
}
