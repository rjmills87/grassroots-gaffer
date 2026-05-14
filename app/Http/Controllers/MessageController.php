<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Team;
use App\Models\User;
use App\Notifications\NewTeamAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function store(Request $request, Team $team)
    {
        if (auth()->user()->role !== 'coach' || $team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['user_id'] = auth()->id();

        $message = $team->messages()->create([
            'user_id' => $validated['user_id'],
            'message' => $validated['message'],
        ]);

        $this->storeAttachments($message, $request->file('attachments', []));

        $guardianIds = $team->players()
            ->whereNotNull('guardian_id')
            ->pluck('guardian_id')
            ->unique()
            ->values();

        if ($guardianIds->isNotEmpty()) {
            $recipients = User::query()->whereIn('id', $guardianIds)->get();
            $message->loadMissing('team');
            Notification::send($recipients, new NewTeamAnnouncementNotification($message));
        }

        return redirect()->route('announcements.index', ['team' => $team->id]);
    }

    public function update(Request $request, Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'removed_attachment_ids' => 'nullable|array',
            'removed_attachment_ids.*' => 'integer',
        ]);

        $message->update([
            'message' => $validated['message'],
        ]);
        $team = $message->team;

        $this->removeAttachments($message, $validated['removed_attachment_ids'] ?? []);
        $this->storeAttachments($message, $request->file('attachments', []));

        return redirect()->route('announcements.index', ['team' => $team->id]);
    }

    public function destroy(Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $team = $message->team;

        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $message->delete();

        return redirect()->route('announcements.index', ['team' => $team->id]);
    }

    public function downloadAttachment(MessageAttachment $messageAttachment)
    {
        if (! $this->userCanAccessMessage(auth()->user(), $messageAttachment->message)) {
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('public')->download($messageAttachment->file_path, $messageAttachment->original_name);
    }

    public function previewAttachment(MessageAttachment $messageAttachment)
    {
        if (! $this->userCanAccessMessage(auth()->user(), $messageAttachment->message)) {
            abort(403, 'Unauthorized action.');
        }

        return Storage::disk('public')->response(
            $messageAttachment->file_path,
            $messageAttachment->original_name,
            [
                'Content-Type' => $messageAttachment->mime_type,
                'Content-Disposition' => 'inline; filename="'.$messageAttachment->original_name.'"',
            ]
        );
    }

    protected function userCanAccessMessage(User $user, Message $message): bool
    {
        $team = $message->team;

        if ($user->role === 'coach' && $team->user_id === $user->id) {
            return true;
        }

        if ($user->role === 'guardian') {
            return $user->players()->where('team_id', $team->id)->exists();
        }

        return false;
    }

    protected function storeAttachments(Message $message, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $existingCount = $message->attachments()->count();
        if (($existingCount + count($files)) > 3) {
            throw ValidationException::withMessages([
                'attachments' => 'A maximum of 3 attachments is allowed per announcement.',
            ]);
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $filePath = $file->store('message_attachments', 'public');

            $message->attachments()->create([
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize(),
            ]);
        }
    }

    protected function removeAttachments(Message $message, array $attachmentIds): void
    {
        if (empty($attachmentIds)) {
            return;
        }

        $attachments = $message->attachments()->whereIn('id', $attachmentIds)->get();

        foreach ($attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
    }
}
