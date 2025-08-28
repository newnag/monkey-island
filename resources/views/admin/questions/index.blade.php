@extends('admin.layout')

@section('title', 'จัดการข้อสอบ')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<style>
    .questions-container {
        /* background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); */
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .glass-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    
    .btn-modern {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        color: white;
        font-weight: 600;
        text-transform: none;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-modern:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-success-modern {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .btn-success-modern:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }
    
    .table-modern {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    
    .table-modern tbody tr:hover {
        background: rgba(59, 130, 246, 0.05);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    
    .badge-modern {
        border-radius: 20px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    .filter-section {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 4px 8px;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        margin-left: 8px;
    }
    
    .dataTables_wrapper .dataTables_info {
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin: 0 2px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        border-color: #3b82f6 !important;
    }
    
    table.dataTable thead th {
        border-bottom: 2px solid #3b82f6 !important;
    }
    
    .dt-buttons {
        margin-bottom: 15px;
    }
    
    .dt-buttons .btn {
        border-radius: 8px;
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<div class="questions-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="glass-card p-4">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="h3 mb-1 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list me-3"></i>จัดการข้อสอบ
                            </h2>
                            <p class="text-muted mb-0">จัดการคำถามและข้อสอบทั้งหมดในระบบ</p>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('admin.questions.create') }}" class="btn btn-modern">
                                <i class="fas fa-plus"></i>
                                เพิ่มข้อสอบใหม่
                            </a>
                            <button onclick="document.getElementById('import-modal').style.display='block'" class="btn btn-success-modern">
                                <i class="fas fa-upload"></i>
                                นำเข้า Excel
                            </button>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="filter-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-primary fw-bold">
                                    <i class="fas fa-book me-2"></i>กรองตามวิชา
                                </label>
                                <select id="subject-filter" class="form-select">
                                    <option value="">-- ทุกวิชา --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-primary fw-bold">
                                    <i class="fas fa-chart-line me-2"></i>กรองตามความยาก
                                </label>
                                <select id="difficulty-filter" class="form-select">
                                    <option value="">-- ทุกระดับ --</option>
                                    <option value="ง่าย">🟢 ง่าย</option>
                                    <option value="ปานกลาง">🟡 ปานกลาง</option>
                                    <option value="ยาก">🔴 ยาก</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Questions Table -->
                    <div class="table-responsive">
                        <table id="questions-table" class="table table-modern table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">
                                        <i class="fas fa-book me-2"></i>วิชา
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-question-circle me-2"></i>คำถาม
                                    </th>
                                    <th scope="col" class="text-center">
                                        <i class="fas fa-check-circle me-2"></i>คำตอบที่ถูก
                                    </th>
                                    <th scope="col" class="text-center">
                                        <i class="fas fa-chart-line me-2"></i>ความยาก
                                    </th>
                                    <th scope="col" class="text-center" data-orderable="false">
                                        <i class="fas fa-cogs me-2"></i>การดำเนินการ
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-info badge-modern">
                                                {{ $question->subject->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">
                                                {{ Str::limit($question->question_text, 80) }}
                                            </div>
                                            @if($question->image)
                                                <small class="text-success">
                                                    <i class="fas fa-image me-1"></i>มีรูปภาพ
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary badge-modern">
                                                {{ strtoupper(str_replace('option_', '', $question->correct_answer)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($question->difficulty === 'easy')
                                                <span class="badge bg-success badge-modern">🟢 ง่าย</span>
                                            @elseif($question->difficulty === 'medium')
                                                <span class="badge bg-warning badge-modern">🟡 ปานกลาง</span>
                                            @else
                                                <span class="badge bg-danger badge-modern">🔴 ยาก</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')" title="ลบ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="fw-bold text-primary h4">{{ $questions->count() }}</div>
                                <small class="text-muted">ข้อสอบทั้งหมด</small>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-warning h4">{{ $subjects->count() }}</div>
                                <small class="text-muted">วิชาทั้งหมด</small>
                            </div>
                            <div class="col-md-4">
                                <div class="fw-bold text-info h4">{{ $questions->where('difficulty', 'easy')->count() }}</div>
                                <small class="text-muted">ข้อสอบง่าย</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Import Modal -->
<div class="modal fade" id="import-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-upload me-2"></i>นำเข้าข้อสอบจาก Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-book me-2"></i>เลือกวิชา
                        </label>
                        <select name="subject_id" required class="form-select">
                            <option value="">-- เลือกวิชา --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-file-excel me-2"></i>ไฟล์ Excel
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-control">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            รองรับไฟล์: .xlsx, .xls, .csv (ขนาดไม่เกิน 5MB)
                        </div>
                    </div>
                    <div class="alert alert-info border-0" style="background: rgba(59, 130, 246, 0.1);">
                        <h6 class="alert-heading fw-bold">
                            <i class="fas fa-lightbulb me-2"></i>รูปแบบไฟล์ที่ถูกต้อง:
                        </h6>
                        <ul class="mb-0 small">
                            <li>คอลัมน์ A: คำถาม</li>
                            <li>คอลัมน์ B: ตัวเลือก A</li>
                            <li>คอลัมน์ C: ตัวเลือก B</li>
                            <li>คอลัมน์ D: ตัวเลือก C (ไม่บังคับ)</li>
                            <li>คอลัมน์ E: ตัวเลือก D (ไม่บังคับ)</li>
                            <li>คอลัมน์ F: คำตอบที่ถูก (A, B, C, หรือ D)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-modern">
                        <i class="fas fa-upload me-2"></i>นำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#questions-table').DataTable({
        responsive: true,
        language: {
            "decimal": "",
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "loadingRecords": "กำลังโหลด...",
            "processing": "กำลังประมวลผล...",
            "search": "ค้นหา:",
            "searchPlaceholder": "พิมพ์เพื่อค้นหา...",
            "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "aria": {
                "sortAscending": ": เรียงจากน้อยไปมาก",
                "sortDescending": ": เรียงจากมากไปน้อย"
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        columnDefs: [
            { 
                targets: [4], // การดำเนินการ column (index 4 now instead of 5)
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'asc']],
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-2"></i>Export Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3] // ไม่รวมคอลัมน์การดำเนินการ
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf me-2"></i>Export PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-2"></i>พิมพ์',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ]
    });

    // Custom filters
    $('#subject-filter').on('change', function() {
        var val = this.value;
        table.column(0).search(val).draw();
    });

    $('#difficulty-filter').on('change', function() {
        var val = this.value;
        table.column(3).search(val).draw();
    });

    // Modern modal trigger
    $('[onclick*="import-modal"]').off('click').on('click', function(e) {
        e.preventDefault();
        $('#import-modal').modal('show');
    });
    
    // Table hover effects
    $('.table-modern tbody tr').hover(
        function() { $(this).addClass('shadow-sm'); },
        function() { $(this).removeClass('shadow-sm'); }
    );
    
    // Tooltip initialization
    $('[title]').tooltip();
    
    // Confirm delete with SweetAlert2
    $(document).on('click', '.btn-outline-danger', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '❌ ยืนยันการลบ',
                text: 'คุณแน่ใจหรือไม่ว่าต้องการลบข้อสอบนี้?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '✅ ใช่ ลบเลย!',
                cancelButtonText: '❌ ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อสอบนี้?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush
@endsection
