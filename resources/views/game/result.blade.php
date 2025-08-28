@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-purple-900 flex items-center justify-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold text-white mb-4">
                    🎉 เกมจบแล้ว! 🎉
                </h1>
                <p class="text-xl text-gray-300">
                    ขอแสดงความยินดีกับผลการเล่นของคุณ
                </p>
            </div>

            <!-- Score Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">สรุปผลคะแนน</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                            <div class="text-4xl font-bold">{{ $gameSession->score }}</div>
                            <div class="text-lg">คะแนนรวม</div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="text-4xl font-bold">{{ $gameSession->current_question_index }}</div>
                            <div class="text-lg">ข้อที่ทำไป</div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                            <div class="text-4xl font-bold">{{ number_format(($gameSession->score / $gameSession->current_question_index) * 100, 1) }}%</div>
                            <div class="text-lg">เปอร์เซ็นต์ถูกต้อง</div>
                        </div>
                    </div>

                    <!-- Game Details -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">รายละเอียดการเล่น</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500">ระดับ</div>
                                <div class="font-semibold capitalize">{{ $gameSession->mode }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">วิชา</div>
                                <div class="font-semibold">{{ $gameSession->subject->name ?? 'ทั่วไป' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">เวลาที่ใช้</div>
                                <div class="font-semibold">
                                    {{ $gameSession->started_at->diffForHumans($gameSession->completed_at, true) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">สถานะ</div>
                                <div class="font-semibold text-green-600">สำเร็จ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Helpers Used -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">ตัวช่วยที่ใช้</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center {{ $gameSession->helper_fifty_fifty ? 'text-red-600' : 'text-gray-400' }}">
                                <span class="mr-2">50:50</span>
                                @if($gameSession->helper_fifty_fifty)
                                    <span class="text-sm">✓ ใช้แล้ว</span>
                                @else
                                    <span class="text-sm">ไม่ได้ใช้</span>
                                @endif
                            </div>
                            <div class="flex items-center {{ $gameSession->helper_ask_audience ? 'text-red-600' : 'text-gray-400' }}">
                                <span class="mr-2">👥 ถามผู้ชม</span>
                                @if($gameSession->helper_ask_audience)
                                    <span class="text-sm">✓ ใช้แล้ว</span>
                                @else
                                    <span class="text-sm">ไม่ได้ใช้</span>
                                @endif
                            </div>
                            <div class="flex items-center {{ $gameSession->helper_extra_time ? 'text-red-600' : 'text-gray-400' }}">
                                <span class="mr-2">⏰ เพิ่มเวลา</span>
                                @if($gameSession->helper_extra_time)
                                    <span class="text-sm">✓ ใช้แล้ว</span>
                                @else
                                    <span class="text-sm">ไม่ได้ใช้</span>
                                @endif
                            </div>
                            <div class="flex items-center {{ $gameSession->helper_skip ? 'text-red-600' : 'text-gray-400' }}">
                                <span class="mr-2">⏭️ ข้าม</span>
                                @if($gameSession->helper_skip)
                                    <span class="text-sm">✓ ใช้แล้ว</span>
                                @else
                                    <span class="text-sm">ไม่ได้ใช้</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            @if($gameSession->mode === 'ranking')
            <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">🏆 อันดับคะแนน</h2>
                
                @if($leaderboard && count($leaderboard) > 0)
                    <div class="space-y-3">
                        @foreach($leaderboard as $index => $entry)
                        <div class="flex items-center p-4 rounded-lg {{ $entry->player_name === $gameSession->player->name ? 'bg-yellow-100 border-2 border-yellow-400' : 'bg-gray-50' }}">
                            <div class="flex-shrink-0 w-12 text-center">
                                @if($index === 0)
                                    <span class="text-2xl">🥇</span>
                                @elseif($index === 1)
                                    <span class="text-2xl">🥈</span>
                                @elseif($index === 2)
                                    <span class="text-2xl">🥉</span>
                                @else
                                    <span class="text-lg font-bold text-gray-600">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="flex-1 ml-4">
                                <div class="font-semibold text-lg {{ $entry->player_name === $gameSession->player->name ? 'text-yellow-800' : 'text-gray-800' }}">
                                    {{ $entry->player_name }}
                                    @if($entry->player_name === $gameSession->player->name)
                                        <span class="text-sm text-yellow-600">(คุณ)</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $entry->score }} คะแนน • {{ $entry->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-2xl font-bold {{ $entry->player_name === $gameSession->player->name ? 'text-yellow-800' : 'text-gray-700' }}">
                                {{ $entry->score }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <p>ยังไม่มีข้อมูลอันดับคะแนน</p>
                    </div>
                @endif
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center space-y-4">
                <div class="space-x-4">
                    <a href="{{ route('game.index') }}" 
                       class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        เล่นใหม่
                    </a>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        กลับหน้าหลัก
                    </a>
                </div>
                
                <div class="pt-4">
                    <button onclick="shareResult()" 
                            class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        แชร์ผลคะแนน
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareResult() {
    if (navigator.share) {
        navigator.share({
            title: 'ผลคะแนนเกม Monkey Island',
            text: `ฉันทำคะแนนได้ {{ $gameSession->score }} คะแนนจากการเล่นเกม Monkey Island!`,
            url: window.location.origin
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const text = `ฉันทำคะแนนได้ {{ $gameSession->score }} คะแนนจากการเล่นเกม Monkey Island! ${window.location.origin}`;
        navigator.clipboard.writeText(text).then(() => {
            alert('คัดลอกผลคะแนนแล้ว!');
        });
    }
}
</script>
@endsection
