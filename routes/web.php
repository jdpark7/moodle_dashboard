<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

// 1. Connection & Session Auth Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// 2. Student Portal Routes
Route::prefix('student')->group(function () {
    Route::get('/', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/enroll', [StudentController::class, 'enroll'])->name('student.enroll');
});

// 3. Teacher Portal Routes
Route::prefix('teacher')->group(function () {
    Route::get('/', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::post('/feedback', [TeacherController::class, 'saveFeedback'])->name('teacher.feedback');
    Route::post('/outreach', [TeacherController::class, 'runOutreach'])->name('teacher.outreach');
});
