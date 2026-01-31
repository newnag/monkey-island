<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuestionsExport;
use App\Http\Controllers\Controller;
use App\Imports\QuestionsImport;
use App\Models\Question;
use App\Models\Subject;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with('subject:id,name'); // Eager load เฉพาะ columns ที่ต้องการ

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('question_text', 'like', '%'.$request->search.'%');
        }

        $questions = $query->paginate(15);
        $subjects = CacheService::getAllSubjects(); // ใช้ cached subjects

        return view('admin.questions.index', compact('questions', 'subjects'));
    }

    /**
     * Display questions by subject
     */
    public function bySubject(Subject $subject)
    {
        $questions = $subject->questions()->paginate(15);

        return view('admin.questions.by-subject', compact('subject', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subjects = CacheService::getAllSubjects(); // ใช้ cached subjects
        $selectedSubject = $request->get('subject_id');

        return view('admin.questions.create', compact('subjects', 'selectedSubject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_answer' => 'required|in:option_a,option_b,option_c,option_d',
            'image_path' => 'nullable|image|max:2048',
        ]);

        // Convert correct_answer from option_x to x format for database
        $validated['correct_answer'] = str_replace('option_', '', $validated['correct_answer']);

        // Handle image upload
        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('questions', 'public');
        }

        Question::create($validated);

        // Clear subject cache (เพราะ questions_count เปลี่ยน)
        CacheService::clearSubjectCache();

        return redirect()->route('admin.questions.index')
            ->with('success', 'คำถามถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $question->load('subject');

        // Get previous and next questions in the same subject
        $previousQuestion = Question::where('subject_id', $question->subject_id)
            ->where('id', '<', $question->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextQuestion = Question::where('subject_id', $question->subject_id)
            ->where('id', '>', $question->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('admin.questions.show', compact('question', 'previousQuestion', 'nextQuestion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $subjects = CacheService::getAllSubjects(); // ใช้ cached subjects

        return view('admin.questions.edit', compact('question', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:option_a,option_b,option_c,option_d',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'boolean',
        ]);

        // Convert correct_answer from option_x to x format for database
        $validated['correct_answer'] = str_replace('option_', '', $validated['correct_answer']);

        // Handle image removal
        if ($request->boolean('remove_image') && $question->image) {
            Storage::disk('public')->delete($question->image);
            $question->image = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $validated['image'] = $request->file('image')->store('questions', 'public');
        }

        // Remove image-related fields that shouldn't be mass-assigned
        unset($validated['remove_image']);

        $question->update($validated);

        return redirect()->route('admin.questions.index')
            ->with('success', 'คำถามถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        // Delete image if exists
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        // Clear subject cache (เพราะ questions_count เปลี่ยน)
        CacheService::clearSubjectCache();

        return redirect()->route('admin.questions.index')
            ->with('success', 'คำถามถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Import questions from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        try {
            Excel::import(new QuestionsImport($request->subject_id), $request->file('file'));

            return back()->with('success', 'นำเข้าคำถามสำเร็จ');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }

    /**
     * Export questions to Excel
     */
    public function export(?Subject $subject = null)
    {
        $filename = $subject
            ? "questions_{$subject->name}_".date('Y-m-d').'.xlsx'
            : 'all_questions_'.date('Y-m-d').'.xlsx';

        return Excel::download(new QuestionsExport($subject?->id), $filename);
    }
}
