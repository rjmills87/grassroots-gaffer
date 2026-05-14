<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewTeamAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $team = $this->message->team;
        $announcementsUrl = route('announcements.index', ['team' => $team->id], true);
        $excerpt = Str::limit($this->message->message, 200);

        return (new MailMessage)
            ->subject('New announcement: '.$team->name)
            ->line('The coach has posted a new announcement for '.$team->name.'.')
            ->line('Preview: '.$excerpt)
            ->line('If files are attached, open the link below to view or download them in the app.')
            ->action('View announcements', $announcementsUrl)
            ->line('Thank you for using '.config('app.name').'.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'team_id' => $this->message->team_id,
        ];
    }
}
