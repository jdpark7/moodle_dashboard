<?php $__env->startSection('title', '학습 분석 대시보드 (교수자용)'); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in space-y-8">
    
    <!-- Header with Course Selector -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <h2 class="text-2xl font-bold text-white font-sans"><?php echo e(session('fullname')); ?> 교수님 👨‍🏫</h2>
            <p class="text-slate-400 text-sm mt-1">담당하고 계시는 과목들의 세부 진도현황 및 학습 참여도 분석을 제공합니다.</p>
        </div>
        
        <!-- Course Selector Dropdown -->
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-sm font-semibold text-slate-400">분석 과목 선택:</span>
            <div class="relative">
                <select id="course-switcher" onchange="switchCourse(this.value)" class="appearance-none bg-slate-950/80 border border-slate-850 hover:border-slate-800 focus:border-brand-500 text-slate-200 text-sm font-bold rounded-xl py-3 pl-4 pr-10 focus:outline-none transition-colors shadow-lg cursor-pointer">
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c['id']); ?>" <?php echo e($c['id'] == $selected_course_id ? 'selected' : ''); ?>>
                            <?php echo e($c['fullname']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Course Information Summary -->
    <div class="glass p-6 rounded-3xl border border-slate-850 bg-gradient-to-r from-slate-950/60 to-indigo-950/10">
        <h3 class="text-lg font-bold text-white mb-2"><?php echo e($selected_course['fullname']); ?></h3>
        <p class="text-sm text-slate-400 leading-relaxed max-w-4xl"><?php echo e(strip_tags($selected_course['summary'] ?? '이 강좌에 등록된 요약 내용이 없습니다.')); ?></p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow-md">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white"><?php echo e($total_students); ?>명</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">총 수강 학생 수</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shadow-md">
                <i data-lucide="line-chart" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white"><?php echo e($avg_progress); ?>%</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">학급 평균 진도율</span>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl border border-slate-800 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shadow-md">
                <i data-lucide="file-warning" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="block text-2xl font-bold text-white"><?php echo e($pending_grades_count); ?>건</span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">채점 대기중인 과제</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Chart 1: Activity Line Chart -->
        <div class="lg:col-span-3 glass p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-6">
                <i data-lucide="activity" class="text-brand-400 w-5 h-5"></i>
                학습 참여도 추이 (주간 학생 로그인 횟수)
            </h3>
            <div class="h-64 relative">
                <canvas id="loginChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Grade Bar Chart -->
        <div class="lg:col-span-2 glass p-6 rounded-3xl border border-slate-800">
            <h3 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-6">
                <i data-lucide="bar-chart-3" class="text-indigo-400 w-5 h-5"></i>
                종합 예상 평가 등급 분포
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
                    수강생 학습 현황 목록
                </h3>
                
                <!-- Search bar -->
                <div class="relative w-48 md:w-64">
                    <input type="text" id="roster-search" onkeyup="filterRoster()" class="w-full bg-slate-950/60 border border-slate-850 focus:border-brand-500 rounded-xl py-2 pl-9 pr-4 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="학생 검색 (이름)...">
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
                                <th class="py-4 px-5">학생 프로필</th>
                                <th class="py-4 px-5">마지막 LMS 접속</th>
                                <th class="py-4 px-5 text-center">진도율</th>
                                <th class="py-4 px-5 text-center">평가 성적</th>
                                <th class="py-4 px-5 text-center">참여 상태</th>
                                <th class="py-4 px-5 text-center">동적 진단</th>
                            </tr>
                        </thead>
                        <tbody id="roster-body" class="divide-y divide-slate-850">
                            <?php if(!empty($students)): ?>
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-slate-900/10 transition-colors">
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                <img src="<?php echo e($s['profileimageurl']); ?>" alt="Profile" class="w-8.5 h-8.5 rounded-lg object-cover ring-2 ring-indigo-500/10 bg-slate-800 shrink-0">
                                                <div>
                                                    <span class="block text-xs font-bold text-slate-200 student-name"><?php echo e($s['fullname']); ?></span>
                                                    <span class="text-[10px] text-slate-500 font-mono"><?php echo e($s['email']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-xs text-slate-400">
                                            <?php echo e($s['lastaccess_formatted']); ?>

                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="flex flex-col items-center justify-center gap-1.5 max-w-[80px] mx-auto">
                                                <span class="text-xs font-semibold text-brand-400"><?php echo e($s['progress']); ?>%</span>
                                                <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-brand-500 h-full rounded-full" style="width: <?php echo e($s['progress']); ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-center text-xs font-bold text-slate-300">
                                            <?php echo e($s['grade']); ?>점
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <?php if($s['is_risk_computed']): ?>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">미접속/학습경고</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">정상 이수중</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <button type="button" onclick="openStudentModal(<?php echo e($s['id']); ?>, '<?php echo e($s['fullname']); ?>', '<?php echo e($s['email']); ?>', '<?php echo e($s['profileimageurl']); ?>', <?php echo e($s['progress']); ?>, <?php echo e($s['grade']); ?>, '<?php echo e(addslashes($s['feedback'])); ?>', '<?php echo e($s['is_risk_computed']); ?>')" class="px-3 py-1.5 text-[10px] font-bold rounded-lg border border-slate-700 hover:border-slate-500 text-slate-300 hover:text-white transition-all bg-slate-900/40">
                                                상세 분석
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500 text-sm">
                                        현재 수강 중인 학생이 없습니다.
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                    주의 대상자 경보 모니터 (위험군)
                </h3>
                <button type="button" onclick="runOutreach()" class="px-2.5 py-1.5 rounded-xl text-[9px] font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 flex items-center gap-1.5 shadow transition-colors select-none">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    AI 독려 메일 발송
                </button>
            </div>

            <div class="glass p-6 rounded-3xl border border-slate-800/80 shadow-xl space-y-4 max-h-[460px] overflow-y-auto">
                <?php if(!empty($risk_students)): ?>
                    <p class="text-xs text-rose-400/90 font-medium">⚠️ 7일 이상 로그인하지 않거나 학업 진도율이 45% 미만인 관심 수강생입니다.</p>
                    
                    <div class="space-y-3.5">
                        <?php $__currentLoopData = $risk_students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3.5 rounded-xl bg-rose-500/5 border border-rose-500/15 flex items-center gap-3.5">
                                <img src="<?php echo e($s['profileimageurl']); ?>" alt="Profile" class="w-9 h-9 rounded-lg object-cover ring-2 ring-rose-500/20 bg-slate-800 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-200"><?php echo e($s['fullname']); ?></span>
                                    <p class="text-[10px] text-rose-400 mt-0.5">
                                        진도율: <?php echo e($s['progress']); ?>% • 마지막 접속: <?php echo e($s['lastaccess_formatted']); ?>

                                    </p>
                                </div>
                                <button type="button" onclick="openStudentModal(<?php echo e($s['id']); ?>, '<?php echo e($s['fullname']); ?>', '<?php echo e($s['email']); ?>', '<?php echo e($s['profileimageurl']); ?>', <?php echo e($s['progress']); ?>, <?php echo e($s['grade']); ?>, '<?php echo e(addslashes($s['feedback'])); ?>', '<?php echo e($s['is_risk_computed']); ?>')" class="w-8 h-8 rounded-lg flex items-center justify-center border border-rose-500/20 text-rose-400 hover:bg-rose-500/10 transition-colors" title="진단 피드백">
                                    <i data-lucide="notebook-pen" class="w-4 h-4"></i>
                                </button>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-12 h-12 mx-auto rounded-full bg-green-500/10 text-green-400 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <p class="text-slate-300 font-semibold text-sm">경고 관찰 대상 없음</p>
                        <p class="text-slate-500 text-xs mt-1">모든 학생들이 적극적으로 학업을 이수하고 있습니다.</p>
                    </div>
                <?php endif; ?>
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
                        <th class="py-4 px-5">평가 항목명</th>
                        <th class="py-4 px-5">마감 기한</th>
                        <th class="py-4 px-5 text-center">제출 현황</th>
                        <th class="py-4 px-5 text-center">채점 완료율</th>
                        <th class="py-4 px-5 text-center">학급 평균 점수</th>
                        <th class="py-4 px-5 text-center">진행</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850 text-slate-300 text-xs">
                    <?php if(!empty($assignments_summary)): ?>
                        <?php $__currentLoopData = $assignments_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-900/10 transition-colors">
                                <td class="py-4 px-5 font-bold text-slate-200">
                                    <?php echo e($a['name']); ?>

                                </td>
                                <td class="py-4 px-5 text-slate-450">
                                    <?php echo e($a['deadline']); ?>

                                </td>
                                <td class="py-4 px-5 text-center font-bold">
                                    <span><?php echo e($a['submitted_text']); ?>명</span>
                                    <span class="text-slate-500 font-normal">(<?php echo e($a['submission_rate']); ?>%)</span>
                                </td>
                                <td class="py-4 px-5 text-center font-bold">
                                    <?php if($a['is_pending']): ?>
                                        <span class="text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded"><?php echo e($a['graded_text']); ?> (채점대기)</span>
                                    <?php else: ?>
                                        <span class="text-green-400 bg-green-500/10 border border-green-500/20 px-2 py-0.5 rounded"><?php echo e($a['graded_text']); ?> (완료)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-5 text-center font-bold text-brand-400">
                                    <?php echo e($a['avg_score']); ?> / <?php echo e($a['max_score']); ?>점
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="text-[10px] text-slate-500 font-semibold uppercase">LMS 연동됨</span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">
                                이 과목에 등록된 과제/평가가 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
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
                    <h3 id="modal-title" class="text-base font-bold text-white">이름</h3>
                    <p id="modal-email" class="text-[11px] text-slate-500 font-mono">이메일</p>
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
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">이수 진도율</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 text-center">
                    <span id="modal-grade" class="block text-lg font-bold text-indigo-400">0점</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">예상 획득 성적</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 text-center">
                    <span id="modal-status" class="block text-[11px] font-bold">진단중</span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">참여 경고현황</span>
                </div>
            </div>

            <!-- Dynamic Feedback Form -->
            <div class="space-y-2">
                <label for="modal-feedback-input" class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">학습 활동 평가 및 소견 피드백</label>
                <p class="text-[11px] text-slate-500">주의 관찰 피드백을 기록하여 학생 모니터링 시 소견을 저장합니다. (동적 로컬 적용)</p>
                <input type="hidden" id="modal-student-id">
                <textarea id="modal-feedback-input" rows="3" class="w-full bg-slate-950/60 border border-slate-850 focus:border-brand-500 rounded-xl p-3 text-xs text-slate-100 placeholder-slate-700 focus:outline-none transition-colors" placeholder="피드백 코멘트를 입력해 주세요."></textarea>
                <div class="flex justify-end pt-1">
                    <button type="button" onclick="saveFeedback()" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center gap-1.5">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        피드백 소견 저장
                    </button>
                </div>
            </div>

            <!-- Details Roster Activity checklist -->
            <div class="space-y-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">개별 활동 이수 상세 항목</span>
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
                    <h3 class="text-sm font-bold text-white">AI 독려 메일 발송 결과 보고서</h3>
                    <p class="text-[10px] text-slate-500">수강생별 AI 생성 메시지 발송 완료 목록입니다. (laravel.log에 이메일 내용 기록됨)</p>
                </div>
            </div>
            <button type="button" onclick="closeOutreachModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-850 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-850 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-semibold">총 발송 완료 대상자:</span>
                <span id="outreach-sent-count" class="text-base font-bold text-rose-400">0명</span>
            </div>

            <div class="space-y-3.5 max-h-[45vh] overflow-y-auto pr-1" id="outreach-logs-container">
                <!-- Dynamically loaded list of logs -->
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra_js'); ?>
<script>
    function switchCourse(courseId) {
        showLoading("학습 데이터를 분석하는 중...");
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
        document.getElementById('modal-grade').textContent = grade + "점";
        document.getElementById('modal-feedback-input').value = feedback;
        
        const statusBadge = document.getElementById('modal-status');
        if (isRisk === '1' || isRisk === 1 || isRisk === 'True' || isRisk === true) {
            statusBadge.textContent = "학습 위험군";
            statusBadge.className = "block text-rose-400 text-xs font-bold py-1";
        } else {
            statusBadge.textContent = "이수 정상";
            statusBadge.className = "block text-green-400 text-xs font-bold py-1";
        }

        const activityContainer = document.getElementById('modal-activity-items');
        activityContainer.innerHTML = ''; 

        const actTemplates = [
            { name: "[과제] 1주차 환경 설정 및 알고리즘 구현", status: "completed", val: "95 / 100점" },
            { name: "[과제] 2주차 복잡도 최적화 및 테스트케이스 분석", status: progress > 30 ? "completed" : "pending", val: progress > 30 ? "88 / 100점" : "미제출" },
            { name: "[퀴즈] 1학기 중간 점검 퀴즈 평가", status: progress > 50 ? "completed" : "pending", val: progress > 50 ? "18 / 20점" : "미응시" },
            { name: "[과제] 기말 파이널 프로젝트 텀시트 제안", status: progress > 75 ? "completed" : "pending", val: progress > 75 ? "제출 완료" : "미제출" }
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
        const courseId = '<?php echo e($selected_course_id); ?>';
        
        if (!feedbackText.trim()) {
            alert("피드백 코멘트를 입력해주세요.");
            return;
        }

        showLoading("피드백 소견을 저장하는 중...");

        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('course_id', courseId);
        formData.append('feedback', feedbackText);
        // Laravel CSRF token
        formData.append('_token', '<?php echo e(csrf_token()); ?>');

        fetch("<?php echo e(route('teacher.feedback')); ?>", {
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
                alert("에러: " + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert("피드백 저장 실패");
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Line Chart: Weekly Activities
        const loginCtx = document.getElementById('loginChart').getContext('2d');
        new Chart(loginCtx, {
            type: 'line',
            data: {
                labels: ['월', '화', '수', '목', '금', '토', '일'],
                datasets: [{
                    label: '접속 트래픽',
                    data: <?php echo json_encode($weekly_login_trends); ?>,
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
                labels: <?php echo json_encode($chart_grades_labels); ?>,
                datasets: [{
                    label: '학생 수',
                    data: <?php echo json_encode($chart_grades_data); ?>,
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
        if (!confirm("주의 위험군 및 과제 미제출 수강생 전원에게 AI 격려 메일을 발송하시겠습니까?\n발송 후 교수자님께도 요약 메일 보고가 전달됩니다.")) return;

        showLoading("AI로 맞춤형 격려 문구를 생성하고 메일을 발송하는 중...");

        const formData = new FormData();
        formData.append('course_id', '<?php echo e($selected_course_id); ?>');
        formData.append('_token', '<?php echo e(csrf_token()); ?>');

        fetch("<?php echo e(route('teacher.outreach')); ?>", {
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
                    alert("발송 대상자가 없습니다. (모든 학생이 정상 수강 중입니다.)");
                    return;
                }

                // Render report details
                document.getElementById('outreach-sent-count').textContent = data.sent_count + "명";
                
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
                    reasonRow.innerHTML = `<strong>사유:</strong> ${log.reason}`;

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
                alert("에러: " + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert("독려 메일 발송 처리 중 오류가 발생했습니다.");
        });
    }

    function closeOutreachModal() {
        const modal = document.getElementById('outreach-modal');
        modal.classList.remove('opacity-100');
        modal.classList.add('pointer-events-none', 'opacity-0');
        window.location.reload();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('moodledash::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\moodledashboard-laravel\Modules/MoodleDash/resources/views/teacher_dashboard.blade.php ENDPATH**/ ?>