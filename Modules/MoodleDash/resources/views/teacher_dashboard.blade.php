@extends('moodledash::layouts.app')

@section('title', 'Learning Analytics Dashboard (Teacher)')

@section('content')
<div class="fade-in space-y-8">
    
    <!-- Header with Course Selector -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <h2 class="text-2xl font-bold text-white font-sans">Professor {{ session('fullname') }} 👨‍🏫</h2>
            <p class="text-slate-400 text-sm mt-1">Provides detailed progress status and learning engagement analytics for your courses.</p>
        </div>
        
        <!-- Course Selector Dropdown -->
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-sm font-semibold text-slate-400">Select Course:</span>
            <div class="relative">
                <select id="course-switcher" onchange="switchCourse(this.value)" class="appearance-none bg-slate-950/80 border border-slate-850 hover:border-slate-800 focus:border-brand-500 text-slate-200 text-sm font-bold rounded-xl py-3 pl-4 pr-10 focus:outline-none transition-colors shadow-lg cursor-pointer">
                    @foreach ($courses as $c)
                        <option value="{{ $c['id'] }}" {{ $c['id'] == $selected_course_id ? 'selected' : '' }}>
                            {{ $c['fullname'] }}
                        </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Course Information Summary -->
    <div class="glass p-6 rounded-3xl border border-slate-850 bg-gradient-to-r from-slate-950/60 to-indigo-950/10">
        <h3 class="text-lg font-bold text-white mb-2">{{ $selected_course['fullname'] }}</h3>
        <p class="text-sm text-slate-400 leading-relaxed max-w-4xl">{{ strip_tags($selected_course['summary'] ?? 'No summary available for this course.') }}</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-md">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $total_students }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Students Enrolled</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shadow-md">
                <i data-lucide="line-chart" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $avg_progress }}%</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Class Average Progress</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shadow-md">
                <i data-lucide="file-warning" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white">{{ $pending_grades_count }}</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Grades</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Chart 1: Activity Line Chart -->
        <div class="lg:col-span-3 glass p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-6">
                <i data-lucide="activity" class="text-brand-400 w-5 h-5"></i>
                Learning Engagement Trend (Weekly Student Logins)
            </h3>
            <div class="h-64 relative">
                <canvas id="loginChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Grade Bar Chart -->
        <div class="lg:col-span-2 glass p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-6">
                <i data-lucide="bar-chart-3" class="text-indigo-400 w-5 h-5"></i>
                Overall Expected Grade Distribution
            </h3>
            <div class="h-64 relative">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Risk Warnings & Student Analysis Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Roster Table (Left 2 columns) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="users-round" class="text-slate-400 w-5 h-5"></i>
                    Student Enrollment & Learning Status
                </h3>
                
                <!-- Search bar -->
                <div class="relative w-48 md:w-64">
                    <input type="text" id="roster-search" onkeyup="filterRoster()" class="w-full bg-slate-950/60 border border-slate-850 focus:border-brand-500 rounded-xl py-2 pl-9 pr-4 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Search student (Name)...">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </div>

            <!-- Table Card -->
            <div class="glass rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950/60 border-b border-slate-800 text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                                <th class="py-4 px-5">Student Profile</th>
                                <th class="py-4 px-5">Last LMS Access</th>
                                <th class="py-4 px-5 text-center">Progress</th>
                                <th class="py-4 px-5 text-center">Grades</th>
                                <th class="py-4 px-5 text-center">Engagement Status</th>
                                <th class="py-4 px-5 text-center">Diagnostic Tool</th>
                            </tr>
                        </thead>
                        <tbody id="roster-body" class="divide-y divide-slate-850">
                            @if (!empty($students))
                                @foreach ($students as $s)
                                    <tr class="hover:bg-slate-900/10 transition-colors">
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $s['profileimageurl'] }}" alt="Profile" class="w-8.5 h-8.5 rounded-lg object-cover ring-2 ring-indigo-500/10 bg-slate-800 shrink-0">
                                                <div>
                                                    <span class="block text-xs font-bold text-slate-200 student-name">{{ $s['fullname'] }}</span>
                                                    <span class="text-[10px] text-slate-500 font-mono">{{ $s['email'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-xs text-slate-400">
                                            {{ $s['lastaccess_formatted'] }}
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="flex flex-col items-center justify-center gap-1.5 max-w-[80px] mx-auto">
                                                <span class="text-xs font-semibold text-brand-400">{{ $s['progress'] }}%</span>
                                                <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-brand-500 h-full rounded-full" style="width: {{ $s['progress'] }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-center text-xs font-bold text-slate-300">
                                            {{ $s['grade'] }}
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if ($s['is_risk_computed'])
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Inactive / Risk Warning</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">Active / On Track</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <button type="button" onclick="openStudentModal({{ $s['id'] }}, '{{ $s['fullname'] }}', '{{ $s['email'] }}', '{{ $s['profileimageurl'] }}', {{ $s['progress'] }}, {{ $s['grade'] }}, '{{ addslashes($s['feedback']) }}', '{{ $s['is_risk_computed'] }}')" class="px-3 py-1.5 text-[10px] font-bold rounded-lg border border-slate-700 hover:border-slate-500 text-slate-300 hover:text-white transition-all bg-slate-900/40">
                                                Analyze
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                                        No students currently enrolled.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Risk Monitor List (Right column) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="alert-octagon" class="text-rose-400 w-5 h-5"></i>
                    At-Risk Student Monitor (Warning Group)
                </h3>
                <button type="button" onclick="runOutreach()" class="px-2.5 py-1.5 rounded-xl text-[9px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 flex items-center gap-1.5 shadow transition-colors select-none">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    AI Outreach Emails
                </button>
            </div>

            <div class="glass p-6 rounded-3xl border border-slate-800/80 shadow-xl space-y-4 max-h-[460px] overflow-y-auto">
                @if (!empty($risk_students))
                    <p class="text-xs text-rose-400/90 font-medium">⚠️ At-risk students who have not logged in for over 7 days or have a progress rate under 45%.</p>
                    
                    <div class="space-y-3.5">
                        @foreach ($risk_students as $s)
                            <div class="p-3.5 rounded-xl bg-rose-500/5 border border-rose-500/15 flex items-center gap-3.5">
                                <img src="{{ $s['profileimageurl'] }}" alt="Profile" class="w-9 h-9 rounded-lg object-cover ring-2 ring-rose-500/20 bg-slate-800 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-200">{{ $s['fullname'] }}</span>
                                    <p class="text-[10px] text-rose-400 mt-0.5">
                                        진도율: {{ $s['progress'] }}% • 마지막 접속: {{ $s['lastaccess_formatted'] }}
                                    </p>
                                </div>
                                <button type="button" onclick="openStudentModal({{ $s['id'] }}, '{{ $s['fullname'] }}', '{{ $s['email'] }}', '{{ $s['profileimageurl'] }}', {{ $s['progress'] }}, {{ $s['grade'] }}, '{{ addslashes($s['feedback']) }}', '{{ $s['is_risk_computed'] }}')" class="w-8 h-8 rounded-lg flex items-center justify-center border border-rose-500/20 text-rose-400 hover:bg-rose-500/10 transition-colors" title="Diagnostic Feedback">
                                    <i data-lucide="notebook-pen" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-12 h-12 mx-auto rounded-full bg-green-500/10 text-green-400 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <p class="text-slate-300 font-semibold text-sm">No warnings or at-risk students</p>
                        <p class="text-slate-500 text-xs mt-1">All students are actively engaging and on track.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Course Assignment Submission Monitoring Table -->
    <div class="space-y-4 pt-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <i data-lucide="file-check-2" class="text-indigo-400 w-5 h-5"></i>
            평가 및 과제물 제출 관리
        </h3>

        <div class="glass rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-4 px-5">Assessment Title</th>
                        <th class="py-4 px-5">Deadline</th>
                        <th class="py-4 px-5 text-center">Submissions</th>
                        <th class="py-4 px-5 text-center">Grading Status</th>
                        <th class="py-4 px-5 text-center">Class Avg Score</th>
                        <th class="py-4 px-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850 text-slate-300 text-xs">
                    @if (!empty($assignments_summary))
                        @foreach ($assignments_summary as $a)
                            <tr class="hover:bg-slate-900/10 transition-colors">
                                <td class="py-4 px-5 font-bold text-slate-200">
                                    {{ $a['name'] }}
                                </td>
                                <td class="py-4 px-5 text-slate-450">
                                    {{ $a['deadline'] }}
                                </td>
                                <td class="py-4 px-5 text-center font-bold">
                                    <span>{{ $a['submitted_text'] }} student(s)</span>
                                    <span class="text-slate-500 font-normal">({{ $a['submission_rate'] }}%)</span>
                                </td>
                                <td class="py-4 px-5 text-center font-bold">
                                    @if ($a['is_pending'])
                                        <span class="text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">{{ $a['graded_text'] }} (Pending)</span>
                                    @else
                                        <span class="text-green-400 bg-green-500/10 border border-green-500/20 px-2 py-0.5 rounded">{{ $a['graded_text'] }} (Completed)</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-center font-bold text-brand-400">
                                    {{ $a['avg_score'] }} / {{ $a['max_score'] }}
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="text-[10px] text-slate-500 font-semibold uppercase">LMS Linked</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">
                                No assignments or assessments registered for this course.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Detail Modal Overlay -->
<div id="student-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#05070c]/70 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="glass max-w-xl w-full mx-4 rounded-3xl border border-slate-800 shadow-2xl relative flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-850 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img id="modal-img" src="" alt="Avatar" class="w-11 h-11 rounded-lg object-cover ring-2 ring-indigo-500/20 bg-slate-800">
                <div>
                    <h3 id="modal-title" class="text-base font-bold text-white">Name</h3>
                    <p id="modal-email" class="text-[11px] text-slate-500 font-mono">Email</p>
                </div>
            </div>
            <button type="button" onclick="closeStudentModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-850 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 text-center">
                    <span id="modal-progress" class="block text-lg font-bold text-brand-400">0%</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Progress Rate</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 text-center">
                    <span id="modal-grade" class="block text-lg font-bold text-indigo-400">0 pts</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Expected Grade</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 text-center">
                    <span id="modal-status" class="block text-[11px] font-bold">Diagnosing</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Risk Level</span>
                </div>
            </div>

            <!-- Dynamic Feedback Form -->
            <div class="space-y-2">
                <label for="modal-feedback-input" class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Student Assessment & Diagnostic Feedback</label>
                <p class="text-[11px] text-slate-500">Record diagnostic feedback for monitoring students. (Applies locally)</p>
                <input type="hidden" id="modal-student-id">
                <textarea id="modal-feedback-input" rows="3" class="w-full bg-slate-950/60 border border-slate-850 focus:border-brand-500 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-700 focus:outline-none transition-colors" placeholder="Enter feedback comments here."></textarea>
                <div class="flex justify-end pt-1">
                    <button type="button" onclick="saveFeedback()" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center gap-1.5">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        Save Feedback
                    </button>
                </div>
            </div>

            <!-- Details Roster Activity checklist -->
            <div class="space-y-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Individual Activities & Assessment Details</span>
                <div class="space-y-2.5" id="modal-activity-items">
                    <!-- Injected dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Outreach Report Modal Overlay -->
<div id="outreach-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#05070c]/70 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="glass max-w-2xl w-full mx-4 rounded-3xl border border-slate-800 shadow-2xl relative flex flex-col max-h-[85vh]">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-850 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <i data-lucide="mail-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">AI Student Outreach Report</h3>
                    <p class="text-[10px] text-slate-500">List of AI outreach messages successfully generated and sent to students. (Recorded in laravel.log)</p>
                </div>
            </div>
            <button type="button" onclick="closeOutreachModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-850 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-semibold">Total Emails Sent:</span>
                <span id="outreach-sent-count" class="text-base font-bold text-rose-400">0 student(s)</span>
            </div>

            <div class="space-y-3.5 max-h-[45vh] overflow-y-auto pr-1" id="outreach-logs-container">
                <!-- Dynamically loaded list of logs -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function switchCourse(courseId) {
        showLoading("Analyzing learning data...");
        window.location.href = "?course_id=" + courseId;
    }

    function filterRoster() {
        const query = document.getElementById('roster-search').value.toLowerCase();
        const rows = document.querySelectorAll('#roster-body tr');
        
        rows.forEach(row => {
            const nameEl = row.querySelector('.student-name');
            if (nameEl) {
                const name = nameEl.textContent.toLowerCase();
                if (name.includes(query)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    }

    function openStudentModal(id, fullname, email, pic, progress, grade, feedback, isRisk) {
        document.getElementById('modal-student-id').value = id;
        document.getElementById('modal-title').textContent = fullname;
        document.getElementById('modal-email').textContent = email;
        document.getElementById('modal-img').src = pic;
        document.getElementById('modal-progress').textContent = progress + "%";
        document.getElementById('modal-grade').textContent = grade + " pts";
        document.getElementById('modal-feedback-input').value = feedback;
        
        const statusBadge = document.getElementById('modal-status');
        if (isRisk === '1' || isRisk === 1 || isRisk === 'True' || isRisk === true) {
            statusBadge.textContent = "At-Risk";
            statusBadge.className = "block text-rose-400 text-xs font-bold py-1";
        } else {
            statusBadge.textContent = "On Track";
            statusBadge.className = "block text-green-400 text-xs font-bold py-1";
        }

        const activityContainer = document.getElementById('modal-activity-items');
        activityContainer.innerHTML = ''; 

        const actTemplates = [
            { name: "[Assignment] Week 1 Environment Setup & Algorithm Implementation", status: "completed", val: "95 / 100 pts" },
            { name: "[Assignment] Week 2 Complexity Optimization & Test Case Analysis", status: progress > 30 ? "completed" : "pending", val: progress > 30 ? "88 / 100 pts" : "Not Submitted" },
            { name: "[Quiz] Mid-term Evaluation Quiz", status: progress > 50 ? "completed" : "pending", val: progress > 50 ? "18 / 20 pts" : "Not Taken" },
            { name: "[Assignment] Final Project Proposal", status: progress > 75 ? "completed" : "pending", val: progress > 75 ? "Submitted" : "Not Submitted" }
        ];

        actTemplates.forEach(act => {
            const item = document.createElement('div');
            item.className = "flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-900 text-xs";
            
            const left = document.createElement('div');
            left.className = "flex items-center gap-2";
            
            const icon = document.createElement('i');
            if (act.status === 'completed') {
                icon.setAttribute('data-lucide', 'check-circle-2');
                icon.className = "text-green-500 w-4.5 h-4.5";
            } else {
                icon.setAttribute('data-lucide', 'circle-dashed');
                icon.className = "text-slate-650 w-4.5 h-4.5";
            }
            
            const nameSpan = document.createElement('span');
            nameSpan.className = "text-slate-300 font-medium";
            nameSpan.textContent = act.name;
            
            left.appendChild(icon);
            left.appendChild(nameSpan);

            const scoreBadge = document.createElement('span');
            if (act.status === 'completed') {
                scoreBadge.className = "px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-400";
                scoreBadge.textContent = act.val;
            } else {
                scoreBadge.className = "px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-slate-500 border border-slate-800";
                scoreBadge.textContent = act.val;
            }
            
            item.appendChild(left);
            item.appendChild(scoreBadge);
            activityContainer.appendChild(item);
        });

        lucide.createIcons();

        const modal = document.getElementById('student-modal');
        modal.classList.remove('pointer-events-none', 'opacity-0');
        modal.classList.add('opacity-100');
    }

    function closeStudentModal() {
        const modal = document.getElementById('student-modal');
        modal.classList.remove('opacity-100');
        modal.classList.add('pointer-events-none', 'opacity-0');
    }

    function saveFeedback() {
        const studentId = document.getElementById('modal-student-id').value;
        const feedbackText = document.getElementById('modal-feedback-input').value;
        const courseId = '{{ $selected_course_id }}';
        
        if (!feedbackText.trim()) {
            alert("Please enter feedback comments.");
            return;
        }

        showLoading("Saving feedback...");

        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('course_id', courseId);
        formData.append('feedback', feedbackText);
        // Laravel CSRF token
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('teacher.feedback') }}", {
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
                alert(data.message);
                closeStudentModal();
                window.location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert("Feedback save failed.");
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Line Chart: Weekly Activities
        const loginCtx = document.getElementById('loginChart').getContext('2d');
        new Chart(loginCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Access Traffic',
                    data: {!! json_encode($weekly_login_trends) !!},
                    borderColor: '#4f66ff',
                    backgroundColor: 'rgba(79, 102, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#6366f1',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });

        // Bar Chart: Expected Grades Distribution
        const gradeCtx = document.getElementById('gradeChart').getContext('2d');
        new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart_grades_labels) !!},
                datasets: [{
                    label: 'Number of Students',
                    data: {!! json_encode($chart_grades_data) !!},
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.75)',
                        'rgba(59, 130, 246, 0.75)',
                        'rgba(16, 185, 129, 0.75)',
                        'rgba(245, 158, 11, 0.75)',
                        'rgba(239, 68, 68, 0.75)'
                    ],
                    borderColor: [
                        '#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { 
                            color: '#64748b',
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });
    });

    function runOutreach() {
        if (!confirm("Would you like to send AI outreach emails to all at-risk and non-submitting students?\nA summary report will also be sent to you afterwards.")) return;

        showLoading("Generating custom AI outreach messages and sending emails...");

        const formData = new FormData();
        formData.append('course_id', '{{ $selected_course_id }}');
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('teacher.outreach') }}", {
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
                if (data.sent_count === 0) {
                    alert("No recipients found. (All students are currently on track.)");
                    return;
                }

                // Render report details
                document.getElementById('outreach-sent-count').textContent = data.sent_count + " student(s)";
                
                const container = document.getElementById('outreach-logs-container');
                container.innerHTML = ''; // reset

                data.logs.forEach(log => {
                    const logItem = document.createElement('div');
                    logItem.className = "p-4 rounded-2xl bg-slate-950/50 border border-slate-900 space-y-2.5 text-xs";
                    
                    const topRow = document.createElement('div');
                    topRow.className = "flex justify-between items-center";
                    topRow.innerHTML = `
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-200">${log.name}</span>
                            <span class="text-[10px] text-slate-500 font-mono">(${log.email})</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">${log.type}</span>
                    `;

                    const reasonRow = document.createElement('div');
                    reasonRow.className = "text-[11px] text-slate-400";
                    reasonRow.innerHTML = `<strong>Reason:</strong> ${log.reason}`;

                    const msgBox = document.createElement('div');
                    msgBox.className = "p-3 rounded-lg bg-slate-900/60 border border-slate-950 text-slate-300 whitespace-pre-line leading-relaxed text-[11px]";
                    msgBox.textContent = log.message;

                    logItem.appendChild(topRow);
                    logItem.appendChild(reasonRow);
                    logItem.appendChild(msgBox);
                    container.appendChild(logItem);
                });

                lucide.createIcons();

                // Open modal
                const modal = document.getElementById('outreach-modal');
                modal.classList.remove('pointer-events-none', 'opacity-0');
                modal.classList.add('opacity-100');

            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert("An error occurred while sending outreach emails.");
        });
    }

    function closeOutreachModal() {
        const modal = document.getElementById('outreach-modal');
        modal.classList.remove('opacity-100');
        modal.classList.add('pointer-events-none', 'opacity-0');
        window.location.reload();
    }
</script>
@endsection
