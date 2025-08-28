<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Player extends Model
{
    protected $fillable = [
        'player_code',
        'nickname',
        'ip_address'
    ];

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function leaderboards()
    {
        return $this->hasMany(Leaderboard::class);
    }

    public static function generatePlayerCode()
    {
        do {
            $code = str_pad(random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (self::where('player_code', $code)->exists());

        return $code;
    }

    public function getBestScore($subjectId = null, $mode = null)
    {
        $query = $this->leaderboards();
        
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($mode) {
            $query->where('mode', $mode);
        }
        
        return $query->orderByDesc('score')
                     ->orderBy('time_taken')
                     ->first();
    }
}
