@extends('admin.layout')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $subject->name }}</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    แก้ไข
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    กลับ
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Subject Info -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">รายละเอียดวิชา</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อวิชา</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $subject->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำอธิบาย</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $subject->description ?: 'ไม่มีคำอธิบาย' }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวนข้อสอบสูงสุด</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $subject->max_questions }} ข้อ</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ</label>
                                <p class="mt-1">
                                    @if($subject->status === 'active')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">เปิดใช้งาน</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">ปิดใช้งาน</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่สร้าง</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $subject->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">แก้ไขล่าสุด</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $subject->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Stats -->
            <div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">สстатистิก</h3>
                    
                    <div class="space-y-4">
                        <div class="text-center p-4 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $subject->questions->count() }}
                            </div>
                            <div class="text-sm text-blue-800 dark:text-blue-200">ข้อสอบทั้งหมด</div>
                        </div>
                        
                        <div class="text-center p-4 bg-green-100 dark:bg-green-900 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $subject->questions->where('status', true)->count() }}
                            </div>
                            <div class="text-sm text-green-800 dark:text-green-200">ข้อสอบที่เปิดใช้งาน</div>
                        </div>
                        
                        <div class="space-y-2">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">ระดับความยาก</h4>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">ง่าย:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $subject->questions->where('difficulty', 'easy')->count() }} ข้อ</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">ปานกลาง:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $subject->questions->where('difficulty', 'medium')->count() }} ข้อ</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">ยาก:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $subject->questions->where('difficulty', 'hard')->count() }} ข้อ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-2">
                        <a href="{{ route('admin.questions.by-subject', $subject) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            จัดการข้อสอบ
                        </a>
                        <a href="{{ route('admin.questions.create', ['subject_id' => $subject->id]) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            เพิ่มข้อสอบใหม่
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
