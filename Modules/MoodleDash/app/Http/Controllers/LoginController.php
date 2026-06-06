<?php

namespace Modules\MoodleDash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'teacher' 
                ? redirect()->route('teacher.dashboard') 
                : redirect()->route('student.dashboard');
        }
        return view('moodledash::login');
    }

    public function login(Request $request)
    {
        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));

        if (empty($username) || empty($password)) {
            return back()->withErrors(['username' => '아이디와 비밀번호를 입력해 주세요.']);
        }

        try {
            if (Auth::attempt(['username' => $username, 'password' => $password])) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                
                // Store standard session variables used in header/views
                session([
                    'fullname' => $user->fullname,
                    'username' => $user->username,
                    'userid' => $user->id,
                    'userpictureurl' => $user->userpictureurl ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150',
                    'role' => $user->role,
                    'is_demo' => empty($user->moodle_token), // Demo Mode when no moodle_token is defined
                    'moodle_url' => empty($user->moodle_token) ? 'demo-mode' : env('MOODLE_URL', 'moodle-server'),
                    'moodle_token' => $user->moodle_token
                ]);

                return $user->role === 'teacher'
                    ? redirect()->route('teacher.dashboard')->with('success', "교수자 대시보드 로그인 성공: {$user->fullname} 님")
                    : redirect()->route('student.dashboard')->with('success', "학생 대시보드 로그인 성공: {$user->fullname} 님");
            }

            return back()->withErrors(['username' => '아이디 또는 비밀번호가 잘못되었습니다.']);

        } catch (Exception $e) {
            return back()->withErrors(['error' => '로그인 실패: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('login')->with('info', '로그아웃되었습니다.');
    }
}
