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
        // ใช้ single query update แทนการ loop เพื่อ performance ที่ดีขึ้น
        $table = (new self)->getTable();
        
        // Build conditions
        $conditions = [];
        $bindings = [];
        
        if ($subjectId) {
            $conditions[] = 'subject_id = ?';
            $bindings[] = $subjectId;
        } else {
            $conditions[] = 'subject_id IS NULL';
        }
        
        if ($mode) {
            $conditions[] = 'mode = ?';
            $bindings[] = $mode;
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Use database-specific ranking query
        $driver = \DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL/MariaDB: Use user variables for ranking
            \DB::statement('SET @rank := 0');
            \DB::update("
                UPDATE {$table} AS l1
                JOIN (
                    SELECT id, @rank := @rank + 1 AS new_rank
                    FROM {$table}
                    WHERE {$whereClause}
                    ORDER BY score DESC, time_taken ASC
                ) AS l2 ON l1.id = l2.id
                SET l1.rank = l2.new_rank
            ", $bindings);
        } else {
            // SQLite/PostgreSQL: Fallback to efficient batch update
            $leaderboards = self::query()
                ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
                ->when(!$subjectId, fn($q) => $q->whereNull('subject_id'))
                ->when($mode, fn($q) => $q->where('mode', $mode))
                ->orderByDesc('score')
                ->orderBy('time_taken')
                ->pluck('id')
                ->toArray();
            
            if (!empty($leaderboards)) {
                $cases = [];
                $ids = [];
                foreach ($leaderboards as $rank => $id) {
                    $cases[] = "WHEN {$id} THEN " . ($rank + 1);
                    $ids[] = $id;
                }
                
                $caseStatement = implode(' ', $cases);
                $idsString = implode(',', $ids);
                
                \DB::update("
                    UPDATE {$table}
                    SET rank = CASE id {$caseStatement} END
                    WHERE id IN ({$idsString})
                ");
            }
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
