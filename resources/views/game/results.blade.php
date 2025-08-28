@extends('game.layout')

@section('title', 'ผลการเล่น')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <!-- Result Header -->
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-r from-primary to-secondary text-white p-8 rounded-full mb-4">
                <i class="fas fa-trophy text-6xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">เกมจบแล้ว!</h1>
            <p class="text-lg text-gray-600">ผลการเล่น {{ $session->mode }} Mode</p>
        </div>

        <!-- Score Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <!-- Total Score -->
                <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white p-6 rounded-xl">
                    <i class="fas fa-star text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-1">คะแนนรวม</h3>
                    <p class="text-3xl font-bold">{{ number_format($session->total_score) }}</p>
                </div>

                <!-- Correct Answers -->
                <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-xl">
                    <i class="fas fa-check-circle text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-1">ตอบถูก</h3>
                    <p class="text-3xl font-bold">{{ $correctAnswers }}</p>
                    <p class="text-sm opacity-90">จาก {{ $totalQuestions }} ข้อ</p>
                </div>

                <!-- Accuracy -->
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-xl">
                    <i class="fas fa-bullseye text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-1">ความแม่นยำ</h3>
                    <p class="text-3xl font-bold">{{ $accuracy }}%</p>
                </div>
            </div>
        </div>

        <!-- Game Statistics -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-chart-bar mr-2 text-primary"></i>
                สถิติการเล่น
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Time Used -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-clock text-2xl text-gray-600 mb-2"></i>
                    <h4 class="font-semibold text-gray-800">เวลาที่ใช้</h4>
                    <p class="text-lg font-bold text-primary">{{ gmdate('i:s', $session->time_used) }}</p>
                </div>

                <!-- Helpers Used -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-hand-helping text-2xl text-gray-600 mb-2"></i>
                    <h4 class="font-semibold text-gray-800">ตัวช่วยที่ใช้</h4>
                    <p class="text-lg font-bold text-primary">{{ $helpersUsedCount }} / 4</p>
                </div>

                <!-- Average Time per Question -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-stopwatch text-2xl text-gray-600 mb-2"></i>
                    <h4 class="font-semibold text-gray-800">เฉลี่ย/ข้อ</h4>
                    <p class="text-lg font-bold text-primary">{{ $averageTimePerQuestion }}s</p>
                </div>

                <!-- Subject -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-book text-2xl text-gray-600 mb-2"></i>
                    <h4 class="font-semibold text-gray-800">วิชา</h4>
                    <p class="text-lg font-bold text-primary">{{ $session->subject->name ?? 'หลายวิชา' }}</p>
                </div>
            </div>
        </div>

        @if($session->mode === 'ranking' && $leaderboardPosition)
        <!-- Leaderboard Position -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl shadow-xl p-8 mb-8">
            <div class="text-center">
                <i class="fas fa-medal text-4xl mb-4"></i>
                <h2 class="text-2xl font-bold mb-2">อันดับของคุณ</h2>
                <p class="text-5xl font-bold mb-2">#{{ $leaderboardPosition }}</p>
                <p class="text-lg opacity-90">จากทั้งหมด {{ $totalPlayers }} คน</p>
                
                @if($leaderboardPosition <= 10)
                <div class="mt-4 p-4 bg-white/20 rounded-lg">
                    <p class="text-lg font-semibold">🎉 ยินดีด้วย! คุณอยู่ใน Top 10</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Question Review -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-list-check mr-2 text-primary"></i>
                รายละเอียดคำตอบ
            </h2>

            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($questionAnswers as $index => $qa)
                <div class="flex items-center justify-between p-4 border rounded-lg 
                           {{ $qa['correct'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">
                            ข้อ {{ $index + 1 }}: {{ Str::limit($qa['question'], 50) }}
                        </h4>
                        <p class="text-sm text-gray-600">
                            คำตอบของคุณ: {{ $qa['selected_answer'] ? $qa['selected_option'] : 'ไม่ตอบ' }}
                            @if(!$qa['correct'])
                            | ถูกต้อง: {{ $qa['correct_option'] }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center">
                            @if($qa['correct'])
                            <i class="fas fa-check-circle text-green-500 text-xl mr-2"></i>
                            <span class="font-bold text-green-600">+{{ $qa['points'] }}</span>
                            @else
                            <i class="fas fa-times-circle text-red-500 text-xl mr-2"></i>
                            <span class="font-bold text-red-600">0</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $qa['time_used'] }}s
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <!-- Play Again -->
                <a href="{{ route('game.play', $session->mode) }}" 
                   class="btn btn-primary btn-lg px-8">
                    <i class="fas fa-redo mr-2"></i>
                    เล่นอีกครั้ง
                </a>

                <!-- Different Mode -->
                <a href="{{ route('game.index') }}" 
                   class="btn btn-outline btn-lg px-8">
                    <i class="fas fa-gamepad mr-2"></i>
                    เลือกโหมดอื่น
                </a>

                @if($session->mode === 'ranking')
                <!-- View Leaderboard -->
                <a href="{{ route('game.leaderboard') }}" 
                   class="btn btn-secondary btn-lg px-8">
                    <i class="fas fa-trophy mr-2"></i>
                    ดูอันดับ
                </a>
                @endif
            </div>

            <!-- Share Results -->
            <div class="pt-4">
                <p class="text-gray-600 mb-3">แชร์ผลคะแนนของคุณ</p>
                <div class="flex justify-center gap-3">
                    <button class="btn btn-circle btn-outline btn-sm" onclick="shareToFacebook()">
                        <i class="fab fa-facebook-f text-blue-600"></i>
                    </button>
                    <button class="btn btn-circle btn-outline btn-sm" onclick="shareToTwitter()">
                        <i class="fab fa-twitter text-sky-500"></i>
                    </button>
                    <button class="btn btn-circle btn-outline btn-sm" onclick="shareToLine()">
                        <i class="fab fa-line text-green-500"></i>
                    </button>
                    <button class="btn btn-circle btn-outline btn-sm" onclick="copyResult()">
                        <i class="fas fa-copy text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Share functions
function shareToFacebook() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent(`ฉันเพิ่งได้คะแนน {{ $session->total_score }} คะแนนใน Monkey Island Quiz! ตอบถูก {{ $correctAnswers }} จาก {{ $totalQuestions }} ข้อ`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank');
}

function shareToTwitter() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent(`ฉันเพิ่งได้คะแนน {{ $session->total_score }} คะแนนใน Monkey Island Quiz! ตอบถูก {{ $correctAnswers }} จาก {{ $totalQuestions }} ข้อ #MonkeyIslandQuiz`);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareToLine() {
    const url = encodeURIComponent(window.location.origin);
    const text = encodeURIComponent(`ฉันเพิ่งได้คะแนน {{ $session->total_score }} คะแนนใน Monkey Island Quiz! ตอบถูก {{ $correctAnswers }} จาก {{ $totalQuestions }} ข้อ`);
    window.open(`https://social-plugins.line.me/lineit/share?url=${url}&text=${text}`, '_blank');
}

function copyResult() {
    const text = `ฉันเพิ่งได้คะแนน {{ $session->total_score }} คะแนนใน Monkey Island Quiz! ตอบถูก {{ $correctAnswers }} จาก {{ $totalQuestions }} ข้อ - ${window.location.origin}`;
    
    navigator.clipboard.writeText(text).then(() => {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast toast-top toast-center z-50';
        toast.innerHTML = `
            <div class="alert alert-success">
                <div>
                    <h3 class="font-bold">คัดลอกแล้ว!</h3>
                    <div class="text-xs">คัดลอกผลคะแนนเรียบร้อยแล้ว</div>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }).catch(() => {
        alert('ไม่สามารถคัดลอกได้');
    });
}

// Confetti animation on page load
document.addEventListener('DOMContentLoaded', () => {
    @if($session->total_score > 0)
    // Add confetti effect for good scores
    if ({{ $accuracy }} >= 80) {
        // Simple confetti effect using CSS animation
        for (let i = 0; i < 50; i++) {
            createConfetti();
        }
    }
    @endif
});

function createConfetti() {
    const confetti = document.createElement('div');
    confetti.style.cssText = `
        position: fixed;
        top: -10px;
        left: ${Math.random() * 100}vw;
        width: 10px;
        height: 10px;
        background: hsl(${Math.random() * 360}, 100%, 50%);
        pointer-events: none;
        z-index: 1000;
        animation: fall ${2 + Math.random() * 2}s linear forwards;
    `;
    
    document.body.appendChild(confetti);
    setTimeout(() => confetti.remove(), 4000);
}

// Add keyframe animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fall {
        to {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush

@endsection
