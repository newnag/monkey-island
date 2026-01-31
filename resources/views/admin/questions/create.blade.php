@extends('admin.layout')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">เพิ่มข้อสอบใหม่</h2>
            <a href="{{ route('admin.questions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                กลับ
            </a>
        </div>

        <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- Subject -->
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วิชา</label>
                    <select name="subject_id" id="subject_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- เลือกวิชา --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $selectedSubject) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Question Text -->
                <div>
                    <label for="question_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำถาม</label>
                    <textarea name="question_text" id="question_text" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('question_text') }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="option_a" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตัวเลือก A</label>
                        <input type="text" name="option_a" id="option_a" value="{{ old('option_a') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('option_a')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="option_b" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตัวเลือก B</label>
                        <input type="text" name="option_b" id="option_b" value="{{ old('option_b') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('option_b')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="option_c" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตัวเลือก C</label>
                        <input type="text" name="option_c" id="option_c" value="{{ old('option_c') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('option_c')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="option_d" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตัวเลือก D</label>
                        <input type="text" name="option_d" id="option_d" value="{{ old('option_d') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('option_d')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Correct Answer -->
                <div>
                    <label for="correct_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำตอบที่ถูกต้อง</label>
                    <select name="correct_answer" id="correct_answer" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- เลือกคำตอบที่ถูก --</option>
                        <option value="option_a" {{ old('correct_answer') === 'option_a' ? 'selected' : '' }}>A</option>
                        <option value="option_b" {{ old('correct_answer') === 'option_b' ? 'selected' : '' }}>B</option>
                        <option value="option_c" {{ old('correct_answer') === 'option_c' ? 'selected' : '' }}>C</option>
                        <option value="option_d" {{ old('correct_answer') === 'option_d' ? 'selected' : '' }}>D</option>
                    </select>
                    @error('correct_answer')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="image_path" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รูปภาพประกอบ (ถ้ามี)</label>
                    <input type="file" name="image_path" id="image_path" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</p>
                    @error('image_path')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        บันทึก
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
