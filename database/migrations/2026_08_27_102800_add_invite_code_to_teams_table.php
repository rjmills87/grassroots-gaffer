<?php

use App\Support\InviteCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('invite_code', 12)->nullable()->unique()->after('team_badge_url');
            $table->timestamp('invite_code_generated_at')->nullable()->after('invite_code');
            $table->timestamp('invite_code_expires_at')->nullable()->after('invite_code_generated_at');
        });

        DB::table('teams')->whereNull('invite_code')->orderBy('id')->each(function (object $team): void {
            DB::table('teams')->where('id', $team->id)->update([
                'invite_code' => InviteCode::unique(),
                'invite_code_generated_at' => now(),
                'invite_code_expires_at' => InviteCode::expiresAt(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['invite_code']);
            $table->dropColumn([
                'invite_code',
                'invite_code_generated_at',
                'invite_code_expires_at',
            ]);
        });
    }
};
