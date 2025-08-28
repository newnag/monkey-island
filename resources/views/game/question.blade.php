@extends('game.layout')

@section('title', 'เกมตอบคำถาม')

@section('content')
<div id="game-container" class="min-h-screen">
    <!-- Game Header -->
    <div class="bg-white/95 backdrop-blur-sm shadow-lg mb-6 rounded-xl p-4">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
            <!-- Question Progress -->
            <div class="flex items-center gap-4">
                <div class="text-lg font-bold text-primary">
                    คำถามที่ <span id="current-question">{{ $currentQuestionIndex + 1 }}</span> 
                    จาก {{ $totalQuestions }}
                </div>
                <div class="w-48 bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-gradient-to-r from-primary to-secondary h-2.5 rounded-full transition-all duration-500" 
                         style="width: {{ (($currentQuestionIndex + 1) / $totalQuestions) * 100 }}%"></div>
                </div>
            </div>

            <!-- Timer -->
            <div class="flex items-center gap-4">
                <div class="text-lg font-bold text-accent">
                    คะแนน: <span id="current-score">{{ $session->total_score ?? 0 }}</span>
                </div>
                <div id="timer" class="text-xl font-bold text-error bg-white px-4 py-2 rounded-lg shadow-md">
                    <i class="fas fa-clock mr-2"></i>
                    <span id="time-left">{{ $timeLimit }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Game Helpers -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 50:50 Helper -->
        <button id="helper-fifty-fifty" class="helper-btn bg-gradient-to-r from-orange-400 to-orange-600 text-white" 
                data-helper="fifty_fifty" {{ $helpersUsed['fifty_fifty'] ? 'disabled' : '' }}>
            <i class="fas fa-balance-scale text-2xl mb-2"></i>
            <div class="text-sm font-bold">ลดตัวเลือก</div>
            <div class="text-xs opacity-90">50:50</div>
        </button>

        <!-- Ask Audience Helper -->
        <button id="helper-audience" class="helper-btn bg-gradient-to-r from-blue-400 to-blue-600 text-white" 
                data-helper="ask_audience" {{ $helpersUsed['ask_audience'] ? 'disabled' : '' }}>
            <i class="fas fa-users text-2xl mb-2"></i>
            <div class="text-sm font-bold">ถามผู้ชม</div>
            <div class="text-xs opacity-90">สถิติ</div>
        </button>

        <!-- Extra Time Helper -->
        <button id="helper-extra-time" class="helper-btn bg-gradient-to-r from-green-400 to-green-600 text-white" 
                data-helper="extra_time" {{ $helpersUsed['extra_time'] ? 'disabled' : '' }}>
            <i class="fas fa-plus-circle text-2xl mb-2"></i>
            <div class="text-sm font-bold">เพิ่มเวลา</div>
            <div class="text-xs opacity-90">+30 วิ</div>
        </button>

        <!-- Skip Helper -->
        <button id="helper-skip" class="helper-btn bg-gradient-to-r from-purple-400 to-purple-600 text-white" 
                data-helper="skip" {{ $helpersUsed['skip'] ? 'disabled' : '' }}>
            <i class="fas fa-forward text-2xl mb-2"></i>
            <div class="text-sm font-bold">ข้ามคำถาม</div>
            <div class="text-xs opacity-90">Skip</div>
        </button>
    </div>

    <!-- Question Card -->
    <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-6 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 leading-relaxed">
                {{ $question->question_text }}
            </h2>
            
            @if($question->image_path)
            <div class="mt-4">
                <img src="{{ Storage::url($question->image_path) }}" alt="Question Image" 
                     class="max-w-full h-auto max-h-64 mx-auto rounded-lg shadow-md">
            </div>
            @endif
        </div>

        <!-- Answer Options -->
        <div class="grid gap-4">
            @foreach(['option_a', 'option_b', 'option_c', 'option_d'] as $index => $option)
            @if($question->$option)
            <button class="answer-option w-full text-left p-4 rounded-lg border-2 border-gray-200 
                          hover:border-primary hover:bg-primary/10 transition-all duration-200 
                          focus:outline-none focus:ring-2 focus:ring-primary/50"
                    data-answer="{{ $option }}" data-index="{{ $index }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center 
                               font-bold mr-4 option-label">
                        {{ chr(65 + $index) }}
                    </div>
                    <div class="flex-1 text-lg">
                        {{ $question->$option }}
                    </div>
                </div>
            </button>
            @endif
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center gap-4">
        <button id="submit-answer" class="btn btn-primary btn-lg px-8 py-3 disabled:opacity-50" disabled>
            <i class="fas fa-check mr-2"></i>
            ตอบคำถาม
        </button>
        
        <button id="quit-game" class="btn btn-outline btn-error px-8 py-3">
            <i class="fas fa-times mr-2"></i>
            ออกจากเกม
        </button>
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
