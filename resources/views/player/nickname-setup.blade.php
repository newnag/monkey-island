@extends('game.layout')

@section('title', 'ตั้งชื่อเล่น')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">🎮 ยินดีต้อนรับ</h1>
            <p class="text-white opacity-80">ตั้งชื่อเล่นเพื่อเริ่มต้นการผจญภัย</p>
        </div>

        <!-- Nickname Form -->
        <div class="card-game rounded-2xl p-8">
            <form id="nickname-form" method="POST" action="{{ route('player.setup-nickname') }}">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="nickname" class="block text-lg font-bold text-white mb-3">
                            🏷️ ชื่อเล่นของคุณ
                        </label>
                        <input 
                            type="text" 
                            id="nickname" 
                            name="nickname" 
                            placeholder="ใส่ชื่อเล่นที่คุณต้องการ..." 
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

                    <div class="text-center">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="bg-green-500 hover:bg-green-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105"
                        >
                            🚀 เริ่มเล่น
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info -->
        <div class="card-game rounded-2xl p-6 mt-6">
            <h3 class="text-lg font-bold text-white mb-3">📝 ข้อมูลเพิ่มเติม</h3>
            <ul class="text-white opacity-80 text-sm space-y-2">
                <li>• ชื่อเล่นจะถูกบันทึกไว้ในเครื่องของคุณ</li>
                <li>• คุณสามารถเปลี่ยนชื่อเล่นได้ภายหลัง</li>
                <li>• ชื่อเล่นจะแสดงในกระดานคะแนน</li>
                <li>• ไม่ต้องลงทะเบียนหรือใส่รหัสผ่าน</li>
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

    // Auto-focus on nickname input
    nicknameInput.focus();

    // Character counter
    nicknameInput.addEventListener('input', function() {
        const length = this.value.length;
        const maxLength = 20;
        
        // Enable/disable submit button
        if (length > 0 && length <= maxLength) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
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

        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ กำลังบันทึก...';
    });

    // Initial check
    nicknameInput.dispatchEvent(new Event('input'));
});
</script>
@endpush
@endsection
