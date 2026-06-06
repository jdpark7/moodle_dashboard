<?php

namespace Modules\MoodleDash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\MoodleDash\Models\Course;
use Modules\MoodleDash\Models\Enrollment;
use Modules\MoodleDash\Models\Assignment;
use Modules\MoodleDash\Models\Submission;
use Modules\MoodleDash\Services\AiMessageService;
use Modules\MoodleDash\Mail\EncouragementMail;
use Modules\MoodleDash\Mail\TeacherSummaryMail;
use Exception;

class TeacherController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return redirect()->route('login')->with('warning', '교수자 계정으로 로그인해 주세요.');
        }

        try {
            // 1. Fetch courses assigned/managed by the teacher
            $courses = Course::whereIn('id', [101, 102, 103])->get();
            if ($courses->isEmpty()) {
                return view('moodledash::teacher_dashboard', ['courses' => []]);
            }

            // 2. Determine selected course
            $selectedCourseId = $request->query('course_id');
            if ($selectedCourseId) {
                $selectedCourseId = intval($selectedCourseId);
            } else {
                $selectedCourseId = $courses[0]->id;
            }

            $selectedCourse = $courses->where('id', $selectedCourseId)->first();
            if (!$selectedCourse) {
                $selectedCourse = $courses[0];
                $selectedCourseId = $selectedCourse->id;
            }

            // 3. Fetch enrolled students in the selected course
            $students = $selectedCourse->users;

            // 4. Perform analytics calculations
            $totalStudents = count($students);
            $avgProgress = 0;
            $pendingGradesCount = 0;
            $riskStudents = [];
            $gradesDistribution = [
                'A (90-100)' => 0,
                'B (80-89)' => 0,
                'C (70-79)' => 0,
                'D (60-69)' => 0,
                'F (Below 60)' => 0
            ];

            if ($totalStudents > 0) {
                $progressSum = 0;
                foreach ($students as $s) {
                    $progress = $s->pivot->progress;
                    $progressSum += $progress;

                    $lastAccessDays = (time() - $s->lastaccess) / 86400;
                    $isRisk = ($progress < 45) || ($lastAccessDays >= 7);

                    // Dynamically calculate grades based on progress
                    $gradeVal = intval($progress * 0.9 + 5);
                    $s->grade = $gradeVal;
                    $s->feedback = $s->pivot->feedback;

                    // Map grades distribution
                    if ($gradeVal >= 90) {
                        $gradesDistribution['A (90-100)']++;
                    } elseif ($gradeVal >= 80) {
                        $gradesDistribution['B (80-89)']++;
                    } elseif ($gradeVal >= 70) {
                        $gradesDistribution['C (70-79)']++;
                    } elseif ($gradeVal >= 60) {
                        $gradesDistribution['D (60-69)']++;
                    } else {
                        $gradesDistribution['F (Below 60)']++;
                    }

                    // Map last access string
                    if ($lastAccessDays < 1) {
                        $s->lastaccess_formatted = "오늘 접속";
                    } elseif ($lastAccessDays < 2) {
                        $s->lastaccess_formatted = "어제 접속";
                    } else {
                        $s->lastaccess_formatted = intval($lastAccessDays) . "일 전 접속";
                    }

                    $s->is_risk_computed = $isRisk;
                    if ($isRisk) {
                        $riskStudents[] = $s;
                    }
                }
                $avgProgress = intval($progressSum / $totalStudents);
            }

            // 5. Fetch assignments & submissions to count pending grades
            $assignments = Assignment::where('course_id', $selectedCourseId)->get();
            $assignmentsSummary = [];

            foreach ($assignments as $a) {
                $submissions = Submission::where('assignment_id', $a->id)->get();
                $submittedCount = $submissions->where('status', 'submitted')->count();
                $gradedCount = $submissions->where('status', 'submitted')->whereNotNull('grade')->count();
                $allGrades = $submissions->where('status', 'submitted')->whereNotNull('grade')->pluck('grade')->toArray();

                $avgScore = count($allGrades) > 0 
                    ? round(array_sum($allGrades) / count($allGrades), 1) 
                    : 0.0;

                $pendingGradesCount += ($submittedCount - $gradedCount);

                $assignmentsSummary[] = [
                    'id' => $a->id,
                    'name' => $a->name,
                    'deadline' => date('Y-m-d H:i', $a->deadline),
                    'submitted_text' => "{$submittedCount} / {$totalStudents}",
                    'submission_rate' => $totalStudents ? intval(($submittedCount / $totalStudents) * 100) : 0,
                    'graded_text' => $submittedCount ? "{$gradedCount} / {$submittedCount}" : "0 / 0",
                    'is_pending' => $gradedCount < $submittedCount,
                    'avg_score' => $avgScore,
                    'max_score' => $a->maxgrade
                ];
            }

            // Get weekly traffic datasets based on course selection
            $weeklyLoginTrends = [20, 22, 19, 21, 23, 8, 12];
            if ($selectedCourseId == 101) {
                $weeklyLoginTrends = [28, 30, 22, 31, 26, 12, 18];
            } elseif ($selectedCourseId == 102) {
                $weeklyLoginTrends = [41, 38, 43, 35, 39, 14, 25];
            }

            $context = [
                'courses' => $courses,
                'selected_course_id' => $selectedCourseId,
                'selected_course' => $selectedCourse,
                'students' => $students,
                'total_students' => $totalStudents,
                'avg_progress' => $avgProgress,
                'pending_grades_count' => $pendingGradesCount,
                'risk_students' => $riskStudents,
                'risk_students_count' => count($riskStudents),
                'assignments_summary' => $assignmentsSummary,
                // Charts data
                'chart_grades_labels' => array_keys($gradesDistribution),
                'chart_grades_data' => array_values($gradesDistribution),
                'weekly_login_trends' => $weeklyLoginTrends
            ];

            return view('moodledash::teacher_dashboard', $context);

        } catch (Exception $e) {
            return view('moodledash::teacher_dashboard', ['courses' => []])
                ->withErrors(['error' => '교수자 데이터 동기화 실패: ' . $e->getMessage()]);
        }
    }

    public function saveFeedback(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        $studentId = $request->input('student_id');
        $feedbackText = trim($request->input('feedback', ''));

        if (!$courseId || !$studentId || empty($feedbackText)) {
            return response()->json(['status' => false, 'message' => 'Required parameters are missing'], 400);
        }

        try {
            Enrollment::where('user_id', $studentId)
                      ->where('course_id', $courseId)
                      ->update(['feedback' => $feedbackText]);

            return response()->json(['status' => true, 'message' => '피드백이 데이터베이스에 저장되었습니다.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function runOutreach(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        if (!$courseId) {
            return response()->json(['status' => false, 'message' => 'Required parameters are missing'], 400);
        }

        try {
            $courseId = intval($courseId);
            $selectedCourse = Course::findOrFail($courseId);
            $students = $selectedCourse->users;

            if ($students->isEmpty()) {
                return response()->json(['status' => true, 'sent_count' => 0, 'logs' => [], 'message' => '수강생이 없습니다.']);
            }

            // Fetch assignments for the course
            $assignments = Assignment::where('course_id', $courseId)->get();

            $ai = new AiMessageService();
            $logs = [];
            $sentCount = 0;

            foreach ($students as $s) {
                $lastAccessDays = (time() - $s->lastaccess) / 86400;
                $isInactive = $lastAccessDays >= 7;

                $missingAssignName = null;
                if (!$isInactive && $assignments->isNotEmpty()) {
                    foreach ($assignments as $a) {
                        $isPast = $a->deadline < time();
                        if ($isPast) {
                            $submitted = Submission::where('assignment_id', $a->id)
                                                   ->where('user_id', $s->id)
                                                   ->where('status', 'submitted')
                                                   ->exists();
                            if (!$submitted) {
                                $missingAssignName = $a->name;
                                break;
                            }
                        }
                    }
                }

                if ($isInactive || $missingAssignName) {
                    $type = $isInactive ? "미접속" : "과제 미제출";
                    $reason = $isInactive 
                        ? "최근 " . intval($lastAccessDays) . "일 동안 LMS 미접속" 
                        : "과제 [{$missingAssignName}] 미제출";

                    // Generate AI message
                    $aiMessage = $ai->generateEncouragement(
                        $s->fullname,
                        $selectedCourse->fullname,
                        $isInactive ? intval($lastAccessDays) : null,
                        $missingAssignName,
                        Auth::user()->fullname
                    );

                    // Send email to student
                    Mail::to($s->email)->send(new EncouragementMail(
                        $s->fullname,
                        $selectedCourse->fullname,
                        $aiMessage
                    ));

                    $logs[] = [
                        'name' => $s->fullname,
                        'email' => $s->email,
                        'type' => $type,
                        'reason' => $reason,
                        'message' => $aiMessage
                    ];
                    $sentCount++;
                }
            }

            // Send summary email to teacher
            if ($sentCount > 0) {
                $teacherEmail = Auth::user()->email;
                Mail::to($teacherEmail)->send(new TeacherSummaryMail(
                    $selectedCourse->fullname,
                    $sentCount,
                    $logs
                ));
            }

            return response()->json([
                'status' => true,
                'sent_count' => $sentCount,
                'logs' => $logs
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
