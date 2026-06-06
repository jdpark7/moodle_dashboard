<?php

namespace Modules\MoodleDash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\MoodleDash\Models\Course;
use Modules\MoodleDash\Models\Enrollment;
use Modules\MoodleDash\Models\Assignment;
use Modules\MoodleDash\Models\Submission;
use Exception;

class StudentController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role !== 'student') {
            return redirect()->route('login')->with('warning', '학생 계정으로 로그인해 주세요.');
        }

        $user = Auth::user();

        try {
            // 1. Fetch enrolled courses and split completed/active
            $enrolledCourses = $user->courses;
            $enrolledCoursesFormatted = [];
            $historyCourses = [];

            foreach ($enrolledCourses as $c) {
                $progress = $c->pivot->progress;
                $cFormatted = [
                    'id' => $c->id,
                    'fullname' => $c->fullname,
                    'shortname' => $c->shortname,
                    'summary' => $c->summary,
                    'progress' => $progress
                ];

                if ($progress == 100) {
                    $historyCourses[] = $cFormatted;
                } else {
                    $enrolledCoursesFormatted[] = $cFormatted;
                }
            }

            // 2. Fetch catalog courses (not yet enrolled)
            $enrolledIds = $enrolledCourses->pluck('id');
            $catalogCourses = Course::whereNotIn('id', $enrolledIds)->get();

            // 3. Fetch pending assignments
            $courseIds = $enrolledCourses->pluck('id');
            $pendingAssignments = [];

            if ($courseIds->isNotEmpty()) {
                $assignments = Assignment::whereIn('course_id', $courseIds)->get();
                foreach ($assignments as $a) {
                    // Check student's submission status
                    $submission = Submission::where('assignment_id', $a->id)
                                            ->where('user_id', $user->id)
                                            ->first();

                    $status = $submission ? $submission->status : 'new';
                    $isPast = $a->deadline < time();

                    if ($status === 'new' && !$isPast) {
                        $timeLeft = $a->deadline - time();
                        $daysLeft = intval($timeLeft / 86400);
                        $hoursLeft = intval(($timeLeft % 86400) / 3600);

                        $course = $enrolledCourses->where('id', $a->course_id)->first();

                        $pendingAssignments[] = [
                            'id' => $a->id,
                            'course_id' => $a->course_id,
                            'course_name' => $course ? $course->fullname : '기타 강좌',
                            'name' => $a->name,
                            'deadline' => $a->deadline,
                            'maxgrade' => $a->maxgrade,
                            'remaining_text' => $daysLeft > 0 
                                ? "{$daysLeft}일 {$hoursLeft}시간 남음" 
                                : "{$hoursLeft}시간 남음",
                            'days_left' => $daysLeft
                        ];
                    }
                }

                // Sort by deadline ascending
                usort($pendingAssignments, function ($a, $b) {
                    return $a['deadline'] <=> $b['deadline'];
                });
            }

            // 4. Compute statistics
            $totalCourses = count($enrolledCoursesFormatted);
            $avgProgress = 0;
            if ($totalCourses > 0) {
                $progressSum = array_sum(array_column($enrolledCoursesFormatted, 'progress'));
                $avgProgress = intval($progressSum / $totalCourses);
            }

            $context = [
                'enrolled_courses' => $enrolledCoursesFormatted,
                'history_courses' => $historyCourses,
                'catalog_courses' => $catalogCourses,
                'pending_assignments' => $pendingAssignments,
                'total_courses' => $totalCourses,
                'avg_progress' => $avgProgress,
                'pending_count' => count($pendingAssignments)
            ];

            return view('moodledash::student_dashboard', $context);

        } catch (Exception $e) {
            return view('moodledash::student_dashboard', [
                'enrolled_courses' => []
            ])->withErrors(['error' => '학습 정보를 불러오지 못했습니다: ' . $e->getMessage()]);
        }
    }

    public function enroll(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'student') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        if (!$courseId) {
            return response()->json(['status' => false, 'message' => 'Course ID is missing'], 400);
        }

        $user = Auth::user();

        try {
            // Check if already enrolled
            $isEnrolled = Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->exists();
            if ($isEnrolled) {
                return response()->json(['status' => false, 'message' => '이미 수강신청된 강좌입니다.']);
            }

            // Create Enrollment
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'progress' => 0,
                'feedback' => '정상 학습 수행 중인 학생입니다.'
            ]);

            // Seed empty submissions for all course assignments
            $assignments = Assignment::where('course_id', $courseId)->get();
            foreach ($assignments as $a) {
                Submission::create([
                    'assignment_id' => $a->id,
                    'user_id' => $user->id,
                    'status' => 'new',
                    'grade' => null,
                    'timemodified' => 0
                ]);
            }

            session()->flash('success', '수강신청이 성공적으로 완료되었습니다!');
            return response()->json(['status' => true]);

        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
