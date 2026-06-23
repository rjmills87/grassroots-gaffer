<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

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
        $this->message->loadMissing(['team', 'attachments']);

        $team = $this->message->team;
        $announcementsUrl = route('announcements.index', ['team' => $team->id], true);

        $mail = (new MailMessage)
            ->subject('New announcement: '.$team->name)
            ->line('The coach has posted a new announcement for '.$team->name.'.')
            ->line($this->message->message)
            ->action('View announcements', $announcementsUrl)
            ->line('Thank you for using '.config('app.name').'.');

        foreach ($this->message->attachments as $attachment) {
            $path = Storage::disk('public')->path($attachment->file_path);

            if (! is_file($path)) {
                continue;
            }

            $mail->attach($path, [
                'as' => $attachment->original_name,
                'mime' => $attachment->mime_type,
            ]);
        }

        return $mail;
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
