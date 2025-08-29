@extends('game.layout')

@section('title', 'อันดับคะแนน')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-white py-12 mb-8">
        <div class="container mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4">
                <i class="fas fa-trophy text-6xl mb-4 block"></i>
                อันดับคะแนน
            </h1>
            <p class="text-xl opacity-90">ผู้เล่นที่ทำคะแนนสูงสุด</p>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-12">
        <!-- Filters -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                <!-- Mode Filter -->
                <div class="flex-1">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-700">โหมด</span>
                    </label>
                    <select name="mode" class="select select-bordered w-full">
                        <option value="">ทุกโหมด</option>
                        <option value="single" {{ request('mode') === 'single' ? 'selected' : '' }}>เดี่ยว (Single)</option>
                        <option value="mixed" {{ request('mode') === 'mixed' ? 'selected' : '' }}>ผสม (Mixed)</option>
                        <option value="fun" {{ request('mode') === 'fun' ? 'selected' : '' }}>สนุก (Fun)</option>
                        <option value="ranking" {{ request('mode') === 'ranking' ? 'selected' : '' }}>แรงกิ้ง (Ranking)</option>
                    </select>
                </div>

                <!-- Subject Filter -->
                <div class="flex-1">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-700">วิชา</span>
                    </label>
                    <select name="subject" class="select select-bordered w-full">
                        <option value="">ทุกวิชา</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        ค้นหา
                    </button>
                </div>
            </form>
        </div>

        @if($leaderboards->isNotEmpty())
        <!-- Top 3 Podium -->
        @if($leaderboards->count() >= 3)
        <div class="flex justify-center items-end mb-12 gap-4">
            <!-- 2nd Place -->
            <div class="text-center">
                <div class="bg-gradient-to-b from-gray-300 to-gray-500 text-white rounded-xl p-6 shadow-xl transform -rotate-2 hover:rotate-0 transition-transform">
                    <div class="text-6xl mb-4">🥈</div>
                    <h3 class="text-xl font-bold mb-2">อันดับ 2</h3>
                    <p class="text-lg font-semibold text-gray-100">{{ $leaderboards[1]->player->nickname ?? $leaderboards[1]->player->player_code }}</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($leaderboards[1]->score) }}</p>
                    <p class="text-sm text-gray-200">{{ $leaderboards[1]->accuracy }}% • {{ $leaderboards[1]->subject->name ?? 'วิชาผสม' }}</p>
                </div>
            </div>

            <!-- 1st Place -->
            <div class="text-center">
                <div class="bg-gradient-to-b from-yellow-300 to-yellow-600 text-white rounded-xl p-8 shadow-2xl transform scale-110">
                    <div class="text-8xl mb-4">🏆</div>
                    <h3 class="text-2xl font-bold mb-2">อันดับ 1</h3>
                    <p class="text-xl font-semibold text-gray-100">{{ $leaderboards[0]->player->nickname ?? $leaderboards[0]->player->player_code }}</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($leaderboards[0]->score) }}</p>
                    <p class="text-sm text-gray-200">{{ $leaderboards[0]->accuracy }}% • {{ $leaderboards[0]->subject->name ?? 'วิชาผสม' }}</p>
                </div>
            </div>

            <!-- 3rd Place -->
            <div class="text-center">
                <div class="bg-gradient-to-b from-orange-400 to-orange-600 text-white rounded-xl p-6 shadow-xl transform rotate-2 hover:rotate-0 transition-transform">
                    <div class="text-6xl mb-4">🥉</div>
                    <h3 class="text-xl font-bold mb-2">อันดับ 3</h3>
                    <p class="text-lg font-semibold text-gray-100">{{ $leaderboards[2]->player->nickname ?? $leaderboards[2]->player->player_code }}</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($leaderboards[2]->score) }}</p>
                    <p class="text-sm text-gray-200">{{ $leaderboards[2]->accuracy }}% • {{ $leaderboards[2]->subject->name ?? 'วิชาผสม' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Full Leaderboard -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary p-6">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-list-ol mr-3"></i>
                    อันดับคะแนนทั้งหมด
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left text-gray-800 font-bold">อันดับ</th>
                            <th class="text-left text-gray-800 font-bold">ผู้เล่น</th>
                            <th class="text-center text-gray-800 font-bold">คะแนน</th>
                            <th class="text-center text-gray-800 font-bold">ตอบถูก</th>
                            <th class="text-center text-gray-800 font-bold">ความแม่นยำ</th>
                            <th class="text-center text-gray-800 font-bold">เวลา</th>
                            <th class="text-left text-gray-800 font-bold">วิชา</th>
                            <th class="text-center text-gray-800 font-bold">โหมด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboards as $index => $record)
                        <tr class="hover:bg-gray-50 {{ $index < 3 ? 'bg-yellow-50 border-l-4 border-yellow-500' : '' }}">
                            <td>
                                <div class="flex items-center">
                                    <span class="text-xl font-bold mr-2">{{ $index + 1 }}</span>
                                    @if($index === 0)
                                    <i class="fas fa-crown text-yellow-500"></i>
                                    @elseif($index === 1)
                                    <i class="fas fa-medal text-gray-500"></i>
                                    @elseif($index === 2)
                                    <i class="fas fa-award text-orange-500"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-white rounded-full w-12 h-12">
                                            <span class="text-lg font-bold">
                                                @if($record->player->nickname)
                                                    {{ strtoupper(substr($record->player->nickname, 0, 2)) }}
                                                @else
                                                    {{ strtoupper(substr($record->player->player_code, 0, 2)) }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $record->player->nickname ?? $record->player->player_code }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ $record->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-2xl font-bold text-primary">
                                    {{ number_format($record->score) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-lg font-semibold text-gray-800">
                                    {{ $record->correct_answers }}/{{ $record->total_questions }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center">
                                    <div class="radial-progress text-primary" 
                                         style="--value:{{ $record->accuracy }};" 
                                         role="progressbar">
                                        {{ $record->accuracy }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 mr-1"></i>
                                    <span class="text-gray-800 font-medium">{{ gmdate('i:s', $record->time_used) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-outline">
                                    {{ $record->subject->name ?? 'วิชาผสม' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                $modeConfig = [
                                    'single' => ['label' => 'เดี่ยว', 'class' => 'badge-primary'],
                                    'mixed' => ['label' => 'ผสม', 'class' => 'badge-secondary'],
                                    'fun' => ['label' => 'สนุก', 'class' => 'badge-accent'],
                                    'ranking' => ['label' => 'แรงกิ้ง', 'class' => 'badge-warning']
                                ];
                                $config = $modeConfig[$record->mode] ?? ['label' => $record->mode, 'class' => 'badge-ghost'];
                                @endphp
                                <div class="badge {{ $config['class'] }}">
                                    {{ $config['label'] }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <!-- No Records -->
        <div class="text-center py-16">
            <div class="text-8xl mb-8">🏆</div>
            <h3 class="text-2xl font-bold text-gray-600 mb-4">ยังไม่มีข้อมูลอันดับ</h3>
            <p class="text-gray-500 mb-8">เป็นคนแรกที่จะขึ้นอันดับกันเถอะ!</p>
            <a href="{{ route('game.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-play mr-2"></i>
                เริ่มเล่นเกม
            </a>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="text-center mt-12">
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="{{ route('game.index') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 ease-in-out overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 animate-pulse"></div>
                    <i class="fas fa-gamepad mr-3 text-xl group-hover:animate-bounce relative z-10"></i>
                    <span class="relative z-10">เล่นเกม</span>
                </a>
                
                <button onclick="window.location.reload()" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-2xl shadow-lg hover:shadow-xl hover:border-gray-400 transform hover:scale-105 transition-all duration-300 ease-in-out hover:bg-gray-50">
                    <i class="fas fa-sync-alt mr-3 text-xl group-hover:rotate-180 group-hover:text-blue-500 transition-all duration-500"></i>
                    <span class="group-hover:text-gray-800 transition-colors duration-300">รีเฟรช</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto refresh every 30 seconds
setInterval(() => {
    const now = new Date();
    const minutes = now.getMinutes();
    
    // Only refresh at specific intervals to avoid interrupting user
    if (minutes % 5 === 0 && now.getSeconds() === 0) {
        window.location.reload();
    }
}, 1000);

// Animate counters on page load
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.radial-progress');
    
    counters.forEach(counter => {
        const target = parseInt(counter.style.getPropertyValue('--value'));
        let current = 0;
        const increment = target / 50; // 50 steps animation
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.style.setProperty('--value', Math.round(current));
        }, 20);
    });
});
</script>
@endpush

@endsection
