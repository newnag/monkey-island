@extends('admin.layout')

@section('title', 'แก้ไขคำถาม')

@push('styles')
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css">
<!-- Custom Styles -->
<style>
/* Modern Design Variables */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #48c9b0 0%, #16a085 100%);
    --warning-gradient: linear-gradient(135deg, #f7d794 0%, #f39c12 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    --info-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --card-shadow: 0 15px 40px rgba(0,0,0,0.1);
    --card-hover-shadow: 0 25px 60px rgba(0,0,0,0.15);
    --border-radius: 20px;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Main Container */
.main-container {
    background: rgba(255,255,255,0.1);
    border-radius: var(--border-radius);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin: 20px;
}

/* Header Styling */
.glass-header {
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
    border-bottom: 1px solid rgba(255,255,255,0.2);
    padding: 30px;
    color: white;
}

.header-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin: 0;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    letter-spacing: -0.5px;
}

.header-subtitle {
    opacity: 0.9;
    font-size: 1rem;
    margin-top: 8px;
    font-weight: 500;
}

/* Glass Form Sections */
.glass-section {
    background: rgba(255,255,255,0.95);
    border-radius: var(--border-radius);
    padding: 35px;
    margin-bottom: 25px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.glass-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--primary-gradient);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.section-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    font-size: 1.2rem;
    margin-right: 15px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.section-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
}

/* Enhanced Form Controls */
.modern-input {
    background: rgba(255,255,255,0.9);
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-radius: 15px;
    padding: 15px 20px;
    font-size: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.modern-input:focus {
    background: white;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

.modern-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 12px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Question Textarea */
.question-input {
    background: linear-gradient(135deg, #f8faff 0%, #e8f4fd 100%);
    border: 3px solid rgba(102, 126, 234, 0.3);
    border-radius: var(--border-radius);
    padding: 25px;
    min-height: 180px;
    resize: vertical;
    font-size: 1.1rem;
    line-height: 1.7;
    box-shadow: inset 0 5px 15px rgba(0,0,0,0.05);
}

.question-input:focus {
    background: white;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

/* Character Counter */
.char-badge {
    background: var(--primary-gradient);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Image Upload Area */
.upload-zone {
    border: 4px dashed rgba(102, 126, 234, 0.5);
    border-radius: var(--border-radius);
    padding: 50px;
    text-align: center;
    background: linear-gradient(135deg, #f8faff 0%, #e8f4fd 100%);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.upload-zone:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #e8f4fd 0%, #f8faff 100%);
}

@keyframes shimmer {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}

.upload-icon {
    font-size: 4rem;
    color: #667eea;
    margin-bottom: 20px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

/* Current Image Display */
.current-image-wrapper {
    position: relative;
    display: inline-block;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    transition: var(--transition);
}

.current-image-wrapper:hover {
    transform: scale(1.05) rotate(-2deg);
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
}

.current-image {
    max-width: 300px;
    max-height: 250px;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

/* Option Inputs */
.option-group {
    position: relative;
    margin-bottom: 25px;
}

.option-badge {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary-gradient);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.3rem;
    z-index: 10;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    transition: var(--transition);
}

.option-field {
    padding: 18px 20px 18px 85px;
    background: rgba(255,255,255,0.9);
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-radius: 25px;
    font-size: 1.1rem;
    transition: var(--transition);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.option-field:focus {
    background: white;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    transform: translateY(-3px);
}

.option-field.correct-answer {
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
    border-color: #28a745;
    box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.15);
}

.option-field.correct-answer ~ .option-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    animation: correctPulse 2s infinite;
}

@keyframes correctPulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 15px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

/* Correct Answer Preview */
.answer-preview {
    background: var(--success-gradient);
    color: white;
    padding: 25px;
    border-radius: var(--border-radius);
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transition: var(--transition);
    font-weight: 600;
    font-size: 1.1rem;
}

.answer-preview:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* Action Buttons */
.btn-modern {
    background: var(--primary-gradient);
    border: none;
    border-radius: 25px;
    padding: 15px 35px;
    font-weight: 700;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: var(--transition);
}

.btn-modern:hover::before {
    left: 100%;
}

.btn-modern:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
}

.btn-success-modern {
    background: var(--info-gradient);
    box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
}

.btn-success-modern:hover {
    box-shadow: 0 15px 40px rgba(67, 233, 123, 0.6);
}

.btn-secondary-modern {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

.btn-secondary-modern:hover {
    box-shadow: 0 15px 40px rgba(108, 117, 125, 0.6);
}

/* Alert Styling */
.modern-alert {
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(220, 53, 69, 0.3);
    border-left: 6px solid #dc3545;
    border-radius: var(--border-radius);
    color: #721c24;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
}

/* Footer */
.glass-footer {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255,255,255,0.3);
    padding: 40px;
    text-align: center;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-container {
        margin: 10px;
    }
    
    .glass-section {
        padding: 25px 20px;
    }
    
    .option-field {
        padding-left: 75px;
    }
    
    .option-badge {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .header-title {
        font-size: 1.8rem;
    }
}

/* Loading Animation */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 25px;
    height: 25px;
    margin: -12px 0 0 -12px;
    border: 3px solid transparent;
    border-top: 3px solid #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Info Badge */
.info-badge {
    background: var(--info-gradient);
    color: white;
    padding: 12px 20px;
    border-radius: 15px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 6px 20px rgba(67, 233, 123, 0.3);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="main-container" data-aos="fade-up">
                <!-- Glass Header -->
                <div class="glass-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="header-title">
                                <i class="fas fa-magic mr-3"></i>แก้ไขคำถาม #{{ $question->id }}
                            </h1>
                            <p class="header-subtitle mb-0">
                                📚 {{ $question->subject->name }} • 
                                ⏰ สร้างเมื่อ {{ $question->created_at->diffForHumans() }} • 
                                🔧 แก้ไขล่าสุด {{ $question->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-success-modern btn-sm">
                                <i class="fas fa-eye mr-2"></i>ดูตัวอย่าง
                            </a>
                            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary-modern btn-sm ml-2">
                                <i class="fas fa-arrow-left mr-2"></i>กลับ
                            </a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data" id="questionForm">
                    @csrf
                    @method('PUT')
                    
                    <div style="padding: 30px;">
                        @if($errors->any())
                        <div class="modern-alert alert-dismissible fade show mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                                <h5 class="mb-0 font-weight-bold">กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</h5>
                            </div>
                            <ul class="mb-0 pl-4">
                                @foreach($errors->all() as $error)
                                <li class="mb-2"><strong>•</strong> {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" style="font-size: 2rem;">
                                <span>&times;</span>
                            </button>
                        </div>
                        @endif

                        <!-- Basic Information Section -->
                        <div class="glass-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                ข้อมูลพื้นฐาน
                            </h3>
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <label class="modern-label">
                                        <i class="fas fa-book text-primary"></i>
                                        วิชา <span class="text-danger">*</span>
                                    </label>
                                    <select name="subject_id" id="subject_id" class="form-control modern-input @error('subject_id') is-invalid @enderror" required>
                                        <option value="">🎯 เลือกวิชา</option>
                                        @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                    <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Question Content Section -->
                        <div class="glass-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                เนื้อหาคำถาม
                            </h3>
                            
                            <div class="mb-4">
                                <label class="modern-label">
                                    <i class="fas fa-edit text-primary"></i>
                                    คำถาม <span class="text-danger">*</span>
                                </label>
                                <textarea name="question_text" id="question_text" 
                                        class="form-control question-input @error('question_text') is-invalid @enderror" 
                                        placeholder="✍️ พิมพ์คำถามของคุณที่นี่... ใส่ใจในรายละเอียด เพื่อให้ผู้เล่นเข้าใจได้ชัดเจน" 
                                        required>{{ old('question_text', $question->question_text) }}</textarea>
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="info-badge">
                                        <i class="fas fa-lightbulb"></i>
                                        เขียนคำถามให้ชัดเจนและเข้าใจง่าย
                                    </div>
                                    <div class="char-badge">
                                        <i class="fas fa-keyboard"></i>
                                        <span id="char-count">{{ strlen($question->question_text) }}</span> ตัวอักษร
                                    </div>
                                </div>
                                @error('question_text')
                                <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Image Section -->
                        <div class="glass-section" data-aos="fade-up" data-aos-delay="300">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-image"></i>
                                </div>
                                รูปภาพประกอบ
                            </h3>
                            
                            <div class="row">
                                @if($question->image)
                                <div class="col-lg-6 mb-4">
                                    <label class="modern-label text-primary mb-3">
                                        <i class="fas fa-eye"></i>
                                        รูปภาพปัจจุบัน
                                    </label>
                                    <div class="text-center">
                                        <div class="current-image-wrapper">
                                            <img src="{{ Storage::url($question->image) }}" alt="Question Image" class="current-image">
                                        </div>
                                        <div class="mt-3">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image">
                                                <label class="custom-control-label text-danger font-weight-bold" for="remove_image">
                                                    <i class="fas fa-trash mr-2"></i>ลบรูปภาพนี้
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                @else
                                <div class="col-12">
                                @endif
                                    <label class="modern-label text-primary mb-3">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        {{ $question->image ? 'อัปโหลดรูปใหม่' : 'เพิ่มรูปภาพ' }}
                                        <span class="text-muted">(ไม่บังคับ)</span>
                                    </label>
                                    <div class="upload-zone" id="imageUploadArea">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h4 class="font-weight-bold text-primary mb-3">คลิกเพื่อเลือกรูปภาพ</h4>
                                        <p class="text-muted mb-0 font-weight-600">รองรับ JPG, PNG, GIF • ไม่เกิน 2MB</p>
                                        <input type="file" name="image" id="image" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            
                            @error('image')
                            <div class="modern-alert mt-3">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Answer Options Section -->
                        <div class="glass-section" data-aos="fade-up" data-aos-delay="400">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                                ตัวเลือกคำตอบ
                            </h3>
                            <div class="info-badge mb-4">
                                <i class="fas fa-info-circle"></i>
                                <strong>คำแนะนำ:</strong> กรอกอย่างน้อย 2 ตัวเลือก และเลือกคำตอบที่ถูกต้อง (จะไฮไลต์เป็นสีเขียว)
                            </div>

                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="option-group">
                                        <div class="option-badge">A</div>
                                        <input type="text" name="option_a" id="option_a" 
                                               class="form-control option-field @error('option_a') is-invalid @enderror"
                                               value="{{ old('option_a', $question->option_a) }}"
                                               placeholder="🔤 ตัวเลือก A (จำเป็น)" required>
                                        @error('option_a')
                                        <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="option-group">
                                        <div class="option-badge">B</div>
                                        <input type="text" name="option_b" id="option_b" 
                                               class="form-control option-field @error('option_b') is-invalid @enderror"
                                               value="{{ old('option_b', $question->option_b) }}"
                                               placeholder="🔤 ตัวเลือก B (จำเป็น)" required>
                                        @error('option_b')
                                        <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="option-group">
                                        <div class="option-badge">C</div>
                                        <input type="text" name="option_c" id="option_c" 
                                               class="form-control option-field @error('option_c') is-invalid @enderror"
                                               value="{{ old('option_c', $question->option_c) }}"
                                               placeholder="🔤 ตัวเลือก C (ไม่บังคับ)">
                                        @error('option_c')
                                        <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="option-group">
                                        <div class="option-badge">D</div>
                                        <input type="text" name="option_d" id="option_d" 
                                               class="form-control option-field @error('option_d') is-invalid @enderror"
                                               value="{{ old('option_d', $question->option_d) }}"
                                               placeholder="🔤 ตัวเลือก D (ไม่บังคับ)">
                                        @error('option_d')
                                        <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Correct Answer Selection -->
                            <div class="row">
                                <div class="col-lg-8">
                                    <label class="modern-label">
                                        <i class="fas fa-check-circle text-success"></i>
                                        คำตอบที่ถูกต้อง <span class="text-danger">*</span>
                                    </label>
                                    <select name="correct_answer" id="correct_answer" 
                                            class="form-control modern-input @error('correct_answer') is-invalid @enderror" required>
                                        <option value="">🎯 เลือกคำตอบที่ถูกต้อง</option>
                                    </select>
                                    @error('correct_answer')
                                    <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="modern-label text-muted">ตัวอย่างการแสดงผล</label>
                                    <div id="correct-preview" class="answer-preview">
                                        <i class="fas fa-trophy mr-2"></i>
                                        <span>เลือกคำตอบที่ถูกต้อง</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Explanation Section -->
                        <div class="glass-section" data-aos="fade-up" data-aos-delay="500">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                คำอธิบายเพิ่มเติม
                            </h3>
                            <div class="mb-4">
                                <label class="modern-label">
                                    <i class="fas fa-comment-alt text-info"></i>
                                    คำอธิบาย <span class="text-muted">(ไม่บังคับ)</span>
                                </label>
                                <textarea name="explanation" id="explanation" 
                                        class="form-control modern-input @error('explanation') is-invalid @enderror" 
                                        rows="5" 
                                        placeholder="💡 ให้คำอธิบายเพิ่มเติมเกี่ยวกับคำตอบ เช่น เหตุผลที่ตัวเลือกนี้ถูกต้อง หรือข้อมูลเพิ่มเติมที่จะช่วยให้ผู้เล่นเข้าใจมากขึ้น...">{{ old('explanation', $question->explanation) }}</textarea>
                                <div class="info-badge mt-3">
                                    <i class="fas fa-graduation-cap"></i>
                                    คำอธิบายนี้จะช่วยให้ผู้เล่นเข้าใจเหตุผลของคำตอบ และเรียนรู้ได้มากขึ้น
                                </div>
                                @error('explanation')
                                <div class="text-danger mt-2"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Glass Footer -->
                    <div class="glass-footer" data-aos="fade-up" data-aos-delay="600">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="d-flex flex-wrap justify-content-center gap-3">
                                    <button type="submit" class="btn btn-modern flex-fill mx-2" style="max-width: 200px;">
                                        <i class="fas fa-save mr-2"></i>อัปเดตคำถาม
                                    </button>
                                    
                                    <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-success-modern flex-fill mx-2" style="max-width: 180px;">
                                        <i class="fas fa-eye mr-2"></i>ดูตัวอย่าง
                                    </a>
                                    
                                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary-modern flex-fill mx-2" style="max-width: 150px;">
                                        <i class="fas fa-arrow-left mr-2"></i>กลับ
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <p class="text-muted mb-0">
                                <i class="fas fa-clock mr-2"></i>
                                <strong>แก้ไขล่าสุด:</strong> {{ $question->updated_at->format('d/m/Y H:i:s') }} 
                                <span class="mx-3">•</span>
                                <i class="fas fa-user mr-2"></i>
                                <strong>โดย:</strong> Admin System
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>
<!-- Custom JavaScript -->
<script>
$(document).ready(function() {

    // Character counter
    function updateCharCount() {
        const count = $('#question_text').val().length;
        $('#char-count').text(count);
        
        const charBadge = $('.char-badge');
        charBadge.removeClass('text-warning text-danger');
        
        if (count > 500) {
            charBadge.addClass('text-danger');
        } else if (count > 300) {
            charBadge.addClass('text-warning');
        }
    }
    
    $('#question_text').on('input', updateCharCount);
    updateCharCount();

    // Image upload with preview
    $('#imageUploadArea').click(function() {
        $('#image').click();
    });

    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            // File size validation
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ใหญ่เกินไป',
                    text: 'กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 2MB',
                    confirmButtonText: 'ตกลง'
                });
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                $('.image-preview').remove();
                
                // Create enhanced preview
                const preview = $(`
                    <div class="image-preview mt-4 text-center" style="display: none;">
                        <div class="info-badge mb-3">
                            <i class="fas fa-check-circle"></i>
                            ตัวอย่างรูปภาพใหม่
                        </div>
                        <div class="current-image-wrapper">
                            <img src="${e.target.result}" alt="Preview" class="current-image">
                        </div>
                        <p class="text-success mt-3 mb-0">
                            <i class="fas fa-info-circle mr-1"></i>รูปภาพจะถูกอัปเดตเมื่อบันทึก
                        </p>
                    </div>
                `);
                
                $('#imageUploadArea').after(preview);
                preview.fadeIn(500);
            };
            reader.readAsDataURL(file);
            
            // Update upload area
            $('#imageUploadArea').find('h4').html('<i class="fas fa-check-circle mr-2"></i>รูปภาพใหม่ถูกเลือกแล้ว!');
            $('#imageUploadArea').addClass('border-success');
            $('#imageUploadArea .upload-icon').html('<i class="fas fa-check-circle"></i>').addClass('text-success');
        }
    });

    // Dynamic correct answer options with enhanced UI
    function updateCorrectAnswerOptions() {
        const correctAnswerSelect = $('#correct_answer');
        const currentValue = correctAnswerSelect.val();
        
        // Clear existing options except first
        correctAnswerSelect.find('option:not(:first)').remove();
        
        // Add options based on filled inputs
        ['A', 'B', 'C', 'D'].forEach((letter) => {
            const optionInput = $(`#option_${letter.toLowerCase()}`);
            const optionText = optionInput.val().trim();
            
            if (optionText) {
                const optionValue = `option_${letter.toLowerCase()}`;
                const selected = currentValue === optionValue ? 'selected' : '';
                const previewText = optionText.length > 45 ? optionText.substr(0, 45) + '...' : optionText;
                
                correctAnswerSelect.append(`
                    <option value="${optionValue}" ${selected}>
                        ${letter}: ${previewText}
                    </option>
                `);
            }
        });
        
        updateOptionVisuals();
    }

    // Enhanced visual indicators
    function updateOptionVisuals() {
        const correctAnswer = $('#correct_answer').val();
        
        // Reset all options
        $('.option-field').removeClass('correct-answer');
        $('.option-badge').css('background', 'var(--primary-gradient)');
        
        // Highlight correct answer
        if (correctAnswer) {
            $(`#${correctAnswer}`).addClass('correct-answer');
            
            // Update preview with animation
            const correctText = $(`#${correctAnswer}`).val() || 'เลือกคำตอบที่ถูกต้อง';
            const letter = correctAnswer.replace('option_', '').toUpperCase();
            
            $('#correct-preview span').html(`
                <strong>${letter}:</strong> ${correctText.substr(0, 35)}${correctText.length > 35 ? '...' : ''}
            `);
            
            // Add success animation to preview
            $('#correct-preview').addClass('animate__animated animate__pulse');
            setTimeout(() => {
                $('#correct-preview').removeClass('animate__animated animate__pulse');
            }, 1000);
        } else {
            $('#correct-preview span').html('<i class="fas fa-question-circle mr-2"></i>เลือกคำตอบที่ถูกต้อง');
        }
    }

    // Event handlers
    $('[id^="option_"]').on('input', updateCorrectAnswerOptions);
    $('#correct_answer').on('change', updateOptionVisuals);
    
    // Initial setup
    updateCorrectAnswerOptions();

    // Enhanced form validation with SweetAlert2
    $('#questionForm').on('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        let errorMessage = '';

        // Validation checks
        const filledOptions = $('[id^="option_"]').filter(function() {
            return $(this).val().trim() !== '';
        }).length;

        if (filledOptions < 2) {
            isValid = false;
            errorMessage = 'กรุณากรอกตัวเลือกอย่างน้อย 2 ตัวเลือก';
        }

        const correctAnswer = $('#correct_answer').val();
        if (!correctAnswer) {
            isValid = false;
            errorMessage = 'กรุณาเลือกคำตอบที่ถูกต้อง';
        }

        if (correctAnswer && !$(`#${correctAnswer}`).val().trim()) {
            isValid = false;
            errorMessage = 'คำตอบที่ถูกต้องต้องมีข้อความ';
        }

        if ($('#question_text').val().trim().length < 10) {
            isValid = false;
            errorMessage = 'คำถามต้องมีความยาวอย่างน้อย 10 ตัวอักษร';
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: '❌ ข้อมูลไม่ครบถ้วน',
                text: errorMessage,
                confirmButtonText: 'ตกลง แก้ไขเลย!',
                confirmButtonColor: '#667eea',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                }
            });
            return false;
        }

        // Show confirmation before submit
        Swal.fire({
            title: '🚀 พร้อมอัปเดตแล้วใช่หรือไม่?',
            text: 'คุณต้องการบันทึกการเปลี่ยนแปลงนี้หรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '✅ ใช่ บันทึกเลย!',
            cancelButtonText: '❌ ยกเลิก',
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                const submitBtn = $('#questionForm button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.addClass('btn-loading').prop('disabled', true);
                
                // Show loading alert
                Swal.fire({
                    title: '💾 กำลังบันทึก...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                this.submit();
            }
        });
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl + S to save
        if (e.ctrlKey && e.which === 83) {
            e.preventDefault();
            $('#questionForm').submit();
        }
        
        // Ctrl + Enter to save
        if (e.ctrlKey && e.which === 13) {
            e.preventDefault();
            $('#questionForm').submit();
        }
    });

    // Auto-save notification (optional)
    let autoSaveTimer;
    let hasChanges = false;
    
    $('#questionForm input, #questionForm textarea, #questionForm select').on('input change', function() {
        hasChanges = true;
        clearTimeout(autoSaveTimer);
        
        // Show unsaved changes indicator
        if (!$('.unsaved-indicator').length) {
            $('.header-title').append(' <span class="unsaved-indicator text-warning">● ยังไม่บันทึก</span>');
        }
    });

    // Warn before leaving with unsaved changes
    $(window).on('beforeunload', function(e) {
        if (hasChanges) {
            const message = 'คุณมีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก ต้องการออกจากหน้านี้หรือไม่?';
            e.returnValue = message;
            return message;
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add loading states to buttons
    $('.btn').on('click', function() {
        if (!$(this).hasClass('btn-loading') && $(this).attr('type') !== 'submit') {
            const btn = $(this);
            btn.append(' <i class="fas fa-spinner fa-spin ml-2"></i>');
        }
    });
});
</script>
@endpush
