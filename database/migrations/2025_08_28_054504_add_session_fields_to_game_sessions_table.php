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
            $table->string('session_id')->unique()->after('id');
            $table->boolean('is_completed')->default(false)->after('status');
            $table->integer('current_question_index')->default(0)->after('total_questions');
            $table->integer('total_score')->default(0)->after('score');
            $table->boolean('helper_fifty_fifty')->default(false)->after('helpers_used');
            $table->boolean('helper_ask_audience')->default(false)->after('helper_fifty_fifty');
            $table->boolean('helper_extra_time')->default(false)->after('helper_ask_audience');
            $table->boolean('helper_skip')->default(false)->after('helper_extra_time');
            $table->timestamp('completed_at')->nullable()->after('finished_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'session_id',
                'is_completed',
                'current_question_index',
                'total_score',
                'helper_fifty_fifty',
                'helper_ask_audience',
                'helper_extra_time',
                'helper_skip',
                'completed_at',
            ]);
        });
    }
};
