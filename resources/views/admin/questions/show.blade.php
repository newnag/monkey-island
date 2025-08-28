@extends('admin.layout')

@section('title', 'รายละเอียดคำถาม')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">รายละเอียดคำถาม #{{ $question->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column: Question Details -->
                        <div class="col-md-8">
                            <!-- Basic Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <strong>วิชา:</strong>
                                    <span class="badge badge-primary">{{ $question->subject->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>ความยาก:</strong>
                                    @php
                                    $difficultyConfig = [
                                        'easy' => ['label' => 'ง่าย', 'class' => 'badge-success'],
                                        'medium' => ['label' => 'ปานกลาง', 'class' => 'badge-warning'],
                                        'hard' => ['label' => 'ยาก', 'class' => 'badge-danger']
                                    ];
                                    $config = $difficultyConfig[$question->difficulty] ?? ['label' => $question->difficulty, 'class' => 'badge-secondary'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <h5 class="text-primary">คำถาม:</h5>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $question->question_text }}</p>
                                </div>
                            </div>

                            <!-- Question Image -->
                            @if($question->image)
                            <div class="mb-4">
                                <h5 class="text-primary">รูปภาพ:</h5>
                                <div class="text-center">
                                    <img src="{{ Storage::url($question->image) }}" alt="Question Image" 
                                         class="img-fluid rounded shadow" style="max-height: 300px;">
                                </div>
                            </div>
                            @endif

                            <!-- Answer Options -->
                            <div class="mb-4">
                                <h5 class="text-primary">ตัวเลือก:</h5>
                                <div class="list-group">
                                    @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $option_key => $label)
                                    @php
                                        $option_field = 'option_' . $option_key;
                                    @endphp
                                    @if($question->$option_field)
                                    <div class="list-group-item d-flex align-items-center {{ $question->correct_answer === $option_key ? 'list-group-item-success' : '' }}">
                                        <div class="mr-3">
                                            @if($question->correct_answer === $option_key)
                                                <span class="badge badge-success badge-pill">{{ $label }} ✓</span>
                                            @else
                                                <span class="badge badge-light badge-pill">{{ $label }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            {{ $question->$option_field }}
                                        </div>
                                        @if($question->correct_answer === $option_key)
                                        <div class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Explanation -->
                            @if($question->explanation)
                            <div class="mb-4">
                                <h5 class="text-primary">คำอธิบาย:</h5>
                                <div class="bg-info bg-opacity-10 p-3 rounded border-left border-info">
                                    <p class="mb-0">{{ $question->explanation }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column: Statistics & Actions -->
                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">ส統ิติ</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-right">
                                                <h4 class="text-primary mb-0">{{ $question->times_used ?? 0 }}</h4>
                                                <small class="text-muted">ครั้งที่ใช้</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $question->success_rate ?? 0 }}%</h4>
                                            <small class="text-muted">ตอบถูก</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata -->
                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">ข้อมูลเพิ่มเติม</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>สร้างเมื่อ:</strong></td>
                                            <td>{{ $question->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>แก้ไขล่าสุด:</strong></td>
                                            <td>{{ $question->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td><code>{{ $question->id }}</code></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">การกระทำ</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.questions.edit', $question) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> แก้ไขคำถาม
                                        </a>

                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-toggle="modal" data-target="#deleteModal">
                                            <i class="fas fa-trash"></i> ลบคำถาม
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Navigation -->
                            @if($previousQuestion)
                            <a href="{{ route('admin.questions.show', $previousQuestion) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-chevron-left"></i> คำถามก่อนหน้า
                            </a>
                            @endif
                        </div>
                        <div class="col-md-6 text-right">
                            @if($nextQuestion)
                            <a href="{{ route('admin.questions.show', $nextQuestion) }}" 
                               class="btn btn-outline-primary">
                                คำถามถัดไป <i class="fas fa-chevron-right"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ที่จะลบคำถามนี้?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>คำเตือน:</strong> การลบคำถามจะไม่สามารถกู้คืนได้
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> ลบคำถาม
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
