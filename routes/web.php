<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

// Public Game Routes
Route::get('/', [GameController::class, 'welcome'])->name('home');
Route::get('/game', [GameController::class, 'index'])->name('game.index');
Route::get('/game/play/{mode}', [GameController::class, 'play'])->name('game.play');
Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
Route::post('/game/start-ranking', [GameController::class, 'startRanking'])->name('game.start-ranking');
Route::get('/game/question/{sessionId}', [GameController::class, 'question'])->name('game.question');
Route::get('/game/next-question/{sessionId}', [GameController::class, 'nextQuestion'])->name('game.next-question');
Route::get('/game/results/{sessionId}', [GameController::class, 'results'])->name('game.results');
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('game.leaderboard');

// Player Routes
Route::get('/nickname-setup', [PlayerController::class, 'showNicknameSetup'])->name('player.nickname-setup');
Route::post('/nickname-setup', [PlayerController::class, 'setupNickname'])->name('player.setup-nickname');
Route::get('/edit-nickname', [PlayerController::class, 'editNickname'])->name('player.edit-nickname');
Route::post('/edit-nickname', [PlayerController::class, 'updateNickname'])->name('player.update-nickname');

// Debug route (remove in production)
Route::get('/clear-player', function () {
    Cookie::queue(Cookie::forget('player_code'));
    Cookie::queue(Cookie::forget('player_nickname'));

    return redirect()->route('home')->with('success', 'ข้อมูลผู้เล่นถูกล้างแล้ว');
})->name('player.clear');

// Game API Routes
Route::post('/game/submit-answer', [GameController::class, 'submitAnswer'])->name('game.submit-answer');
Route::post('/game/use-helper', [GameController::class, 'useHelper'])->name('game.use-helper');
Route::post('/game/quit', [GameController::class, 'quitGame'])->name('game.quit');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Subject Management
    Route::resource('subjects', SubjectController::class);

    // Question Management
    Route::resource('questions', QuestionController::class);
    Route::get('subjects/{subject}/questions', [QuestionController::class, 'bySubject'])->name('questions.by-subject');
    Route::post('questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::get('questions/export/{subject?}', [QuestionController::class, 'export'])->name('questions.export');
});

// Default Laravel Auth Routes
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
