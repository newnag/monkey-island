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
            $table->integer('correct_answers')->default(0)->after('score');
            $table->integer('total_questions')->default(0)->after('correct_answers');
            $table->integer('time_used')->default(0)->after('time_taken');
            $table->decimal('accuracy', 5, 2)->default(0)->after('time_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropColumn(['correct_answers', 'total_questions', 'time_used', 'accuracy']);
        });
    }
};
