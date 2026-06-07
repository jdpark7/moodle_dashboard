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
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 6 characters.',
            'userpicture.image' => 'The file must be an image.',
            'userpicture.max' => 'The image size cannot exceed 2MB.'
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
                    'feedback' => 'Enrollment completed. Ready to start learning.'
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
                ? redirect()->route('teacher.dashboard')->with('success', 'Teacher registration successful!')
                : redirect()->route('student.dashboard')->with('success', 'Student registration successful!');

        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'An error occurred during registration: ' . $e->getMessage()]);
        }
    }
}
