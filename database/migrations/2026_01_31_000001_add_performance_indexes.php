<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * เพิ่ม indexes เพื่อเพิ่มความเร็วในการ query
     */
    public function up(): void
    {
        // Index สำหรับตาราง questions
        Schema::table('questions', function (Blueprint $table) {
            // Index สำหรับการค้นหาคำถามตาม status (active/inactive)
            $table->index('status', 'idx_questions_status');
            
            // Composite index สำหรับการค้นหาคำถามตาม subject + status
            $table->index(['subject_id', 'status'], 'idx_questions_subject_status');
        });

        // Index สำหรับตาราง game_sessions
        Schema::table('game_sessions', function (Blueprint $table) {
            // Index สำหรับการค้นหาตาม session_id (ใช้บ่อยมาก)
            $table->index('session_id', 'idx_game_sessions_session_id');
            
            // Index สำหรับการค้นหาตาม status
            $table->index('status', 'idx_game_sessions_status');
            
            // Index สำหรับการค้นหาตาม is_completed
            $table->index('is_completed', 'idx_game_sessions_is_completed');
            
            // Composite index สำหรับการค้นหา session ที่ยังไม่เสร็จ
            $table->index(['session_id', 'is_completed'], 'idx_game_sessions_session_completed');
            
            // Index สำหรับการค้นหาตาม mode
            $table->index('mode', 'idx_game_sessions_mode');
            
            // Composite index สำหรับ player + status
            $table->index(['player_id', 'status'], 'idx_game_sessions_player_status');
        });

        // Index สำหรับตาราง leaderboards
        Schema::table('leaderboards', function (Blueprint $table) {
            // Index สำหรับการค้นหาตาม mode
            $table->index('mode', 'idx_leaderboards_mode');
            
            // Index สำหรับการเรียงตาม score (descending queries)
            $table->index('score', 'idx_leaderboards_score');
            
            // Index สำหรับการเรียงตาม time_taken
            $table->index('time_taken', 'idx_leaderboards_time_taken');
            
            // Composite index สำหรับการเรียงลำดับ leaderboard
            $table->index(['score', 'time_taken'], 'idx_leaderboards_score_time');
            
            // Composite index สำหรับการกรองตาม mode + subject
            $table->index(['mode', 'subject_id'], 'idx_leaderboards_mode_subject');
            
            // Composite index สำหรับการกรองและเรียงลำดับ
            $table->index(['mode', 'score', 'time_taken'], 'idx_leaderboards_mode_score_time');
        });

        // Index สำหรับตาราง players
        Schema::table('players', function (Blueprint $table) {
            // Index สำหรับการค้นหาตาม player_code (ใช้บ่อยมากจาก Cookie)
            $table->index('player_code', 'idx_players_player_code');
        });

        // Index สำหรับตาราง subjects
        Schema::table('subjects', function (Blueprint $table) {
            // Index สำหรับการค้นหาตาม status
            $table->index('status', 'idx_subjects_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_status');
            $table->dropIndex('idx_questions_subject_status');
        });

        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_game_sessions_session_id');
            $table->dropIndex('idx_game_sessions_status');
            $table->dropIndex('idx_game_sessions_is_completed');
            $table->dropIndex('idx_game_sessions_session_completed');
            $table->dropIndex('idx_game_sessions_mode');
            $table->dropIndex('idx_game_sessions_player_status');
        });

        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropIndex('idx_leaderboards_mode');
            $table->dropIndex('idx_leaderboards_score');
            $table->dropIndex('idx_leaderboards_time_taken');
            $table->dropIndex('idx_leaderboards_score_time');
            $table->dropIndex('idx_leaderboards_mode_subject');
            $table->dropIndex('idx_leaderboards_mode_score_time');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_player_code');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_status');
        });
    }
};
