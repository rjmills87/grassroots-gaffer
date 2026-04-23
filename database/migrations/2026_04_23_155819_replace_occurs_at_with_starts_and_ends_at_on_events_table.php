<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('occurs_at', 'starts_at');
            $table->dateTime('ends_at')->nullable();
        });

        DB::table('events')
            ->select(['id', 'starts_at'])
            ->orderBy('id')
            ->chunkById(100, function ($events): void {
                foreach ($events as $event) {
                    DB::table('events')
                        ->where('id', $event->id)
                        ->update([
                            'ends_at' => Carbon::parse($event->starts_at)->addMinutes(90),
                        ]);
                }
            });

        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('starts_at', 'occurs_at');
            $table->dropColumn('ends_at');
        });
    }
};
