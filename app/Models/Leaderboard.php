<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $fillable = [
        'player_id',
        'game_session_id',
        'subject_id',
        'mode',
        'score',
        'correct_answers',
        'total_questions',
        'time_taken',
        'time_used',
        'accuracy',
        'rank',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getFormattedTimeAttribute()
    {
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public static function updateRanks($subjectId = null, $mode = null)
    {
        $query = self::query();

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($mode) {
            $query->where('mode', $mode);
        }

        $leaderboards = $query->orderByDesc('score')
            ->orderBy('time_taken')
            ->get();

        $rank = 1;
        foreach ($leaderboards as $leaderboard) {
            $leaderboard->update(['rank' => $rank]);
            $rank++;
        }
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByMode($query, $mode)
    {
        return $query->where('mode', $mode);
    }

    public function scopeTopRankings($query, $limit = 10)
    {
        return $query->orderBy('rank')->limit($limit);
    }
}
