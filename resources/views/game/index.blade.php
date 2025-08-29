@extends('game.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-6xl font-bold text-white mb-4">
                🐵 Monkey Island Quiz
            </h1>
            <p class="text-xl text-white opacity-90">
                เว็บไซต์ทำข้อสอบที่สนุกและมีความรู้
            </p>
        </div>

        <!-- Game Modes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Single Mode -->
            <div class="card-game rounded-2xl p-6 text-center">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-2xl font-bold text-white mb-3">Single Mode</h3>
                <p class="text-white opacity-80 mb-4">ทำข้อสอบ 10 ข้อ<br>เหมาะสำหรับฝึกฝน</p>
                <a href="{{ route('game.play', 'single') }}" 
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    เริ่มเล่น
                </a>
            </div>

            <!-- Mixed Mode -->
            <div class="card-game rounded-2xl p-6 text-center">
                <div class="text-4xl mb-4">🔥</div>
                <h3 class="text-2xl font-bold text-white mb-3">Mixed Mode</h3>
                <p class="text-white opacity-80 mb-4">ทำข้อสอบ 20 ข้อ<br>คำถามหลากหลาย</p>
                <a href="{{ route('game.play', 'mixed') }}" 
                   class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    เริ่มเล่น
                </a>
            </div>

            <!-- Fun Mode -->
            <div class="card-game rounded-2xl p-6 text-center">
                <div class="text-4xl mb-4">🎉</div>
                <h3 class="text-2xl font-bold text-white mb-3">Fun Mode</h3>
                <p class="text-white opacity-80 mb-4">เล่นสนุก ๆ 15 ข้อ<br>ไม่นับคะแนน</p>
                <a href="{{ route('game.play', 'fun') }}" 
                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    เริ่มเล่น
                </a>
            </div>

            <!-- Ranking Mode -->
            <div class="card-game rounded-2xl p-6 text-center">
                <div class="text-4xl mb-4">🏆</div>
                <h3 class="text-2xl font-bold text-white mb-3">Ranking Mode</h3>
                <p class="text-white opacity-80 mb-4">แข่งขันจริงจัง 25 ข้อ<br>เข้าระบบอันดับ</p>
                <a href="{{ route('game.play', 'ranking') }}" 
                   class="inline-block bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    เริ่มเล่น
                </a>
            </div>
        </div>

        <!-- Features -->
        <div class="card-game rounded-2xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-white mb-4 text-center">🎮 ฟีเจอร์ในเกม</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl mb-2">💡</div>
                    <p class="text-white text-sm">50:50<br><span class="opacity-70">ตัดตัวเลือก</span></p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">👥</div>
                    <p class="text-white text-sm">Ask Audience<br><span class="opacity-70">โหวตผู้ชม</span></p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">⏰</div>
                    <p class="text-white text-sm">Extra Time<br><span class="opacity-70">เพิ่มเวลา</span></p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">🔄</div>
                    <p class="text-white text-sm">Skip Question<br><span class="opacity-70">ข้ามข้อ</span></p>
                </div>
            </div>
        </div>

        <!-- Available Subjects -->
        <div class="card-game rounded-2xl p-6">
            <h3 class="text-2xl font-bold text-white mb-4 text-center">📚 วิชาที่เปิดให้เล่น</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjects as $subject)
                    <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                        <h4 class="font-bold text-white mb-2">{{ $subject->name }}</h4>
                        <p class="text-white opacity-80 text-sm mb-2">{{ $subject->questions_count }} ข้อสอบ</p>
                        @if($subject->description)
                            <p class="text-white opacity-70 text-xs">{{ Str::limit($subject->description, 50) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="text-center mt-8">
            <a href="{{ route('game.leaderboard') }}" 
               class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 mr-4">
                🏆 ดูอันดับ
            </a>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                👤 Admin
            </a>
        </div>

        <!-- Player Info -->
        @if(Cookie::get('player_code'))
            <div class="text-center mt-6">
                <p class="text-white opacity-70">
                    ยินดีต้อนรับ ผู้เล่นหมายเลข: <span class="font-bold">{{ Cookie::get('player_code') }}</span>
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
