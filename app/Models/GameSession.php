<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameSession extends Model
{
    protected $fillable = [
        'player_id',
        'subject_id',
        'session_id',
        'mode',
        'score',
        'total_questions',
        'correct_answers',
        'time_taken',
        'helpers_used',
        'answers',
        'started_at',
        'finished_at',
        'status',
        'is_completed',
        'current_question_index',
        'total_score',
        'helper_fifty_fifty',
        'helper_ask_audience',
        'helper_extra_time',
        'helper_skip',
        'completed_at'
    ];

    protected $casts = [
        'helpers_used' => 'array',
        'answers' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
        'helper_fifty_fifty' => 'boolean',
        'helper_ask_audience' => 'boolean',
        'helper_extra_time' => 'boolean',
        'helper_skip' => 'boolean',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function getAccuracyAttribute()
    {
        return $this->total_questions > 0 
            ? round(($this->correct_answers / $this->total_questions) * 100, 2) 
            : 0;
    }

    public function getFormattedTimeAttribute()
    {
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByMode($query, $mode)
    {
        return $query->where('mode', $mode);
    }
}
