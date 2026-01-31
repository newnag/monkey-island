<?php

namespace App\Services;

use App\Models\Leaderboard;
use App\Models\Subject;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache keys
     */
    const SUBJECTS_ACTIVE = 'subjects.active';
    const SUBJECTS_ALL = 'subjects.all';
    const LEADERBOARD_PREFIX = 'leaderboard';

    /**
     * Cache TTL (in seconds)
     */
    const TTL_SUBJECTS = 3600; // 1 hour
    const TTL_LEADERBOARD = 300; // 5 minutes

    /**
     * Get cached active subjects with question count
     */
    public static function getActiveSubjects()
    {
        return Cache::remember(self::SUBJECTS_ACTIVE, self::TTL_SUBJECTS, function () {
            return Subject::active()
                ->withCount('questions')
                ->having('questions_count', '>', 0)
                ->get();
        });
    }

    /**
     * Get all subjects (for admin)
     */
    public static function getAllSubjects()
    {
        return Cache::remember(self::SUBJECTS_ALL, self::TTL_SUBJECTS, function () {
            return Subject::withCount('questions')->get();
        });
    }

    /**
     * Get cached leaderboard
     */
    public static function getLeaderboard($mode = 'ranking', $subjectId = null, $limit = 100)
    {
        $key = self::LEADERBOARD_PREFIX . ".{$mode}." . ($subjectId ?? 'all') . ".{$limit}";

        return Cache::remember($key, self::TTL_LEADERBOARD, function () use ($mode, $subjectId, $limit) {
            $query = Leaderboard::with(['player', 'subject'])
                ->where('mode', $mode)
                ->orderByDesc('score')
                ->orderBy('time_taken');

            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Clear subject cache when subjects are updated
     */
    public static function clearSubjectCache()
    {
        Cache::forget(self::SUBJECTS_ACTIVE);
        Cache::forget(self::SUBJECTS_ALL);
    }

    /**
     * Clear leaderboard cache
     */
    public static function clearLeaderboardCache($mode = null, $subjectId = null)
    {
        if ($mode && $subjectId) {
            Cache::forget(self::LEADERBOARD_PREFIX . ".{$mode}.{$subjectId}.100");
        } elseif ($mode) {
            // Clear all leaderboard cache for this mode
            Cache::forget(self::LEADERBOARD_PREFIX . ".{$mode}.all.100");
        } else {
            // Clear all leaderboard cache (expensive but rare operation)
            $modes = ['ranking', 'single', 'mixed', 'fun'];
            foreach ($modes as $m) {
                Cache::forget(self::LEADERBOARD_PREFIX . ".{$m}.all.100");
            }
        }
    }

    /**
     * Clear all game-related cache
     */
    public static function clearAllCache()
    {
        self::clearSubjectCache();
        self::clearLeaderboardCache();
    }
}
