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
            return back()->withErrors(['username' => 'Please enter username and password.']);
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
                    ? redirect()->route('teacher.dashboard')->with('success', "Logged into Teacher Dashboard: {$user->fullname}")
                    : redirect()->route('student.dashboard')->with('success', "Logged into Student Dashboard: {$user->fullname}");
            }

            return back()->withErrors(['username' => 'Invalid username or password.']);

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Login failed: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
