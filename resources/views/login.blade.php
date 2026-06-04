@extends('layouts.app')

@section('title', 'Moodle API 연동 로그인')

@section('content')
<div class="max-w-md mx-auto my-12 fade-in">
    <div class="glass p-8 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Logo / Title -->
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-xl">
                <i data-lucide="graduation-cap" class="w-9 h-9"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Moodle LMS Portal</h1>
            <p class="text-slate-400 text-sm mt-2">Laravel Moodle API Dashboard</p>
        </div>

        <!-- Form for API / Login Connection -->
        <form method="POST" action="{{ route('login.post') }}" id="login-form" class="space-y-5 relative z-10" onsubmit="showLoading('Moodle 서버와 연동하는 중...')">
            @csrf
            <input type="hidden" name="is_demo" id="is_demo_field" value="false">
            <input type="hidden" name="role" id="role_field" value="student">

            <!-- Portal Selection Tabs (Student vs Teacher) -->
            <div class="space-y-2">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">접속 역할 선택</label>
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-950/60 rounded-xl border border-slate-800/50">
                    <button type="button" id="tab-student" onclick="setRole('student')" class="py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-brand-500 text-white shadow-lg">
                        <i data-lucide="user" class="inline-block w-4 h-4 mr-1.5 -mt-0.5"></i>학생용
                    </button>
                    <button type="button" id="tab-teacher" onclick="setRole('teacher')" class="py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-400 hover:text-slate-200">
                        <i data-lucide="award" class="inline-block w-4 h-4 mr-1.5 -mt-0.5"></i>교수자용
                    </button>
                </div>
            </div>

            <!-- Moodle Site URL -->
            <div class="space-y-1.5">
                <label for="moodle_url" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Moodle 사이트 URL</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                        <i data-lucide="link" class="w-4 h-4"></i>
                    </span>
                    <input type="url" name="moodle_url" id="moodle_url" class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="https://moodle.your-university.ac.kr" value="https://sandbox.moodledemo.net">
                </div>
            </div>

            <!-- Authentic Method Accordion -->
            <div class="space-y-4 pt-2">
                <!-- Method Selection -->
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <span>인증 방식 선택</span>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="auth_method" value="token" checked onclick="toggleAuthMethod('token')" class="text-brand-500 bg-slate-900 border-slate-800 focus:ring-brand-500">
                            <span>API 토큰</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="auth_method" value="creds" onclick="toggleAuthMethod('creds')" class="text-brand-500 bg-slate-900 border-slate-800 focus:ring-brand-500">
                            <span>ID/비밀번호</span>
                        </label>
                    </div>
                </div>

                <!-- API Token Input -->
                <div id="method-token" class="space-y-1.5">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="token" id="token" class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="wstoken 값 입력">
                    </div>
                </div>

                <!-- Username/Password Inputs -->
                <div id="method-creds" class="space-y-3 hidden">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </span>
                        <input type="text" name="username" id="username" class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Moodle 사용자 아이디">
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" id="password" class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition-colors" placeholder="Moodle 비밀번호">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Moodle API 연결하기
            </button>
        </form>

        <div class="relative my-6 text-center">
            <span class="absolute inset-x-0 top-1/2 h-[1px] bg-slate-800 -translate-y-1/2"></span>
            <span class="relative bg-[#0d1322] px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider z-10">또는</span>
        </div>

        <!-- Quick Demo Mode Experience -->
        <div class="space-y-2 relative z-10">
            <button type="button" onclick="triggerDemo()" class="w-full bg-slate-900 border border-slate-800 hover:border-slate-700 text-amber-400 font-semibold py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:bg-slate-800/40">
                <i data-lucide="sparkles" class="w-4.5 h-4.5"></i>
                데모 모드로 체험하기 (Mock Data)
            </button>
        </div>

        <!-- Guide Dropdown -->
        <div class="mt-6 border-t border-slate-850 pt-4 relative z-10">
            <details class="group text-xs text-slate-500 cursor-pointer">
                <summary class="flex justify-between items-center font-medium group-hover:text-slate-400 select-none">
                    <span>Moodle API 토큰 발급 방법 안내</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="mt-3 space-y-2 leading-relaxed text-[11px] text-slate-400 pl-1.5 border-l border-slate-800">
                    <p>대시보드는 Moodle의 웹 서비스(REST API)를 사용하여 학사 연동을 수행합니다. 다음 순서로 연동해 주세요:</p>
                    <ol class="list-decimal pl-4 space-y-1">
                        <li>Moodle 관리자 계정으로 접속합니다.</li>
                        <li><strong>사이트 관리 > 서버 > 웹 서비스 > 외부 서비스</strong>로 이동합니다.</li>
                        <li>웹 서비스를 활성화하고 REST 프로토콜을 사용 설정합니다.</li>
                        <li>웹 서비스 토큰 생성 메뉴에서 교수자 혹은 학생 계정을 선택하고, 토큰(wstoken)을 발급받아 복사합니다.</li>
                        <li>필요한 웹 서비스 기능이 권한에 포함되어 있는지 확인합니다: 
                            <code class="text-brand-300 font-mono text-[9px]">core_webservice_get_site_info</code>, 
                            <code class="text-brand-300 font-mono text-[9px]">core_enrol_get_users_courses</code>, 
                            <code class="text-brand-300 font-mono text-[9px]">mod_assign_get_assignments</code>.
                        </li>
                    </ol>
                </div>
            </details>
        </div>
    </div>
</div>
@endblock

@block('extra_js')
<script>
    function setRole(role) {
        document.getElementById('role_field').value = role;
        
        const tabStudent = document.getElementById('tab-student');
        const tabTeacher = document.getElementById('tab-teacher');
        
        if (role === 'student') {
            tabStudent.className = "py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-brand-500 text-white shadow-lg";
            tabTeacher.className = "py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-400 hover:text-slate-200";
        } else {
            tabTeacher.className = "py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-brand-500 text-white shadow-lg";
            tabStudent.className = "py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-slate-400 hover:text-slate-200";
        }
    }

    function toggleAuthMethod(method) {
        const divToken = document.getElementById('method-token');
        const divCreds = document.getElementById('method-creds');
        const inputToken = document.getElementById('token');
        const inputUser = document.getElementById('username');
        const inputPass = document.getElementById('password');

        if (method === 'token') {
            divToken.classList.remove('hidden');
            divCreds.classList.add('hidden');
            inputToken.required = true;
            inputUser.required = false;
            inputPass.required = false;
        } else {
            divToken.classList.add('hidden');
            divCreds.classList.remove('hidden');
            inputToken.required = false;
            inputUser.required = true;
            inputPass.required = true;
        }
    }

    function triggerDemo() {
        document.getElementById('is_demo_field').value = 'true';
        showLoading("데모 데이터를 불러오는 중...");
        document.getElementById('login-form').submit();
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleAuthMethod('token');
    });
</script>
@endblock
