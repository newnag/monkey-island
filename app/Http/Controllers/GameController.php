<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Leaderboard;
use App\Models\Player;
use App\Models\Question;
use App\Models\Subject;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    public function welcome()
    {
        // Check if player has nickname
        if (! $this->playerHasNickname()) {
            return redirect()->route('player.nickname-setup');
        }

        // ใช้ cached subjects แทนการ query ตรง
        $subjects = CacheService::getActiveSubjects();

        return view('game.welcome', compact('subjects'));
    }

    public function index()
    {
        // Check if player has nickname
        if (! $this->playerHasNickname()) {
            return redirect()->route('player.nickname-setup');
        }

        // Generate player if not exists
        $playerId = $this->getOrCreatePlayer();

        // ใช้ cached subjects แทนการ query ตรง
        $subjects = CacheService::getActiveSubjects();

        return view('game.index', compact('subjects'));
    }

    private function playerHasNickname()
    {
        $playerCode = Cookie::get('player_code');

        if (! $playerCode) {
            return false;
        }

        $player = Player::where('player_code', $playerCode)->first();

        if (! $player || empty($player->nickname)) {
            return false;
        }

        return true;
    }

    public function play($mode)
    {
        $validModes = ['single', 'mixed', 'fun', 'ranking'];

        if (! in_array($mode, $validModes)) {
            return redirect()->route('game.index')->with('error', 'โหมดไม่ถูกต้อง');
        }

        // For ranking mode, show special ranking start page
        if ($mode === 'ranking') {
            return $this->showRankingStart();
        }

        // ใช้ cached subjects
        $subjects = CacheService::getActiveSubjects();
        $questionCount = $this->getQuestionCountByMode($mode);

        return view('game.play', compact('mode', 'subjects', 'questionCount'));
    }

    private function showRankingStart()
    {
        // ใช้ cached subjects ที่มีคำถามแล้ว
        $availableSubjects = CacheService::getActiveSubjects();

        return view('game.ranking-start', compact('availableSubjects'));
    }

    public function startRanking(Request $request)
    {
        // ใช้ cached subjects
        $subjects = CacheService::getActiveSubjects();

        if ($subjects->isEmpty()) {
            return redirect()->route('game.index')
                ->with('error', 'ไม่มีวิชาที่มีข้อสอบสำหรับโหมด Ranking');
        }

        $playerId = $this->getOrCreatePlayer();
        $totalQuestions = 25;
        $questionsPerSubject = intval($totalQuestions / $subjects->count());
        $remainingQuestions = $totalQuestions % $subjects->count();

        $allQuestions = collect();
        $subjectNames = [];
        $subjectIds = $subjects->pluck('id')->toArray();

        // ดึงคำถามทั้งหมดใน query เดียว แล้วกรุ๊ปทีหลัง
        $allQuestionsFromDb = Question::whereIn('subject_id', $subjectIds)
            ->active()
            ->with('subject:id,name') // Eager load เฉพาะ columns ที่ต้องการ
            ->inRandomOrder()
            ->get()
            ->groupBy('subject_id');

        // Get questions from each subject equally
        foreach ($subjects as $index => $subject) {
            $questionsToTake = $questionsPerSubject;

            // Add remaining questions to first few subjects
            if ($index < $remainingQuestions) {
                $questionsToTake++;
            }

            $questions = $allQuestionsFromDb->get($subject->id, collect())->take($questionsToTake);

            if ($questions->count() > 0) {
                $allQuestions = $allQuestions->merge($questions);
                $subjectNames[] = $subject->name;
            }
        }

        // Shuffle all questions together
        $allQuestions = $allQuestions->shuffle();

        if ($allQuestions->count() < 10) {
            return redirect()->route('game.index')
                ->with('error', 'ข้อสอบไม่เพียงพอสำหรับโหมด Ranking (ต้องการอย่างน้อย 10 ข้อ)');
        }

        // Limit to exactly 25 questions or available questions
        $finalQuestions = $allQuestions->take(25);

        // Create game session (use null for subject_id since it's mixed subjects)
        $gameSession = GameSession::create([
            'player_id' => $playerId,
            'subject_id' => null, // Mixed subjects
            'session_id' => uniqid('game_', true),
            'mode' => 'ranking',
            'total_questions' => $finalQuestions->count(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Store questions in session with shuffled options
        $questionsWithShuffledOptions = [];
        foreach ($finalQuestions as $q) {
            $shuffledData = $q->getShuffledOptionsAndCorrectAnswer();

            $questionsWithShuffledOptions[] = [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'option_a' => $shuffledData['options']['option_a'],
                'option_b' => $shuffledData['options']['option_b'],
                'option_c' => $shuffledData['options']['option_c'],
                'option_d' => $shuffledData['options']['option_d'],
                'original_correct_answer' => $q->correct_answer,
                'shuffled_correct_answer' => $shuffledData['correct_answer'],
                'difficulty' => $q->difficulty,
                'image_path' => $q->image_path,
                'subject_name' => $q->subject->name,
            ];
        }

        Session::put("game_session_{$gameSession->session_id}", [
            'questions' => $questionsWithShuffledOptions,
            'current_question' => 0,
            'score' => 0,
            'correct_answers' => 0,
            'answers' => [],
            'helpers_used' => [],
            'start_time' => time(),
        ]);

        return redirect()->route('game.question', $gameSession->session_id)
            ->with('success', 'โหมด Ranking เริ่มแล้ว! วิชาผสม: '.implode(', ', $subjectNames));
    }

    public function start(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'mode' => 'required|in:single,mixed,fun,ranking',
        ]);

        $playerId = $this->getOrCreatePlayer();
        $subject = Subject::findOrFail($request->subject_id);

        // Get questions based on mode
        $questionCount = $this->getQuestionCountByMode($request->mode);
        
        // เลือกเฉพาะ columns ที่ต้องการ และใช้ eager load subject
        $questions = Question::where('subject_id', $subject->id)
            ->active()
            ->select(['id', 'subject_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'image_path'])
            ->with('subject:id,name')
            ->inRandomOrder()
            ->limit($questionCount)
            ->get();

        if ($questions->count() < $questionCount) {
            return back()->with('error', 'ข้อสอบไม่เพียงพอสำหรับโหมดนี้');
        }

        // Create game session
        $gameSession = GameSession::create([
            'player_id' => $playerId,
            'subject_id' => $subject->id,
            'session_id' => uniqid('game_', true),
            'mode' => $request->mode,
            'total_questions' => $questions->count(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Store questions in session with shuffled options
        $questionsWithShuffledOptions = [];
        foreach ($questions as $q) {
            $shuffledData = $q->getShuffledOptionsAndCorrectAnswer();

            $questionsWithShuffledOptions[] = [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'option_a' => $shuffledData['options']['option_a'],
                'option_b' => $shuffledData['options']['option_b'],
                'option_c' => $shuffledData['options']['option_c'],
                'option_d' => $shuffledData['options']['option_d'],
                'original_correct_answer' => $q->correct_answer,
                'shuffled_correct_answer' => $shuffledData['correct_answer'],
                'difficulty' => $q->difficulty,
                'image_path' => $q->image_path,
            ];
        }

        Session::put("game_session_{$gameSession->session_id}", [
            'questions' => $questionsWithShuffledOptions,
            'current_question' => 0,
            'score' => 0,
            'correct_answers' => 0,
            'answers' => [],
            'helpers_used' => [],
            'start_time' => time(),
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $gameSession->session_id,
            'redirect' => route('game.question', $gameSession->session_id),
        ]);
    }

    public function question($sessionId)
    {
        $session = GameSession::with(['player', 'subject'])
            ->where('session_id', $sessionId)
            ->where('is_completed', false)
            ->first();

        if (! $session) {
            return redirect()->route('game.index')->with('error', 'ไม่พบเซสชันเกม');
        }

        // Get current question based on session progress
        $currentQuestionIndex = $session->current_question_index;
        $sessionData = Session::get("game_session_{$sessionId}");

        if (! $sessionData || ! isset($sessionData['questions'])) {
            return redirect()->route('game.index')->with('error', 'ไม่พบข้อมูลเกม');
        }

        $questions = $sessionData['questions'];

        if ($currentQuestionIndex >= count($questions)) {
            return $this->completeGame($session);
        }

        $questionData = $questions[$currentQuestionIndex];

        // Create question object from session data
        $question = (object) $questionData;

        $timeLimit = $this->getTimeLimitByMode($session->mode);
        $totalQuestions = $session->total_questions;
        $mode = $session->mode;
        $player = $session->player;

        // Get helpers used
        $helpersUsed = [
            'fifty_fifty' => $session->helper_fifty_fifty,
            'ask_audience' => $session->helper_ask_audience,
            'extra_time' => $session->helper_extra_time,
            'skip' => $session->helper_skip,
        ];

        return view('game.question', compact(
            'session',
            'question',
            'currentQuestionIndex',
            'totalQuestions',
            'timeLimit',
            'helpersUsed',
            'mode',
            'player',
            'sessionId'
        ));
    }

    public function result($sessionId)
    {
        $gameSession = GameSession::with(['subject', 'player'])->findOrFail($sessionId);
        $sessionData = Session::get("game_session_{$sessionId}");

        if (! $sessionData || $gameSession->status === 'completed') {
            // Load from database if session completed
            $leaderboard = Leaderboard::where('game_session_id', $sessionId)->first();

            return view('game.result', compact('gameSession', 'leaderboard'));
        }

        // Complete the game session
        $timeTaken = time() - $sessionData['start_time'];

        $gameSession->update([
            'score' => $sessionData['score'],
            'correct_answers' => $sessionData['correct_answers'],
            'time_taken' => $timeTaken,
            'answers' => $sessionData['answers'],
            'helpers_used' => $sessionData['helpers_used'],
            'finished_at' => now(),
            'status' => 'completed',
        ]);

        // Add to leaderboard
        $leaderboard = Leaderboard::create([
            'player_id' => $gameSession->player_id,
            'game_session_id' => $gameSession->id,
            'subject_id' => $gameSession->subject_id,
            'mode' => $gameSession->mode,
            'score' => $sessionData['score'],
            'time_taken' => $timeTaken,
        ]);

        // Update ranks
        Leaderboard::updateRanks($gameSession->subject_id, $gameSession->mode);

        // Clear session
        Session::forget("game_session_{$sessionId}");

        return view('game.result', compact('gameSession', 'leaderboard'));
    }

    public function leaderboard(Request $request)
    {
        $subjectId = $request->get('subject');
        $mode = $request->get('mode', 'ranking');

        // ใช้ cached leaderboard data
        $leaderboards = CacheService::getLeaderboard($mode, $subjectId, 100);
        
        // ใช้ cached subjects
        $subjects = CacheService::getActiveSubjects();

        return view('game.leaderboard', compact('leaderboards', 'subjects', 'subjectId', 'mode'));
    }

    private function getOrCreatePlayer()
    {
        $playerCode = Cookie::get('player_code');

        if ($playerCode) {
            $player = Player::where('player_code', $playerCode)->first();
            if ($player) {
                return $player->id;
            }
        }

        // Should not reach here if nickname setup is properly enforced
        // But create player anyway with empty nickname as fallback
        $player = Player::create([
            'player_code' => Player::generatePlayerCode(),
            'nickname' => null,
            'ip_address' => request()->ip(),
        ]);

        Cookie::queue('player_code', $player->player_code, 43200); // 30 days

        return $player->id;
    }

    private function getQuestionCountByMode($mode)
    {
        return match ($mode) {
            'single' => 10,
            'mixed' => 20,
            'fun' => 15,
            'ranking' => 25,
            default => 10
        };
    }

    // Helper methods for game logic
    private function getQuestions($subjectId, $mode, $totalQuestions)
    {
        $query = Question::active();

        if ($subjectId && $mode !== 'mixed') {
            $query->where('subject_id', $subjectId);
        }

        return $query->inRandomOrder()->take($totalQuestions)->get();
    }

    private function getTimeLimitByMode($mode)
    {
        return match ($mode) {
            'single' => 30,
            'mixed' => 25,
            'fun' => 60,
            'ranking' => 20,
            default => 30
        };
    }

    private function completeGame($session)
    {
        $session->update([
            'is_completed' => true,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        return redirect()->route('game.results', $session->session_id);
    }

    // API endpoints for game interactions
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'question_id' => 'required|integer',
            'selected_answer' => 'nullable|string|in:option_a,option_b,option_c,option_d',
            'time_left' => 'required|integer',
            'time_up' => 'boolean',
        ]);

        // ใช้ select เฉพาะ columns ที่ต้องการ และ cache session lookup
        $session = GameSession::where('session_id', $request->session_id)
            ->select(['id', 'session_id', 'player_id', 'mode', 'total_questions', 'current_question_index', 'total_score', 'correct_answers', 'started_at'])
            ->first();
            
        if (! $session) {
            return response()->json(['success' => false, 'message' => 'ไม่พบเซสชันเกม']);
        }

        // Get question data from session (which includes shuffled options)
        $sessionData = Session::get("game_session_{$request->session_id}");
        if (! $sessionData || ! isset($sessionData['questions'])) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเกม']);
        }

        $currentQuestionIndex = $session->current_question_index;
        if (! isset($sessionData['questions'][$currentQuestionIndex])) {
            return response()->json(['success' => false, 'message' => 'ไม่พบคำถามปัจจุบัน']);
        }

        $questionData = $sessionData['questions'][$currentQuestionIndex];

        // Check if the question ID matches
        if ($questionData['id'] != $request->question_id) {
            return response()->json(['success' => false, 'message' => 'คำถามไม่ตรงกัน']);
        }

        // Convert selected answer (option_a, option_b, option_c, option_d) to letter format (a, b, c, d)
        $selectedAnswerLetter = '';
        if ($request->selected_answer) {
            $selectedAnswerLetter = str_replace('option_', '', $request->selected_answer);
        }

        // Check if answer is correct by comparing with shuffled correct answer
        $isCorrect = $selectedAnswerLetter === $questionData['shuffled_correct_answer'];
        $pointsEarned = $isCorrect ? 1 : 0;

        // Check if game is completed
        $newQuestionIndex = $currentQuestionIndex + 1;
        $gameCompleted = $newQuestionIndex >= $session->total_questions;

        // รวม update เป็น query เดียว แทนที่จะ increment หลายครั้ง
        $updateData = [
            'current_question_index' => $newQuestionIndex,
            'total_score' => $session->total_score + $pointsEarned,
        ];
        
        if ($isCorrect) {
            $updateData['correct_answers'] = $session->correct_answers + 1;
        }

        if ($gameCompleted) {
            $updateData['is_completed'] = true;
            $updateData['completed_at'] = now();
            $updateData['status'] = 'completed';
        }

        // Single update query
        GameSession::where('id', $session->id)->update($updateData);

        // Update leaderboard for ranking mode (async-like, after response conceptually)
        if ($gameCompleted && $session->mode === 'ranking') {
            // Reload session with new data for leaderboard update
            $session->refresh();
            $session->total_score = $updateData['total_score'];
            $session->correct_answers = $updateData['correct_answers'] ?? $session->correct_answers;
            $session->completed_at = $updateData['completed_at'];
            
            Log::info('Updating leaderboard for session: '.$session->id);
            $this->updateLeaderboard($session);
        }

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'total_score' => $updateData['total_score'],
            'game_completed' => $gameCompleted,
            'message' => $isCorrect ? 'ตอบถูก!' : 'ตอบผิด!',
        ]);
    }

    public function useHelper(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'helper_type' => 'required|string|in:fifty_fifty,ask_audience,extra_time,skip',
            'question_id' => 'required|integer',
        ]);

        $helperField = 'helper_'.$request->helper_type;
        
        // ใช้ single query update with check
        $updated = GameSession::where('session_id', $request->session_id)
            ->where($helperField, false)
            ->update([$helperField => true]);
        
        if (!$updated) {
            // Either session not found or helper already used
            $session = GameSession::where('session_id', $request->session_id)->first();
            if (!$session) {
                return response()->json(['success' => false, 'message' => 'ไม่พบเซสชันเกม']);
            }
            return response()->json(['success' => false, 'message' => 'ใช้ตัวช่วยนี้แล้ว']);
        }

        $data = [];
        $message = '';

        switch ($request->helper_type) {
            case 'fifty_fifty':
                // Get question data from session (which includes shuffled options)
                $sessionData = Session::get("game_session_{$request->session_id}");
                if (! $sessionData || ! isset($sessionData['questions'])) {
                    return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเกม']);
                }

                $session = GameSession::where('session_id', $request->session_id)
                    ->select(['current_question_index'])
                    ->first();
                    
                $currentQuestionIndex = $session->current_question_index;
                $questionData = $sessionData['questions'][$currentQuestionIndex];

                // Get shuffled correct answer (a, b, c, d format)
                $correctAnswerLetter = $questionData['shuffled_correct_answer'];
                $correctOption = 'option_'.$correctAnswerLetter;

                // Get all non-empty options
                $allOptions = ['option_a', 'option_b', 'option_c', 'option_d'];
                $availableOptions = array_filter($allOptions, function ($opt) use ($questionData) {
                    return ! empty(trim($questionData[$opt]));
                });

                // Get incorrect options (excluding correct answer)
                $incorrectOptions = array_filter($availableOptions, function ($opt) use ($correctOption) {
                    return $opt !== $correctOption;
                });

                // Randomly select 2 incorrect options to hide
                $incorrectOptionsArray = array_values($incorrectOptions);
                shuffle($incorrectOptionsArray);
                $hiddenOptions = array_slice($incorrectOptionsArray, 0, 2);

                $data = ['hidden_options' => $hiddenOptions];
                $message = 'ลดตัวเลือกเหลือ 2 ตัวเลือก';
                break;

            case 'ask_audience':
                // Get session data for shuffled questions
                $sessionData = Session::get("game_session_{$request->session_id}");
                if (! $sessionData || ! isset($sessionData['questions'])) {
                    return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเกม']);
                }

                $session = GameSession::where('session_id', $request->session_id)
                    ->select(['current_question_index'])
                    ->first();
                    
                $currentQuestionIndex = $session->current_question_index;
                $currentQuestionData = $sessionData['questions'][$currentQuestionIndex];
                $shuffledCorrectAnswerLetter = $currentQuestionData['shuffled_correct_answer'];
                $shuffledCorrectOption = 'option_'.$shuffledCorrectAnswerLetter;

                $availableOptions = ['option_a', 'option_b', 'option_c', 'option_d'];
                $availableOptions = array_filter($availableOptions, function ($opt) use ($currentQuestionData) {
                    return ! empty($currentQuestionData[$opt]);
                });

                // Generate realistic percentages (correct answer gets 70-85%)
                $percentages = [];
                $correctPercentage = rand(70, 85);
                $remainingPercentage = 100 - $correctPercentage;

                $incorrectOptions = array_filter($availableOptions, function ($opt) use ($shuffledCorrectOption) {
                    return $opt !== $shuffledCorrectOption;
                });
                $incorrectCount = count($incorrectOptions);

                // Initialize all available options with 0%
                foreach ($availableOptions as $option) {
                    $percentages[$option] = 0;
                }

                // Set correct answer percentage
                $percentages[$shuffledCorrectOption] = $correctPercentage;

                // Distribute remaining percentage among incorrect options
                if ($incorrectCount > 0) {
                    $basePercentage = intval($remainingPercentage / $incorrectCount);
                    $remainingAfterBase = $remainingPercentage - ($basePercentage * $incorrectCount);

                    foreach ($incorrectOptions as $option) {
                        $percentages[$option] = $basePercentage;
                    }

                    // Add remaining to random incorrect option
                    if ($remainingAfterBase > 0) {
                        $randomIncorrectOption = array_rand($incorrectOptions);
                        $percentages[$incorrectOptions[$randomIncorrectOption]] += $remainingAfterBase;
                    }
                }

                $data = ['percentages' => $percentages];
                $message = 'ดูผลการสำรวจจากผู้ชม';
                break;

            case 'extra_time':
                $data = ['extra_seconds' => 30];
                $message = 'เพิ่มเวลา 30 วินาที';
                break;

            case 'skip':
                $message = 'ข้ามคำถามนี้';
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function quitGame(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $session = GameSession::where('session_id', $request->session_id)->first();
        if ($session) {
            $session->update([
                'is_completed' => true,
                'status' => 'quit',
                'completed_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function nextQuestion($sessionId)
    {
        return redirect()->route('game.question', $sessionId);
    }

    public function results($sessionId)
    {
        $session = GameSession::with(['player', 'subject'])
            ->where('session_id', $sessionId)
            ->where('is_completed', true)
            ->first();

        if (! $session) {
            return redirect()->route('game.index')->with('error', 'ไม่พบผลเกม');
        }

        // Calculate statistics
        $correctAnswers = $session->correct_answers;
        $totalQuestions = $session->total_questions;
        $accuracy = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;

        $helpersUsedCount = collect([
            $session->helper_fifty_fifty,
            $session->helper_ask_audience,
            $session->helper_extra_time,
            $session->helper_skip,
        ])->filter()->count();

        // Calculate time used
        $timeUsed = $session->completed_at ?
            $session->completed_at->diffInSeconds($session->started_at) : 0;

        $averageTimePerQuestion = $totalQuestions > 0 ?
            round($timeUsed / $totalQuestions, 1) : 0;

        // Get leaderboard position if ranking mode
        $leaderboardPosition = null;
        $totalPlayers = null;
        if ($session->mode === 'ranking') {
            $leaderboard = Leaderboard::where('subject_id', $session->subject_id)
                ->where('mode', 'ranking')
                ->orderBy('score', 'desc')
                ->orderBy('time_used', 'asc')
                ->get();

            $position = $leaderboard->search(function ($item) use ($session) {
                return $item->player_id === $session->player_id;
            });

            if ($position !== false) {
                $leaderboardPosition = $position + 1;
                $totalPlayers = $leaderboard->count();
            }
        }

        // Mock question answers for review (simplified)
        $questionAnswers = [];
        for ($i = 0; $i < $totalQuestions; $i++) {
            $questionAnswers[] = [
                'question' => 'คำถามที่ '.($i + 1),
                'selected_answer' => 'option_a',
                'selected_option' => 'A',
                'correct_option' => 'A',
                'correct' => $i < $correctAnswers,
                'points' => $i < $correctAnswers ? 10 : 0,
                'time_used' => rand(10, 30),
            ];
        }

        return view('game.results', compact(
            'session',
            'correctAnswers',
            'totalQuestions',
            'accuracy',
            'helpersUsedCount',
            'averageTimePerQuestion',
            'leaderboardPosition',
            'totalPlayers',
            'questionAnswers'
        ));
    }

    private function updateLeaderboard($session)
    {
        Log::info('updateLeaderboard called', [
            'session_id' => $session->id,
            'player_id' => $session->player_id,
            'mode' => $session->mode,
            'total_score' => $session->total_score,
        ]);

        // Check if player already has a better score for ranking mode (mixed subjects)
        $existingRecord = Leaderboard::where('player_id', $session->player_id)
            ->where('mode', $session->mode)
            ->where('subject_id', $session->subject_id) // This can be null for mixed
            ->first();

        if (! $existingRecord || $session->total_score > $existingRecord->score) {
            Log::info('Creating/updating leaderboard entry');

            // Calculate time taken
            $timeTaken = $session->completed_at ?
                $session->completed_at->diffInSeconds($session->started_at) : 0;

            $leaderboard = Leaderboard::updateOrCreate(
                [
                    'player_id' => $session->player_id,
                    'subject_id' => $session->subject_id, // Can be null for mixed
                    'mode' => $session->mode,
                ],
                [
                    'game_session_id' => $session->id,
                    'score' => $session->total_score,
                    'correct_answers' => $session->correct_answers,
                    'total_questions' => $session->total_questions,
                    'time_taken' => $timeTaken,
                    'time_used' => $timeTaken,
                    'accuracy' => $session->total_questions > 0 ?
                        round(($session->correct_answers / $session->total_questions) * 100, 2) : 0,
                ]
            );

            Log::info('Leaderboard entry created/updated', ['leaderboard_id' => $leaderboard->id]);

            // Clear leaderboard cache หลังจาก update
            CacheService::clearLeaderboardCache($session->mode, $session->subject_id);
        } else {
            Log::info('Existing record has better score, not updating');
        }
    }
}
