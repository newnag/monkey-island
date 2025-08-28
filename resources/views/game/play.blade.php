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
                @switch($mode)
                    @case('single')
                        🎯 Single Mode
                        @break
                    @case('mixed')
                        🔥 Mixed Mode
                        @break
                    @case('fun')
                        🎉 Fun Mode
                        @break
                    @case('ranking')
                        🏆 Ranking Mode
                        @break
                @endswitch
            </h1>
            <p class="text-white opacity-80">
                @switch($mode)
                    @case('single')
                        ทำข้อสอบ 10 ข้อ เหมาะสำหรับฝึกฝน
                        @break
                    @case('mixed')
                        ทำข้อสอบ 20 ข้อ คำถามหลากหลาย
                        @break
                    @case('fun')
                        เล่นสนุก ๆ 15 ข้อ ไม่นับคะแนน
                        @break
                    @case('ranking')
                        แข่งขันจริงจัง 25 ข้อ เข้าระบบอันดับ
                        @break
                @endswitch
            </p>
        </div>

        <!-- Subject Selection -->
        <div class="card-game rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">เลือกวิชาที่ต้องการทำข้อสอบ</h2>
            
            <form id="start-game-form" class="space-y-4">
                @csrf
                <input type="hidden" name="mode" value="{{ $mode }}">
                
                <div class="space-y-3">
                    @foreach($subjects as $subject)
                        @php
                            $requiredQuestions = match($mode) {
                                'single' => 10,
                                'mixed' => 20,
                                'fun' => 15,
                                'ranking' => 25,
                                default => 10
                            };
                        @endphp
                        @if($subject->questions_count >= $requiredQuestions)
                            <label class="block">
                                <input type="radio" name="subject_id" value="{{ $subject->id }}" class="sr-only" required>
                                <div class="subject-card cursor-pointer p-4 rounded-lg border-2 border-transparent bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-white">{{ $subject->name }}</h3>
                                            @if($subject->description)
                                                <p class="text-white opacity-80 text-sm">{{ $subject->description }}</p>
                                            @endif
                                            <p class="text-white opacity-70 text-xs mt-1">
                                                มีข้อสอบ {{ $subject->questions_count }} ข้อ
                                            </p>
                                        </div>
                                        <div class="text-2xl">📚</div>
                                    </div>
                                </div>
                            </label>
                        @else
                            <div class="subject-card-disabled p-4 rounded-lg bg-gray-500 bg-opacity-30 cursor-not-allowed">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-300">{{ $subject->name }}</h3>
                                        <p class="text-gray-400 text-sm">ข้อสอบไม่เพียงพอสำหรับโหมดนี้</p>
                                        <p class="text-gray-400 text-xs">
                                            มีข้อสอบ {{ $subject->questions_count }} ข้อ (ต้องการ {{ $requiredQuestions }} ข้อ)
                                        </p>
                                    </div>
                                    <div class="text-2xl opacity-50">📚</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @php
                    $requiredQuestions = match($mode) {
                        'single' => 10,
                        'mixed' => 20,
                        'fun' => 15,
                        'ranking' => 25,
                        default => 10
                    };
                @endphp
                
                @if($subjects->where('questions_count', '>=', $requiredQuestions)->count() > 0)
                    <div class="text-center mt-8">
                        <button type="submit" id="start-button" disabled
                            class="bg-green-500 hover:bg-green-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white font-bold py-4 px-12 rounded-full text-xl transition-all duration-300 transform hover:scale-105">
                            🚀 เริ่มเกม
                        </button>
                    </div>
                @else
                    <div class="text-center mt-8">
                        <p class="text-white opacity-80 mb-4">ไม่มีวิชาที่มีข้อสอบเพียงพอสำหรับโหมดนี้</p>
                        <a href="{{ route('home') }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition duration-300">
                            กลับเลือกโหมดใหม่
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Game Rules -->
        <div class="card-game rounded-2xl p-6 mt-6">
            <h3 class="text-lg font-bold text-white mb-3">📋 กำราเล่น</h3>
            <ul class="text-white opacity-80 text-sm space-y-1">
                <li>• ตอบคำถามให้ถูกต้องภายในเวลาที่กำหนด</li>
                <li>• มีตัวช่วย 4 ตัว ใช้ได้คนละครั้งเดียว</li>
                <li>• คำตอบถูก = 1 คะแนน</li>
                <li>• คำถามและตัวเลือกจะถูกสุ่มทุกครั้ง</li>
                @if($mode === 'ranking')
                    <li>• โหมด Ranking จะบันทึกคะแนนเข้าระบบอันดับ</li>
                @endif
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('start-game-form');
    const startButton = document.getElementById('start-button');
    const subjectCards = document.querySelectorAll('.subject-card');
    const radioButtons = document.querySelectorAll('input[name="subject_id"]');

    // Handle subject selection
    subjectCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.parentElement.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateSelection();
            }
        });
    });

    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateSelection);
    });

    function updateSelection() {
        // Remove selected class from all cards
        subjectCards.forEach(card => {
            card.classList.remove('border-green-400', 'bg-opacity-40');
            card.classList.add('border-transparent', 'bg-opacity-20');
        });

        // Add selected class to selected card
        const selectedRadio = document.querySelector('input[name="subject_id"]:checked');
        if (selectedRadio) {
            const selectedCard = selectedRadio.parentElement.querySelector('.subject-card');
            selectedCard.classList.remove('border-transparent', 'bg-opacity-20');
            selectedCard.classList.add('border-green-400', 'bg-opacity-40');
            startButton.disabled = false;
        } else {
            startButton.disabled = true;
        }
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        startButton.disabled = true;
        startButton.innerHTML = '⏳ กำลังเริ่มเกม...';

        fetch('{{ route("game.start") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถเริ่มเกมได้'));
                startButton.disabled = false;
                startButton.innerHTML = '🚀 เริ่มเกม';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            startButton.disabled = false;
            startButton.innerHTML = '🚀 เริ่มเกม';
        });
    });
});
</script>
@endpush
@endsection
