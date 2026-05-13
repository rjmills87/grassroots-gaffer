<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttachment;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EventController extends Controller
{
    public function store(Request $request, Team $team)
    {
        if (! $this->userIsTeamCoach(auth()->user(), $team)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'location' => 'required|string|max:255',
            'details' => 'required|string|max:255',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $event = $team->events()->create($this->extractEventData($validated));

        $playerIds = $team->players()->pluck('id');

        $event->players()->attach($playerIds);
        $this->storeAttachments($event, $request->file('attachments', []));

        return redirect()->route('events.index', ['team' => $team->id]);
    }

    public function show(Event $event)
    {
        $user = auth()->user();

        if (! $this->userCanAccessEvent($user, $event)) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Event/Show', [
            'event' => $event->load(['players', 'attachments']),
            'user' => $user,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->ensureCoachCanManageFutureEvent($event);

        $validated = $request->validate([
            'type' => 'required|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'location' => 'required|string|max:255',
            'details' => 'required|string|max:255',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'removed_attachment_ids' => 'nullable|array',
            'removed_attachment_ids.*' => 'integer',
        ]);

        $event->update($this->extractEventData($validated));
        $this->removeAttachments($event, $validated['removed_attachment_ids'] ?? []);
        $this->storeAttachments($event, $request->file('attachments', []));

        return redirect()->route('event.show', $event);
    }

    public function destroy(Event $event)
    {
        $this->ensureCoachCanManageFutureEvent($event);

        $teamId = $event->team_id;
        foreach ($event->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $event->delete();

        return redirect()->route('events.index', ['team' => $teamId]);
    }

    public function updatePlayerResponse(Request $request, Event $event, Player $player)
    {
        if ($player->team_id !== $event->team_id || ! $event->players()->where('players.id', $player->id)->exists()) {
            abort(404);
        }

        $user = auth()->user();
        $isTeamCoach = $this->userIsTeamCoach($user, $event->team);
        $isPlayerGuardian = $player->guardian_id === $user->id;

        if (! $isTeamCoach && ! $isPlayerGuardian) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'player_response' => 'required|string|in:attending,unavailable',
        ]);

        $event->players()->updateExistingPivot($player->id, $validated);

        return redirect()->route('event.show', $event);
    }

    public function sendReminders(Event $event)
    {
        if (! $this->userIsTeamCoach(auth()->user(), $event->team)) {
            abort(403, 'Unauthorized action.');
        }

        $playersWithoutResponse = $event->players()->wherePivotNull('player_response')->get();

        foreach ($playersWithoutResponse as $player) {
            $guardian = $player->guardian;

            if (! $guardian) {
                continue;
            }

            $guardian->notify(new EventReminderNotification($event));
        }

        return redirect()->route('event.show', $event);
    }

    public function downloadAttachment(EventAttachment $eventAttachment)
    {
        if (! $this->userCanAccessEvent(auth()->user(), $eventAttachment->event)) {
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('public')->download($eventAttachment->file_path, $eventAttachment->original_name);
    }

    public function previewAttachment(EventAttachment $eventAttachment)
    {
        if (! $this->userCanAccessEvent(auth()->user(), $eventAttachment->event)) {
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('public')->response(
            $eventAttachment->file_path,
            $eventAttachment->original_name,
            [
                'Content-Type' => $eventAttachment->mime_type,
                'Content-Disposition' => 'inline; filename="'.$eventAttachment->original_name.'"',
            ]
        );
    }

    protected function userIsTeamCoach(User $user, Team $team): bool
    {
        return $user->role === 'coach' && $team->user_id === $user->id;
    }

    protected function userCanAccessEvent(User $user, Event $event): bool
    {
        if ($this->userIsTeamCoach($user, $event->team)) {
            return true;
        }

        if ($user->role === 'guardian') {
            return $user->players()->where('team_id', $event->team_id)->exists();
        }

        return false;
    }

    protected function ensureCoachCanManageFutureEvent(Event $event): void
    {
        if (! $this->userIsTeamCoach(auth()->user(), $event->team)) {
            abort(403, 'Unauthorized action.');
        }

        if ($event->starts_at->lte(now())) {
            abort(403, 'Past events cannot be changed.');
        }
    }

    protected function extractEventData(array $validated): array
    {
        return [
            'type' => $validated['type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'location' => $validated['location'],
            'details' => $validated['details'],
        ];
    }

    protected function storeAttachments(Event $event, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $existingCount = $event->attachments()->count();
        if (($existingCount + count($files)) > 3) {
            throw ValidationException::withMessages([
                'attachments' => 'A maximum of 3 attachments is allowed per event.',
            ]);
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $filePath = $file->store('event_attachments', 'public');

            $event->attachments()->create([
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize(),
            ]);
        }
    }

    protected function removeAttachments(Event $event, array $attachmentIds): void
    {
        if (empty($attachmentIds)) {
            return;
        }

        $attachments = $event->attachments()->whereIn('id', $attachmentIds)->get();

        foreach ($attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
    }
}
