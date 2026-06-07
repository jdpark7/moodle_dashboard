@extends('moodledash::layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="max-w-xl mx-auto my-6 fade-in">
    <div class="glass p-8 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Logo / Title -->
        <div class="text-center mb-6 relative z-10">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-xl">
                <i data-lucide="user-plus" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Sign Up</h1>
            <p class="text-slate-400 text-sm mt-1">Dashboard Portal Registration</p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" id="register-form" enctype="multipart/form-data" class="space-y-4 relative z-10" onsubmit="showLoading('Creating account...')">
            @csrf

            <!-- Role Selector -->
            <div class="space-y-2">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Account Role</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-800 hover:border-slate-700 cursor-pointer transition-all">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-emerald-400"></i>
                            <span class="text-sm font-semibold text-slate-200">Student Account</span>
                        </div>
                        <input type="radio" name="role" value="student" checked class="text-brand-500 bg-slate-900 border-slate-800 focus:ring-brand-500">
                    </label>
                    <label class="relative flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-800 hover:border-slate-700 cursor-pointer transition-all">
                        <div class="flex items-center gap-2">
                            <i data-lucide="award" class="w-4 h-4 text-indigo-400"></i>
                            <span class="text-sm font-semibold text-slate-200">Teacher Account</span>
                        </div>
                        <input type="radio" name="role" value="teacher" class="text-brand-500 bg-slate-900 border-slate-800 focus:ring-brand-500">
                    </label>
                </div>
            </div>

            <!-- Username -->
            <div class="space-y-1">
                <label for="username" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Username</label>
                <input type="text" name="username" id="username" required value="{{ old('username') }}"
                       class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                       placeholder="Enter your username">
            </div>

            <!-- Full Name (Last / First) -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="lastname" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Last Name</label>
                    <input type="text" name="lastname" id="lastname" required value="{{ old('lastname') }}"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="e.g. Smith">
                </div>
                <div class="space-y-1">
                    <label for="firstname" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">First Name</label>
                    <input type="text" name="firstname" id="firstname" required value="{{ old('firstname') }}"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="e.g. John">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1">
                <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                       placeholder="example@univ.ac.kr">
            </div>

            <!-- Password -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="password" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="Min 6 characters">
                </div>
                <div class="space-y-1">
                    <label for="password_confirmation" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="Re-enter password">
                </div>
            </div>

            <!-- Contact / Address -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="phone_number" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Phone Number (Optional)</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="010-0000-0000">
                </div>
                <div class="space-y-1">
                    <label for="address" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Address (Optional)</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                           placeholder="City, State, Zip">
                </div>
            </div>

            <!-- Profile Picture Upload & Selection -->
            <div class="space-y-1.5">
                <label for="userpicture" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Upload Profile Picture (Optional)</label>
                <input type="file" name="userpicture" id="userpicture" accept="image/*"
                       class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2 px-3 text-sm text-slate-300 focus:outline-none transition-colors">
            </div>

            <!-- Profile Picture Selection -->
            <div class="space-y-2">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Select Profile Avatar</label>
                <input type="hidden" name="userpictureurl" id="userpictureurl" value="">
                
                <div class="grid grid-cols-4 gap-3">
                    <button type="button" onclick="selectAvatar(this, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150')"
                            class="relative rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150" alt="Avatar A" class="w-full h-full object-cover">
                    </button>
                    <button type="button" onclick="selectAvatar(this, 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=150')"
                            class="relative rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                        <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=150" alt="Avatar B" class="w-full h-full object-cover">
                    </button>
                    <button type="button" onclick="selectAvatar(this, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150')"
                            class="relative rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150" alt="Avatar C" class="w-full h-full object-cover">
                    </button>
                    <button type="button" onclick="selectAvatar(this, 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150')"
                            class="relative rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150" alt="Avatar D" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-5 h-5"></i>
                Complete Registration & Sign In
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors">
                Already have an account? Sign In
            </a>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    function selectAvatar(button, url) {
        // Remove border from all buttons
        button.parentElement.querySelectorAll('button').forEach(btn => {
            btn.classList.remove('border-brand-400');
            btn.classList.add('border-transparent');
        });

        // Add border to selected
        button.classList.remove('border-transparent');
        button.classList.add('border-brand-400');

        // Set value
        document.getElementById('userpictureurl').value = url;
    }
</script>
@endsection
