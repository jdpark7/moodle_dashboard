<?php $__env->startSection('title', '내 프로필 수정'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto fade-in">
    <div class="glass p-8 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Title / Header -->
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide">내 프로필 수정</h1>
                <p class="text-xs text-slate-400 mt-0.5">개인정보 및 로그인 비밀번호를 변경합니다</p>
            </div>
            <div class="ml-auto text-right">
                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider <?php echo e($user->role === 'teacher' ? 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/25' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/25'); ?>">
                    <?php echo e($user->role === 'teacher' ? '교수자' : '학생'); ?>

                </span>
            </div>
        </div>

        <!-- Form for Profile Modification -->
        <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" class="space-y-5 relative z-10" onsubmit="showLoading('정보 수정 반영 중...')">
            <?php echo csrf_field(); ?>

            <!-- Username (Read-only) -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">아이디 (변경 불가)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-600">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="text" readonly value="<?php echo e($user->username); ?>"
                           class="w-full bg-slate-950/20 border border-slate-900/50 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-400 cursor-not-allowed focus:outline-none">
                </div>
            </div>

            <!-- Full Name (Last / First) -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="lastname" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">성 (Last Name)</label>
                    <input type="text" name="lastname" id="lastname" required value="<?php echo e(old('lastname', $user->lastname)); ?>"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors">
                </div>
                <div class="space-y-1">
                    <label for="firstname" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">이름 (First Name)</label>
                    <input type="text" name="firstname" id="firstname" required value="<?php echo e(old('firstname', $user->firstname)); ?>"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-1">
                <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">이메일 주소</label>
                <input type="email" name="email" id="email" required value="<?php echo e(old('email', $user->email)); ?>"
                       class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors">
            </div>

            <!-- Phone Number & Address -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="phone_number" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">전화번호</label>
                    <input type="text" name="phone_number" id="phone_number" value="<?php echo e(old('phone_number', $user->phone_number)); ?>"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors">
                </div>
                <div class="space-y-1">
                    <label for="address" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">주소</label>
                    <input type="text" name="address" id="address" value="<?php echo e(old('address', $user->address)); ?>"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors">
                </div>
            </div>

            <!-- Avatar selection -->
            <div class="space-y-3">
                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">프로필 이미지 변경</label>
                <input type="hidden" name="userpictureurl" id="userpictureurl" value="<?php echo e($user->userpictureurl); ?>">
                
                <!-- File Upload -->
                <div class="space-y-1">
                    <input type="file" name="userpicture" id="userpicture" accept="image/*"
                           class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-1.5 px-3 text-xs text-slate-300 focus:outline-none transition-colors"
                           onchange="previewUploadedAvatar(this)">
                </div>

                <div class="flex items-center gap-4">
                    <img src="<?php echo e($user->userpictureurl); ?>" id="current-avatar-preview" alt="Current Avatar" class="w-14 h-14 rounded-xl object-cover ring-2 ring-indigo-500/25 bg-slate-900">
                    
                    <div class="grid grid-cols-4 gap-2 flex-1">
                        <button type="button" onclick="updateAvatarPreview(this, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150')"
                                class="relative rounded-lg overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150" alt="Avatar A" class="w-full h-full object-cover">
                        </button>
                        <button type="button" onclick="updateAvatarPreview(this, 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=150')"
                                class="relative rounded-lg overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                            <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=150" alt="Avatar B" class="w-full h-full object-cover">
                        </button>
                        <button type="button" onclick="updateAvatarPreview(this, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150')"
                                class="relative rounded-lg overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150" alt="Avatar C" class="w-full h-full object-cover">
                        </button>
                        <button type="button" onclick="updateAvatarPreview(this, 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150')"
                                class="relative rounded-lg overflow-hidden aspect-square border-2 border-transparent hover:border-brand-400 transition-all focus:outline-none bg-slate-800">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150" alt="Avatar D" class="w-full h-full object-cover">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Password Change Section (Optional) -->
            <div class="border-t border-slate-800/80 pt-4 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">비밀번호 변경 (변경할 경우만 입력)</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="password" class="text-xs font-semibold text-slate-500">새 비밀번호</label>
                        <input type="password" name="password" id="password"
                               class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                               placeholder="최소 6자 이상">
                    </div>
                    <div class="space-y-1">
                        <label for="password_confirmation" class="text-xs font-semibold text-slate-500">새 비밀번호 확인</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full bg-slate-950/40 border border-slate-800 focus:border-brand-500 rounded-xl py-2.5 px-4 text-sm text-slate-100 focus:outline-none transition-colors"
                               placeholder="비밀번호 재입력">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-3">
                <a href="<?php echo e($user->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard')); ?>"
                   class="flex-1 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 font-semibold py-3 rounded-xl transition-all duration-200 text-center">
                    대시보드로 돌아가기
                </a>
                <button type="submit" class="flex-1 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4.5 h-4.5"></i>
                    저장하기
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra_js'); ?>
<script>
    function updateAvatarPreview(button, url) {
        // Remove border from all buttons
        button.parentElement.querySelectorAll('button').forEach(btn => {
            btn.classList.remove('border-brand-400');
            btn.classList.add('border-transparent');
        });

        // Add border to selected
        button.classList.remove('border-transparent');
        button.classList.add('border-brand-400');

        // Set value and update preview image src
        document.getElementById('userpictureurl').value = url;
        document.getElementById('current-avatar-preview').src = url;
        
        // Reset file upload input
        document.getElementById('userpicture').value = '';
    }

    function previewUploadedAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('current-avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            
            // Clear preset selection borders
            document.querySelectorAll('button[onclick^="updateAvatarPreview"]').forEach(btn => {
                btn.classList.remove('border-brand-400');
                btn.classList.add('border-transparent');
            });
            // Clear preset URL input
            document.getElementById('userpictureurl').value = '';
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('moodledash::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USER\moodledashboard-laravel\Modules/MoodleDash/resources/views/profile_edit.blade.php ENDPATH**/ ?>