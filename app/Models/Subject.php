<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'max_questions',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function leaderboards()
    {
        return $this->hasMany(Leaderboard::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
