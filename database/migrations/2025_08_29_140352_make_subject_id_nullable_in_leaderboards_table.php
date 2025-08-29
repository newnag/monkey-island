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
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });

        Schema::table('leaderboards', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('game_session_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });

        Schema::table('leaderboards', function (Blueprint $table) {
            $table->foreignId('subject_id')->after('game_session_id')->constrained()->onDelete('cascade');
        });
    }
};
