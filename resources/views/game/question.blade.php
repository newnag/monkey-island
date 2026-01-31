@extends('game.layout')

@section('title', 'เกมตอบคำถาม')

@push('styles')
<style>
    /* Custom Animations */
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes timerPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .animate-slide-up { animation: slideInUp 0.5s ease-out forwards; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    .animate-scale-in { animation: scaleIn 0.3s ease-out forwards; }
    .timer-warning { animation: timerPulse 0.5s ease-in-out infinite; }
    
    /* Glassmorphism Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
    }
    
    /* Option Card Styles */
    .option-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .option-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s ease;
    }
    .option-card:hover::before {
        left: 100%;
    }
    .option-card:hover {
        transform: translateX(8px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
    }
    .option-card.selected {
        transform: translateX(8px);
        border-color: #6366f1;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        box-shadow: 0 10px 40px rgba(99, 102, 241, 0.25);
    }
    .option-card.selected .option-badge {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }
    .option-card.correct {
        border-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .option-card.correct .option-badge {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    .option-card.wrong {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .option-card.wrong .option-badge {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    .option-card.disabled-option {
        opacity: 0.4;
        pointer-events: none;
        filter: grayscale(50%);
    }
    
    /* Helper Button Styles */
    .helper-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .helper-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }
    .helper-card:hover:not(:disabled)::after {
        width: 200px;
        height: 200px;
    }
    .helper-card:hover:not(:disabled) {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }
    .helper-card:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(30%);
    }
    .helper-card:disabled::before {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        background: rgba(0,0,0,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }
    
    /* Progress Ring */
    .progress-ring {
        transform: rotate(-90deg);
    }
    .progress-ring-circle {
        transition: stroke-dashoffset 0.5s ease;
    }
    
    /* Timer Styles */
    .timer-container {
        position: relative;
    }
    .timer-bg {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    }
    .timer-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 640px) {
        .question-text { font-size: 1.125rem; line-height: 1.75rem; }
        .option-text { font-size: 0.95rem; }
        .helper-card { padding: 0.75rem; }
        .helper-icon { font-size: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div id="game-container" class="min-h-screen px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
    <div class="max-w-4xl mx-auto">
        
        <!-- Game Header Card -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 mb-4 sm:mb-6 animate-slide-up">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                
                <!-- Left: Progress Info -->
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-5 w-full sm:w-auto">
                    <!-- Question Number -->
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <svg class="progress-ring w-14 h-14 sm:w-16 sm:h-16" viewBox="0 0 60 60">
                                <circle class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none" r="26" cx="30" cy="30"/>
                                <circle class="progress-ring-circle text-indigo-500" stroke="currentColor" stroke-width="4" fill="none" r="26" cx="30" cy="30"
                                        stroke-dasharray="{{ 2 * 3.14159 * 26 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 26 * (1 - (($currentQuestionIndex + 1) / $totalQuestions)) }}"
                                        stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm sm:text-base font-bold text-indigo-600" id="current-question">{{ $currentQuestionIndex + 1 }}</span>
                            </div>
                        </div>
                        <div class="text-center sm:text-left">
                            <div class="text-xs sm:text-sm text-gray-500 font-medium">คำถามที่</div>
                            <div class="text-base sm:text-lg font-bold text-gray-800">
                                <span id="current-question-display">{{ $currentQuestionIndex + 1 }}</span>/<span class="text-gray-400">{{ $totalQuestions }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Score Badge -->
                    <div class="flex items-center gap-2 bg-gradient-to-r from-amber-50 to-yellow-50 px-4 py-2 rounded-xl border border-amber-200">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-star text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <div class="text-xs text-amber-600 font-medium">คะแนน</div>
                            <div class="text-lg sm:text-xl font-bold text-amber-700" id="current-score">{{ $session->total_score ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Timer -->
                <div class="timer-container">
                    <div id="timer" class="timer-bg flex items-center gap-3 px-5 py-3 rounded-2xl border-2 border-orange-200 shadow-md transition-all duration-300">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-orange-600 font-medium">เวลา</div>
                            <div class="text-2xl sm:text-3xl font-bold text-orange-700 tabular-nums" id="time-left">{{ $timeLimit }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4 sm:mt-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs sm:text-sm text-gray-500">ความคืบหน้า</span>
                    <span class="text-xs sm:text-sm font-semibold text-indigo-600">{{ round((($currentQuestionIndex + 1) / $totalQuestions) * 100) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5 overflow-hidden">
                    <div id="progress-bar" 
                         class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-700 ease-out"
                         style="width: {{ (($currentQuestionIndex + 1) / $totalQuestions) * 100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Helpers -->
        <div class="grid grid-cols-4 gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-6 animate-slide-up" style="animation-delay: 0.1s">
            <!-- 50:50 Helper -->
            <button id="helper-fifty-fifty" 
                    class="helper-card flex flex-col items-center justify-center p-2 sm:p-3 lg:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-lg" 
                    data-helper="fifty_fifty" {{ $helpersUsed['fifty_fifty'] ? 'disabled' : '' }}>
                <i class="fas fa-divide helper-icon text-xl sm:text-2xl lg:text-3xl mb-1 sm:mb-2 drop-shadow-md"></i>
                <div class="text-[10px] sm:text-xs lg:text-sm font-bold text-center leading-tight">50:50</div>
                <div class="hidden sm:block text-[10px] lg:text-xs opacity-80 mt-0.5">ลดตัวเลือก</div>
            </button>

            <!-- Ask Audience Helper -->
            <button id="helper-audience" 
                    class="helper-card flex flex-col items-center justify-center p-2 sm:p-3 lg:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-lg" 
                    data-helper="ask_audience" {{ $helpersUsed['ask_audience'] ? 'disabled' : '' }}>
                <i class="fas fa-users helper-icon text-xl sm:text-2xl lg:text-3xl mb-1 sm:mb-2 drop-shadow-md"></i>
                <div class="text-[10px] sm:text-xs lg:text-sm font-bold text-center leading-tight">ถามผู้ชม</div>
                <div class="hidden sm:block text-[10px] lg:text-xs opacity-80 mt-0.5">สถิติ</div>
            </button>

            <!-- Extra Time Helper -->
            <button id="helper-extra-time" 
                    class="helper-card flex flex-col items-center justify-center p-2 sm:p-3 lg:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg" 
                    data-helper="extra_time" {{ $helpersUsed['extra_time'] ? 'disabled' : '' }}>
                <i class="fas fa-hourglass-half helper-icon text-xl sm:text-2xl lg:text-3xl mb-1 sm:mb-2 drop-shadow-md"></i>
                <div class="text-[10px] sm:text-xs lg:text-sm font-bold text-center leading-tight">+30 วิ</div>
                <div class="hidden sm:block text-[10px] lg:text-xs opacity-80 mt-0.5">เพิ่มเวลา</div>
            </button>

            <!-- Skip Helper -->
            <button id="helper-skip" 
                    class="helper-card flex flex-col items-center justify-center p-2 sm:p-3 lg:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-lg" 
                    data-helper="skip" {{ $helpersUsed['skip'] ? 'disabled' : '' }}>
                <i class="fas fa-forward helper-icon text-xl sm:text-2xl lg:text-3xl mb-1 sm:mb-2 drop-shadow-md"></i>
                <div class="text-[10px] sm:text-xs lg:text-sm font-bold text-center leading-tight">ข้าม</div>
                <div class="hidden sm:block text-[10px] lg:text-xs opacity-80 mt-0.5">Skip</div>
            </button>
        </div>

        <!-- Question Card -->
        <div class="glass-card rounded-2xl sm:rounded-3xl shadow-xl mb-4 sm:mb-6 overflow-hidden animate-slide-up" style="animation-delay: 0.2s">
            <!-- Question Header -->
            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 p-4 sm:p-6">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-question text-white text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="question-text text-lg sm:text-xl lg:text-2xl font-bold text-white leading-relaxed">
                            {{ $question->question_text }}
                        </h2>
                    </div>
                </div>
            </div>
            
            <!-- Question Image -->
            @if($question->image_path)
            <div class="px-4 sm:px-6 pt-4 sm:pt-6">
                <div class="relative rounded-xl overflow-hidden shadow-lg bg-gray-100">
                    <img src="{{ Storage::url($question->image_path) }}" alt="Question Image" 
                         class="w-full h-auto max-h-48 sm:max-h-64 object-contain mx-auto">
                </div>
            </div>
            @endif

            <!-- Answer Options -->
            <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                @foreach(['option_a', 'option_b', 'option_c', 'option_d'] as $index => $option)
                @if($question->$option)
                <button class="option-card answer-option w-full text-left p-3 sm:p-4 lg:p-5 rounded-xl sm:rounded-2xl border-2 border-gray-200 
                              bg-white hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 group"
                        data-answer="{{ $option }}" data-index="{{ $index }}">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="option-badge flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 
                                    flex items-center justify-center font-bold text-gray-600 text-base sm:text-lg
                                    group-hover:from-indigo-100 group-hover:to-indigo-200 group-hover:text-indigo-600 transition-all duration-300">
                            {{ chr(65 + $index) }}
                        </div>
                        <div class="option-text flex-1 text-base sm:text-lg text-gray-700 group-hover:text-gray-900 transition-colors">
                            {{ $question->$option }}
                        </div>
                        <div class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full border-2 border-gray-200 
                                    group-hover:border-indigo-400 transition-colors flex items-center justify-center">
                            <i class="fas fa-check text-transparent group-hover:text-indigo-300 text-xs sm:text-sm transition-colors"></i>
                        </div>
                    </div>
                </button>
                @endif
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 animate-slide-up" style="animation-delay: 0.3s">
            <button id="submit-answer" 
                    class="flex-1 sm:flex-none order-1 sm:order-1 inline-flex items-center justify-center gap-2 sm:gap-3 
                           bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 
                           text-white font-bold py-4 sm:py-4 px-6 sm:px-10 rounded-xl sm:rounded-2xl shadow-lg 
                           hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 
                           disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-lg" 
                    disabled>
                <i class="fas fa-paper-plane text-lg sm:text-xl"></i>
                <span class="text-base sm:text-lg">ตอบคำถาม</span>
            </button>
            
            <button id="quit-game" 
                    class="order-2 sm:order-2 inline-flex items-center justify-center gap-2 
                           bg-white hover:bg-red-50 text-red-500 hover:text-red-600 font-semibold 
                           py-3 sm:py-4 px-5 sm:px-6 rounded-xl sm:rounded-2xl border-2 border-red-200 hover:border-red-300
                           shadow-md hover:shadow-lg transition-all duration-300">
                <i class="fas fa-sign-out-alt"></i>
                <span class="text-sm sm:text-base">ออกจากเกม</span>
            </button>
        </div>
        
    </div>
</div>

<!-- Helper Modals -->
@include('game.partials.helper-modals')

<!-- Game Data -->
<script>
    window.gameData = {
        sessionId: '{{ $sessionId }}',
        questionId: {{ $question->id }},
        correctAnswer: 'option_{{ $question->shuffled_correct_answer }}',
        currentQuestionIndex: {{ $currentQuestionIndex }},
        totalQuestions: {{ $totalQuestions }},
        timeLimit: {{ $timeLimit }},
        helpersUsed: @json($helpersUsed),
        mode: '{{ $mode }}',
        csrfToken: '{{ csrf_token() }}'
    };
</script>

@push('scripts')
<script src="{{ asset('js/game-question.js') }}"></script>
@endpush

@endsection
