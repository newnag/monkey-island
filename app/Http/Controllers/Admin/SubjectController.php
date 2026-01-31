<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\CacheService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::withCount('questions')->paginate(10);

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_questions' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        Subject::create($validated);

        // Clear subject cache
        CacheService::clearSubjectCache();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'วิชาถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        $subject->load('questions');

        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_questions' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $subject->update($validated);

        // Clear subject cache
        CacheService::clearSubjectCache();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'วิชาถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has questions
        if ($subject->questions()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบวิชาที่มีข้อสอบอยู่ได้');
        }

        $subject->delete();

        // Clear subject cache
        CacheService::clearSubjectCache();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'วิชาถูกลบเรียบร้อยแล้ว');
    }
}
