@extends('admin.layout')

@section('content')
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Dashboard') }}
    </h2>
</x-slot>

<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $stats['total_subjects'] }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">วิชาทั้งหมด</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">subjects</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $stats['total_questions'] }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">ข้อสอบทั้งหมด</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">questions</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $stats['total_players'] }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">ผู้เล่นทั้งหมด</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">players</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ $stats['total_games'] }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">เกมทั้งหมด</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">total games</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                        {{ $stats['games_today'] }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">เกมวันนี้</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">today</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">การดำเนินการด่วน</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    เพิ่มวิชาใหม่
                </a>
                <a href="{{ route('admin.questions.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    เพิ่มข้อสอบใหม่
                </a>
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ดูเว็บไซต์
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Games -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">เกมล่าสุด</h3>
                <div class="space-y-3">
                    @forelse($recentGames as $game)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $game->subject->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Player: {{ $game->player->player_code ?? 'Unknown' }} | {{ $game->mode }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-green-600 dark:text-green-400">
                                    {{ $game->score ?? 0 }} คะแนน
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $game->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">ยังไม่มีเกมที่เล่น</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Popular Subjects -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">วิชายอดนิยม</h3>
                <div class="space-y-3">
                    @forelse($popularSubjects as $subject)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $subject->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $subject->questions_count ?? 0 }} ข้อสอบ
                                </div>
                            </div>
                            <div class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                {{ $subject->game_sessions_count ?? 0 }} เกม
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">ยังไม่มีข้อมูล</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
