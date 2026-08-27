<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InviteCode
{
    public static function generate(): string
    {
        $characters = (string) config('invites.invite_code_characters');
        $length = (int) config('invites.invite_code_length');
        $maxIndex = strlen($characters) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $maxIndex)];
        }

        return $code;
    }

    public static function unique(): string
    {
        do {
            $code = self::generate();
        } while (DB::table('teams')->where('invite_code', $code)->exists());

        return $code;
    }

    public static function normalize(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }

    public static function expiresAt(): Carbon
    {
        return now()->addDays((int) config('invites.invite_code_ttl_days'));
    }

    public static function joinRequestExpiresAt(): Carbon
    {
        return now()->addDays((int) config('invites.join_request_ttl_days'));
    }
}
