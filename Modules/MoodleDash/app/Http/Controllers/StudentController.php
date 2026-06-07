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
            return redirect()->route('login')->with('warning', 'Please log in with a student account.');
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
                            'course_name' => $course ? $course->fullname : 'Other Course',
                            'name' => $a->name,
                            'deadline' => $a->deadline,
                            'maxgrade' => $a->maxgrade,
                            'remaining_text' => $daysLeft > 0 
                                ? "{$daysLeft}d {$hoursLeft}h left" 
                                : "{$hoursLeft}h left",
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
                'enrolled_courses' => [],
                'history_courses' => [],
                'catalog_courses' => [],
                'pending_assignments' => [],
                'total_courses' => 0,
                'avg_progress' => 0,
                'pending_count' => 0
            ])->withErrors(['error' => 'Failed to load student dashboard information: ' . $e->getMessage()]);
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
                return response()->json(['status' => false, 'message' => 'You are already enrolled in this course.']);
            }

            // Create Enrollment
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'progress' => 0,
                'feedback' => 'Student is learning actively.'
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

            session()->flash('success', 'Successfully enrolled in course!');
            return response()->json(['status' => true]);

        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
