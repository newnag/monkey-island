@extends('game.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block text-white hover:text-gray-200 mb-4">
                ← กลับหน้าแรก
            </a>
            <h1 class="text-4xl font-bold text-white mb-2">
                🏆 Ranking Mode
            </h1>
            <p class="text-white opacity-80">
                แข่งขันจริงจัง 25 ข้อ เข้าระบบอันดับ
            </p>
        </div>

        <!-- Random Subject Info -->
        <div class="card-game rounded-2xl p-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-6">🎲 ข้อสอบผสมจากทุกวิชา</h2>
            
            <div class="bg-white bg-opacity-20 rounded-lg p-6 mb-6">
                <p class="text-white text-lg mb-4">
                    ในโหมด Ranking ระบบจะสุ่มข้อสอบจากทุกวิชาผสมกัน
                </p>
                <p class="text-white opacity-80 text-sm">
                    แต่ละวิชาจะมีจำนวนข้อเท่า ๆ กัน เพื่อความยุติธรรม
                </p>
            </div>

            <div class="space-y-4 mb-8">
                <div class="bg-green-500 bg-opacity-20 rounded-lg p-4">
                    <h3 class="text-white font-bold mb-2">📚 วิชาที่จะผสมในข้อสอบ</h3>
                    @if($availableSubjects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($availableSubjects as $subject)
                                <div class="bg-white bg-opacity-20 rounded p-2">
                                    <span class="text-white text-sm">{{ $subject->name }}</span>
                                    <span class="text-white opacity-70 text-xs block">
                                        ({{ $subject->questions_count }} ข้อ)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-white opacity-80 text-xs mt-3">
                            จำนวนข้อต่อวิชา: ประมาณ {{ floor(25 / $availableSubjects->count()) }}-{{ ceil(25 / $availableSubjects->count()) }} ข้อ
                        </p>
                    @else
                        <p class="text-white opacity-80">ไม่มีวิชาที่เปิดให้เล่น</p>
                    @endif
                </div>
            </div>

            @if($availableSubjects->count() > 0)
                <form action="{{ route('game.start-ranking') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-12 rounded-full text-xl transition-all duration-300 transform hover:scale-105">
                        🚀 เริ่มเกมข้อสอบผสม
                    </button>
                </form>
            @else
                <div class="text-center">
                    <p class="text-white opacity-80 mb-4">ไม่มีวิชาที่มีข้อสอบเพียงพอสำหรับโหมดนี้</p>
                    <a href="{{ route('home') }}" 
                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition duration-300">
                        กลับเลือกโหมดใหม่
                    </a>
                </div>
            @endif
        </div>

        <!-- Game Rules -->
        <div class="card-game rounded-2xl p-6 mt-6">
            <h3 class="text-lg font-bold text-white mb-3">📋 กฎการเล่น Ranking Mode</h3>
            <ul class="text-white opacity-80 text-sm space-y-2">
                <li>• 🎲 ข้อสอบสุ่มจากทุกวิชาผสมกัน</li>
                <li>• ⚖️ แต่ละวิชามีจำนวนข้อเท่า ๆ กัน</li>
                <li>• ⏰ เวลา 20 วินาทีต่อข้อ</li>
                <li>• 📝 ทั้งหมด 25 ข้อ</li>
                <li>• 🎯 คำตอบถูก = 1 คะแนน</li>
                <li>• 🆘 มีตัวช่วย 4 ตัว ใช้ได้คนละครั้งเดียว</li>
                <li>• 🏆 คะแนนจะบันทึกเข้าระบบอันดับ</li>
                <li>• 📊 อันดับจัดตามคะแนน แล้วตามเวลาที่ใช้</li>
            </ul>
        </div>
    </div>
</div>
@endsection
