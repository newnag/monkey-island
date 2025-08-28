<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Question;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Leaderboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_subjects' => Subject::count(),
            'total_questions' => Question::count(),
            'total_players' => Player::count(),
            'total_games' => GameSession::count(),
            'games_today' => GameSession::whereDate('created_at', today())->count(),
        ];

        // Recent games
        $recentGames = GameSession::with(['player', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        // Popular subjects
        $popularSubjects = Subject::withCount('gameSessions')
            ->orderBy('game_sessions_count', 'desc')
            ->limit(5)
            ->get();

        // Top players
        $topPlayers = Leaderboard::with('player')
            ->select('player_id')
            ->selectRaw('MAX(score) as best_score')
            ->selectRaw('MIN(time_taken) as best_time')
            ->groupBy('player_id')
            ->orderBy('best_score', 'desc')
            ->orderBy('best_time')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentGames', 'popularSubjects', 'topPlayers'));
    }
}
