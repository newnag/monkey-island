@extends('game.layout')

@section('title', 'แก้ไขชื่อเล่น')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block text-white hover:text-gray-200 mb-4">
                ← กลับหน้าแรก
            </a>
            <h1 class="text-4xl font-bold text-white mb-2">✏️ แก้ไขชื่อเล่น</h1>
            <p class="text-white opacity-80">เปลี่ยนชื่อเล่นใหม่ของคุณ</p>
        </div>

        <!-- Edit Nickname Form -->
        <div class="card-game rounded-2xl p-8">
            <form id="nickname-form" method="POST" action="{{ route('player.update-nickname') }}">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="nickname" class="block text-lg font-bold text-white mb-3">
                            🏷️ ชื่อเล่นปัจจุบัน: <span class="text-yellow-300">{{ $player->nickname }}</span>
                        </label>
                        <input 
                            type="text" 
                            id="nickname" 
                            name="nickname" 
                            value="{{ old('nickname', $player->nickname) }}"
                            placeholder="ใส่ชื่อเล่นใหม่ที่คุณต้องการ..." 
                            maxlength="20"
                            required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-gray-800 text-lg"
                            autocomplete="off"
                        >
                        <p class="text-white opacity-70 text-sm mt-2">
                            • ชื่อเล่นสามารถมีได้สูงสุด 20 ตัวอักษร<br>
                            • จะแสดงในกระดานคะแนนและผลการเล่น
                        </p>
                        @error('nickname')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="flex-1 bg-green-500 hover:bg-green-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-full transition-all duration-300"
                        >
                            💾 บันทึก
                        </button>
                        
                        <a href="{{ route('home') }}" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-full transition-all duration-300 text-center">
                            ❌ ยกเลิก
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info -->
        <div class="card-game rounded-2xl p-6 mt-6">
            <h3 class="text-lg font-bold text-white mb-3">📝 ข้อมูลเพิ่มเติม</h3>
            <ul class="text-white opacity-80 text-sm space-y-2">
                <li>• การเปลี่ยนชื่อเล่นไม่กระทบคะแนนที่มีอยู่</li>
                <li>• ชื่อเล่นใหม่จะแสดงในผลงานต่อไปนี้</li>
                <li>• คุณสามารถเปลี่ยนชื่อเล่นได้ตลอดเวลา</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('nickname-form');
    const submitBtn = document.getElementById('submit-btn');
    const nicknameInput = document.getElementById('nickname');
    const originalNickname = "{{ $player->nickname }}";

    // Auto-focus and select all text
    nicknameInput.focus();
    nicknameInput.select();

    // Character counter and validation
    nicknameInput.addEventListener('input', function() {
        const length = this.value.length;
        const maxLength = 20;
        const newNickname = this.value.trim();
        
        // Enable/disable submit button
        if (length > 0 && length <= maxLength && newNickname !== originalNickname) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '💾 บันทึก';
        } else if (newNickname === originalNickname) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '✅ ไม่มีการเปลี่ยนแปลง';
        } else {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '💾 บันทึก';
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        const nickname = nicknameInput.value.trim();
        
        if (!nickname || nickname.length > 20) {
            e.preventDefault();
            alert('กรุณาใส่ชื่อเล่นที่ถูกต้อง (1-20 ตัวอักษร)');
            return;
        }

        if (nickname === originalNickname) {
            e.preventDefault();
            alert('ชื่อเล่นใหม่ต้องแตกต่างจากชื่อเดิม');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ กำลังบันทึก...';
    });

    // Initial check
    nicknameInput.dispatchEvent(new Event('input'));
});
</script>
@endpush
@endsection
