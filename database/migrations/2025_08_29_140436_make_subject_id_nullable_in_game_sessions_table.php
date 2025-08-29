<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });

        Schema::table('game_sessions', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('player_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });

        Schema::table('game_sessions', function (Blueprint $table) {
            $table->foreignId('subject_id')->after('player_id')->constrained()->onDelete('cascade');
        });
    }
};
