<?php

namespace Modules\MoodleDash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\MoodleDash\Models\User;
use Modules\MoodleDash\Models\Enrollment;
use Modules\MoodleDash\Models\Assignment;
use Modules\MoodleDash\Models\Submission;
use Exception;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'teacher' 
                ? redirect()->route('teacher.dashboard') 
                : redirect()->route('student.dashboard');
        }
        return view('moodledash::register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'lastname' => 'required|string|max:50',
            'firstname' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:student,teacher',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'userpictureurl' => 'nullable|url',
            'userpicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'username.unique' => '이미 존재하는 아이디입니다.',
            'email.unique' => '이미 등록된 이메일입니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password.min' => '비밀번호는 최소 6자 이상이어야 합니다.',
            'userpicture.image' => '올바른 이미지 파일 형식이 아닙니다.',
            'userpicture.max' => '이미지 크기는 최대 2MB까지 가능합니다.'
        ]);

        try {
            // Handle uploaded file if present
            $avatar = '';
            if ($request->hasFile('userpicture')) {
                $file = $request->file('userpicture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/avatars'), $filename);
                $avatar = '/uploads/avatars/' . $filename;
            } else {
                // Fallback to avatar selector url
                $avatar = trim($request->input('userpictureurl', ''));
            }

            if (empty($avatar)) {
                $avatar = $request->input('role') === 'teacher'
                    ? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=150'
                    : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150';
            }

            // Create User
            $user = User::create([
                'username' => trim($request->input('username')),
                'lastname' => trim($request->input('lastname')),
                'firstname' => trim($request->input('firstname')),
                'email' => trim($request->input('email')),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                'phone_number' => trim($request->input('phone_number')),
                'address' => trim($request->input('address')),
                'userpictureurl' => $avatar,
                'lastaccess' => time()
            ]);

            // If registered as student, auto-enroll in CSE201 (자료구조) as a default course
            if ($user->role === 'student') {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => 101, // CSE201
                    'progress' => 0,
                    'feedback' => '수강신청을 완료하고 학습을 시작한 상태입니다.'
                ]);

                // Create initial submissions for course 101 assignments
                $assignments = Assignment::where('course_id', 101)->get();
                foreach ($assignments as $a) {
                    Submission::create([
                        'assignment_id' => $a->id,
                        'user_id' => $user->id,
                        'status' => 'new',
                        'grade' => null,
                        'timemodified' => 0
                    ]);
                }
            }

            // Login automatically
            Auth::login($user);

            // Populate Session attributes
            session([
                'fullname' => $user->fullname,
                'username' => $user->username,
                'userid' => $user->id,
                'userpictureurl' => $user->userpictureurl,
                'role' => $user->role,
                'is_demo' => true,
                'moodle_url' => 'demo-mode',
                'moodle_token' => null
            ]);

            return $user->role === 'teacher'
                ? redirect()->route('teacher.dashboard')->with('success', '교수자 회원가입 성공!')
                : redirect()->route('student.dashboard')->with('success', '학생 회원가입 성공!');

        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => '회원가입 중 오류가 발생했습니다: ' . $e->getMessage()]);
        }
    }
}
