@extends('moodledash::layouts.app')

@section('title', '학사 대시보드 로그인')

@section('content')
<div class="max-w-md mx-auto my-12 fade-in">
    <div class="glass p-8 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Logo / Title -->
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-xl animate-pulse">
                <i data-lucide="graduation-cap" class="w-9 h-9"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">학사 관리 대시보드</h1>
            <p class="text-slate-400 text-sm mt-2">사용자 인증 정보로 로그인하세요</p>
        </div>

        <!-- Form for Login Connection -->
        <form method="POST" action="{{ route('login.post') }}" id="login-form" class="space-y-5 relative z-10" onsubmit="showLoading('로그인 확인 중...')">
            @csrf

            <!-- Username Input -->
            <div class="space-y-1.5">
                <label for="username" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">아이디 (Username)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="username" id="username" required
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors"
                           placeholder="아이디를 입력하세요">
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">비밀번호 (Password)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors"
                           placeholder="비밀번호를 입력하세요">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                로그인하기
            </button>
        </form>

        <div class="mt-4 mb-2 text-center relative z-10">
            <a href="{{ route('register') }}" class="text-xs text-brand-400 hover:text-brand-300 font-semibold transition-colors">
                아직 계정이 없으신가요? 회원가입하기
            </a>
        </div>

        <!-- Quick Access Section -->
        <div class="relative my-8 text-center relative z-10">
            <span class="absolute inset-x-0 top-1/2 h-[1px] bg-slate-800/80 -translate-y-1/2"></span>
            <span class="relative bg-[#0d1322] px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider z-10">테스트 계정 빠른 로그인</span>
        </div>

        <div class="grid grid-cols-2 gap-3 relative z-10">
            <!-- Student Quick Access -->
            <button type="button" onclick="quickLogin('student_hong', 'password')"
                    class="group p-4 bg-slate-900/60 border border-slate-800 hover:border-slate-700 hover:bg-slate-800/30 rounded-2xl transition-all duration-200 text-left flex flex-col justify-between h-28">
                <div class="flex items-center justify-between w-full">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i data-lucide="user" class="w-4.5 h-4.5"></i>
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-200">학생 (홍길동)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">student_hong</p>
                </div>
            </button>

            <!-- Teacher Quick Access -->
            <button type="button" onclick="quickLogin('prof_kim', 'password')"
                    class="group p-4 bg-slate-900/60 border border-slate-800 hover:border-slate-700 hover:bg-slate-800/30 rounded-2xl transition-all duration-200 text-left flex flex-col justify-between h-28">
                <div class="flex items-center justify-between w-full">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <i data-lucide="award" class="w-4.5 h-4.5"></i>
                    </span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-200">교수자 (김민수)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">prof_kim</p>
                </div>
            </button>
        </div>

        <!-- System Status Guide -->
        <div class="mt-6 border-t border-slate-850 pt-4 relative z-10 text-[11px] text-slate-500 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-ping"></span>
                <span>SQLite DB 연동 완료</span>
            </span>
            <span class="text-slate-600 font-mono text-[9px]">learndash schema v1.0</span>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function quickLogin(username, password) {
        document.getElementById('username').value = username;
        document.getElementById('password').value = password;
        
        showLoading("빠른 로그인 처리 중...");
        
        // Brief timeout for visual effect
        setTimeout(() => {
            document.getElementById('login-form').submit();
        }, 300);
    }
</script>
@endsection
