@extends('layouts.app')

@section('title', '나의 수강 대시보드')

@section('content')
<div class="fade-in space-y-8">
    
    <!-- Welcome Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white font-sans">안녕하세요, {{ session('fullname') }} 학생 👋</h2>
            <p class="text-slate-400 text-sm mt-1">현재 활성화된 학습 과정과 해야 할 학사 과제들을 체크하세요.</p>
        </div>
    </div>

    <!-- Quick Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shadow-md">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $total_courses }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">진행 중인 강좌 수</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400 shadow-md">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $avg_progress }}%</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">평균 이수 진행률</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shadow-md">
                <i data-lucide="clock-alert" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $pending_count }}개</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">제출 대기 과제</span>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Course List & Enrollment -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Enrolled Courses Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="graduation-cap" class="text-indigo-400 w-5 h-5"></i>
                    현재 진행 중인 강좌
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if (!empty($enrolled_courses))
                        @foreach ($enrolled_courses as $course)
                            <div class="glass p-6 rounded-2xl border border-slate-850 flex flex-col justify-between glass-card-hover min-h-[200px]">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-500/15 text-brand-400 border border-brand-500/20 uppercase tracking-wider">{{ $course['shortname'] }}</span>
                                        <span class="text-xs font-bold text-slate-400">ID: {{ $course['id'] }}</span>
                                    </div>
                                    <h4 class="text-base font-bold text-white leading-tight mb-2 hover:text-indigo-300 transition-colors">{{ $course['fullname'] }}</h4>
                                    <p class="text-xs text-slate-400 line-clamp-3 mb-4">{{ strip_tags($course['summary'] ?? '이 강좌에 대한 설명이 등록되지 않았습니다.') }}</p>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">진행도 (이수율)</span>
                                        <span class="font-bold text-brand-400">{{ $course['progress'] }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-950/80 rounded-full h-2 overflow-hidden border border-slate-800/40">
                                        <div class="bg-gradient-to-r from-brand-500 to-indigo-500 h-full rounded-full transition-all duration-300" style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-2 glass p-8 text-center rounded-2xl border border-slate-800">
                            <i data-lucide="book-open-text" class="w-12 h-12 mx-auto text-slate-600 mb-3"></i>
                            <p class="text-slate-400 text-sm">현재 수강 중인 강좌가 없습니다. 수강 신청을 해주세요!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Registration (Self-enrollment Simulator) -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="text-brand-400 w-5 h-5"></i>
                    신규 강좌 수강신청
                </h3>
                
                <div class="glass rounded-2xl border border-slate-800 overflow-hidden divide-y divide-slate-850">
                    @if (!empty($catalog_courses))
                        @foreach ($catalog_courses as $course)
                            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-900/10 transition-colors">
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 uppercase tracking-wider">{{ $course['shortname'] }}</span>
                                        <span class="text-xs font-semibold text-slate-500">ID: {{ $course['id'] }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">{{ $course['fullname'] }}</h4>
                                    <p class="text-xs text-slate-400 line-clamp-2">{{ strip_tags($course['summary'] ?? '상세 설명 없음') }}</p>
                                </div>
                                <button type="button" onclick="enrollCourse('{{ $course['id'] }}')" class="px-4 py-2 text-xs font-bold rounded-xl bg-brand-600 hover:bg-brand-500 text-white transition-all shadow-md shrink-0 self-start md:self-center">
                                    수강신청
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="p-6 text-center text-slate-500 text-sm">
                            신청할 수 있는 신규 강좌가 없습니다.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course History Timeline -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="history" class="text-slate-400 w-5 h-5"></i>
                    수강 강좌 이력 (완료/이전 학기)
                </h3>
                
                <div class="glass p-6 rounded-2xl border border-slate-800 space-y-4">
                    @if (!empty($history_courses))
                        <div class="relative pl-6 border-l border-slate-800 space-y-6">
                            @foreach ($history_courses as $course)
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-indigo-500/20 border border-indigo-400 flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                    </span>
                                    <div>
                                        <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $course['shortname'] }}</span>
                                        <h4 class="text-sm font-bold text-white mt-1.5">{{ $course['fullname'] }}</h4>
                                        <div class="flex items-center gap-4 text-[11px] text-slate-500 mt-1">
                                            <span>진행도: 100% (이수 완료)</span>
                                            <span>•</span>
                                            <span>ID: {{ $course['id'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-500 text-sm">이전 수강 완료 이력이 없습니다.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Sticky Pending Assignments -->
        <div class="space-y-6">
            <div class="sticky top-6 space-y-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="text-rose-400 w-5 h-5"></i>
                    제출 해야 할 과제 목록
                </h3>

                <div class="glass p-6 rounded-3xl border border-slate-800/80 shadow-xl space-y-4">
                    @if (!empty($pending_assignments))
                        <p class="text-xs text-slate-400">마감 예정일 순서로 정렬된 할 일 목록입니다.</p>
                        
                        <div class="space-y-4">
                            @foreach ($pending_assignments as $a)
                                <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-850 hover:border-slate-800 transition-colors flex flex-col justify-between gap-3">
                                    <div class="space-y-1">
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-indigo-900/40 text-indigo-400 uppercase tracking-wider">{{ $a['course_name'] }}</span>
                                        <h4 class="text-xs font-bold text-slate-200 leading-snug">{{ $a['name'] }}</h4>
                                    </div>

                                    <div class="flex items-center justify-between mt-1.5">
                                        <span class="flex items-center text-[10px] font-bold {{ $a['days_left'] == 0 ? 'text-red-400' : ($a['days_left'] < 3 ? 'text-amber-400' : 'text-slate-400') }}">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 mr-1"></i>
                                            {{ $a['remaining_text'] }}
                                        </span>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-900 text-slate-400 border border-slate-800">
                                            미제출
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-full bg-green-500/10 text-green-400 flex items-center justify-center mb-3">
                                <i data-lucide="check-check" class="w-6 h-6"></i>
                            </div>
                            <p class="text-slate-300 font-semibold text-sm">완벽합니다!</p>
                            <p class="text-slate-500 text-xs mt-1">제출 예정인 과제가 모두 완료되었습니다.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function enrollCourse(courseId) {
        if (!confirm("해당 강좌를 수강신청하시겠습니까?")) return;
        
        showLoading("수강신청을 처리하는 중...");

        const formData = new FormData();
        formData.append('course_id', courseId);
        // Add CSRF token for Laravel
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('student.enroll') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.status) {
                window.location.reload();
            } else {
                alert("수강신청 에러: " + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert("서버 통신 실패");
        });
    }
</script>
@endsection
