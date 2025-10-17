<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use App\Models\Team;
use App\Models\Player;

class WelcomeToTeamNotification extends Notification
{
    use Queueable;
    public $team;
    public $player;

    /**
     * Create a new notification instance.
     */
    public function __construct(Team $team, Player $player )
    {   
        $this->team = $team;
        $this->player = $player;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
     public function toMail(object $notifiable): MailMessage
     {
        $resetUrl = Password::broker()->createToken($notifiable);

        return (new MailMessage)
        ->subject('Welcome to ' . $this->team->name)
        ->line('We are pleased to have you and ' . $this->player->name . ' as part of the ' . $this->team->name . ' family.')
        ->line('To get started, please set up your password using the link below:')
        ->action('Set Up Your Password', url('/reset-password/' . $resetUrl . '?email=' . urlencode($notifiable->email)))
        ->line('Once you\'ve set your password, you can log in to view events and respond to availability requests.')
        ->line('We look forward to the season ahead with ' . $this->player->name . '!');
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
