<!DOCTYPE html>
<html lang="ko" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Moodle LMS Dashboard') - Antigravity</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Outfit', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f3ff',
                            100: '#e1e7ff',
                            200: '#c8d4ff',
                            300: '#a3b7ff',
                            400: '#7992ff',
                            500: '#4f66ff',
                            600: '#3b43f7',
                            700: '#3030e3',
                            800: '#2727b8',
                            900: '#252793',
                            950: '#151557',
                        }
                    }
                }
            }
        }
    </script>
    <!-- CSS Custom Styles -->
    <style>
        :root {
            --glass-bg: rgba(17, 24, 39, 0.45);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-highlight: rgba(255, 255, 255, 0.03);
            --gradient-accent: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            --gradient-glow: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.05) 100%);
        }

        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(79, 70, 229, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            color: #f3f4f6;
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card-hover:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 12px 40px 0 rgba(99, 102, 241, 0.15);
            background: rgba(17, 24, 39, 0.6);
        }

        .text-gradient {
            background: linear-gradient(135deg, #a5b4fc 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(11, 15, 25, 0.5);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.3);
        }

        /* Animations */
        @keyframes spinner {
            to { transform: rotate(360deg); }
        }
        .animate-spinner {
            animation: spinner 0.8s linear infinite;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @yield('extra_head')
</head>
<body class="min-h-screen text-slate-200">

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#090d16]/80 backdrop-blur-md transition-opacity duration-300 pointer-events-none opacity-0">
        <div class="w-12 h-12 border-4 border-brand-500 border-t-transparent rounded-full animate-spinner mb-4"></div>
        <div class="text-lg font-medium text-slate-300" id="loading-text">Moodle 데이터를 불러오는 중...</div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
        @if (session('success'))
            <div class="toast-item glass flex items-center p-4 rounded-xl border-l-4 border-l-green-500 pointer-events-auto shadow-2xl fade-in" role="alert">
                <div class="mr-3 text-slate-300">
                    <i data-lucide="check-circle" class="text-green-400 w-5 h-5"></i>
                </div>
                <div class="text-sm font-medium text-slate-100">{{ session('success') }}</div>
                <button class="ml-auto text-slate-400 hover:text-slate-200" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
        @if (session('warning'))
            <div class="toast-item glass flex items-center p-4 rounded-xl border-l-4 border-l-amber-500 pointer-events-auto shadow-2xl fade-in" role="alert">
                <div class="mr-3 text-slate-300">
                    <i data-lucide="alert-circle" class="text-amber-400 w-5 h-5"></i>
                </div>
                <div class="text-sm font-medium text-slate-100">{{ session('warning') }}</div>
                <button class="ml-auto text-slate-400 hover:text-slate-200" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
        @if (session('info'))
            <div class="toast-item glass flex items-center p-4 rounded-xl border-l-4 border-l-indigo-500 pointer-events-auto shadow-2xl fade-in" role="alert">
                <div class="mr-3 text-slate-300">
                    <i data-lucide="info" class="text-indigo-400 w-5 h-5"></i>
                </div>
                <div class="text-sm font-medium text-slate-100">{{ session('info') }}</div>
                <button class="ml-auto text-slate-400 hover:text-slate-200" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="toast-item glass flex items-center p-4 rounded-xl border-l-4 border-l-red-500 pointer-events-auto shadow-2xl fade-in" role="alert">
                    <div class="mr-3 text-slate-300">
                        <i data-lucide="alert-triangle" class="text-red-400 w-5 h-5"></i>
                    </div>
                    <div class="text-sm font-medium text-slate-100">{{ $error }}</div>
                    <button class="ml-auto text-slate-400 hover:text-slate-200" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Layout Wrapper -->
    <div class="flex min-h-screen">
        
        <!-- Sidebar Navigation -->
        @if (session()->has('role'))
        <aside class="w-72 glass border-r border-slate-800/80 hidden md:flex flex-col shrink-0">
            <div class="p-6 border-b border-slate-800/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide font-sans">LMS Board</h1>
                    <p class="text-xs text-indigo-400/80 font-medium tracking-wider uppercase">Laravel Version</p>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                @if (session('role') === 'student')
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('student.dashboard') ? 'bg-brand-500/10 text-brand-400 border border-brand-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span>나의 수강 대시보드</span>
                    </a>
                @elseif (session('role') === 'teacher')
                    <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('teacher.dashboard') ? 'bg-brand-500/10 text-brand-400 border border-brand-500/20 font-semibold' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                        <span>학습 분석 대시보드</span>
                    </a>
                @endif
            </nav>

            <!-- User Footer -->
            <div class="p-4 border-t border-slate-800/50">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/30 border border-slate-800/30">
                    <img src="{{ session('userpictureurl') }}" alt="Profile" class="w-10 h-10 rounded-lg object-cover ring-2 ring-indigo-500/30 bg-slate-800">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-slate-200 truncate">{{ session('fullname') }}</h4>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">
                            {{ session('role') === 'teacher' ? '교수자 (Teacher)' : '학생 (Student)' }}
                        </p>
                    </div>
                    <a href="{{ route('logout') }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition-colors" title="로그아웃">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </aside>
        @endif

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 px-6 glass border-b border-slate-800/50 flex items-center justify-between">
                <div class="flex items-center gap-3 md:hidden">
                    <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center text-white">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-white">LMS Board</span>
                </div>
                
                <div class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-400">
                    <span>Moodle Server:</span>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-mono">{{ session('moodle_url', 'Not Connected') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    @if (session('is_demo'))
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Demo Mode</span>
                    @elseif (session()->has('role'))
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/20">Connected</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">Offline</span>
                    @endif
                    
                    @if (session()->has('role'))
                        <a href="{{ route('logout') }}" class="md:hidden text-slate-400 hover:text-slate-200">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page Body -->
            <div class="flex-1 p-6 md:p-8 space-y-8 overflow-y-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        lucide.createIcons();

        function showLoading(text) {
            const overlay = document.getElementById('loading-overlay');
            const overlayText = document.getElementById('loading-text');
            if (text) overlayText.textContent = text;
            overlay.classList.remove('pointer-events-none', 'opacity-0');
            overlay.classList.add('opacity-100');
        }

        function hideLoading() {
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('pointer-events-none', 'opacity-0');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('.toast-item');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-10', 'transition-all', 'duration-500');
                    setTimeout(() => toast.remove(), 500);
                }, 4000);
            });
        });
    </script>
    @yield('extra_js')
</body>
</html>
