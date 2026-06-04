<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleService;
use App\Services\MockMoodleService;
use Exception;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (session()->has('role')) {
            return session('role') === 'teacher' 
                ? redirect()->route('teacher.dashboard') 
                : redirect()->route('student.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $isDemo = $request->input('is_demo') === 'true';
        $role = $request->input('role', 'student');

        if ($isDemo) {
            session([
                'is_demo' => true,
                'role' => $role,
                'moodle_url' => 'demo-mode',
                'moodle_token' => 'demo-token'
            ]);

            $mock = new MockMoodleService();
            $siteInfo = $mock->getSiteInfo($role);

            session([
                'fullname' => $siteInfo['fullname'],
                'username' => $siteInfo['username'],
                'userid' => $siteInfo['userid'],
                'userpictureurl' => $siteInfo['userpictureurl']
            ]);

            return redirect()->route($role === 'teacher' ? 'teacher.dashboard' : 'student.dashboard')
                ->with('success', "데모 모드({$role})로 연결되었습니다.");
        }

        $moodleUrl = trim($request->input('moodle_url', ''));
        $token = trim($request->input('token', ''));
        $username = trim($request->input('username', ''));
        $password = trim($request->input('password', ''));

        if (empty($moodleUrl)) {
            return back()->withErrors(['moodle_url' => 'Moodle 사이트 URL을 입력해 주세요.']);
        }

        try {
            // Get token if username and password are provided
            if (!empty($username) && !empty($password)) {
                $token = MoodleService::getToken($moodleUrl, $username, $password);
                if (!$token) {
                    return back()->withErrors(['username' => '사용자 인증에 실패하여 토큰을 발급받을 수 없습니다.']);
                }
            }

            if (empty($token)) {
                return back()->withErrors(['token' => 'API 토큰 또는 로그인 정보를 입력해 주세요.']);
            }

            // Verify the token by getting site info
            $client = new MoodleService($moodleUrl, $token);
            $siteInfo = $client->getSiteInfo();

            session([
                'is_demo' => false,
                'role' => $role,
                'moodle_url' => $moodleUrl,
                'moodle_token' => $token,
                'fullname' => $siteInfo['fullname'] ?? $siteInfo['username'],
                'username' => $siteInfo['username'],
                'userid' => $siteInfo['userid'],
                'userpictureurl' => $siteInfo['userpictureurl'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150'
            ]);

            return redirect()->route($role === 'teacher' ? 'teacher.dashboard' : 'student.dashboard')
                ->with('success', "Moodle API 연동에 성공했습니다: " . session('fullname') . " 님");

        } catch (Exception $e) {
            return back()->withErrors(['error' => '연동 실패: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('info', '로그아웃되었습니다.');
    }
}
