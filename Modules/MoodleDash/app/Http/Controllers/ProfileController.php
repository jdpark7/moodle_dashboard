<?php

namespace Modules\MoodleDash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\MoodleDash\Models\User;
use Exception;

class ProfileController extends Controller
{
    public function edit()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please log in to continue.');
        }

        $user = Auth::user();
        return view('moodledash::profile_edit', compact('user'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please log in to continue.');
        }

        $user = Auth::user();

        $rules = [
            'lastname' => 'required|string|max:50',
            'firstname' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'userpictureurl' => 'nullable|url',
            'userpicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Validate password changes if password is provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $request->validate($rules, [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 6 characters.',
            'userpicture.image' => 'The file must be an image.',
            'userpicture.max' => 'The image size cannot exceed 2MB.'
        ]);

        try {
            $user->lastname = trim($request->input('lastname'));
            $user->firstname = trim($request->input('firstname'));
            $user->email = trim($request->input('email'));
            $user->phone_number = trim($request->input('phone_number'));
            $user->address = trim($request->input('address'));
            
            if ($request->hasFile('userpicture')) {
                $file = $request->file('userpicture');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->userpictureurl = '/uploads/avatars/' . $filename;
            } elseif ($request->filled('userpictureurl')) {
                $user->userpictureurl = trim($request->input('userpictureurl'));
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();

            // Refresh Session values
            session([
                'fullname' => $user->fullname,
                'userpictureurl' => $user->userpictureurl,
            ]);

            return back()->with('success', 'Profile updated successfully.');

        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'An error occurred while updating profile: ' . $e->getMessage()]);
        }
    }
}
