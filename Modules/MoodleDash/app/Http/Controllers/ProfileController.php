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
            return redirect()->route('login')->with('warning', '로그인 후 이용 가능합니다.');
        }

        $user = Auth::user();
        return view('moodledash::profile_edit', compact('user'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', '로그인 후 이용 가능합니다.');
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
            'email.unique' => '이미 등록된 이메일입니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password.min' => '비밀번호는 최소 6자 이상이어야 합니다.',
            'userpicture.image' => '올바른 이미지 파일 형식이 아닙니다.',
            'userpicture.max' => '이미지 크기는 최대 2MB까지 가능합니다.'
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

            return back()->with('success', '프로필 정보가 성공적으로 수정되었습니다.');

        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => '프로필 수정 중 오류가 발생했습니다: ' . $e->getMessage()]);
        }
    }
}
