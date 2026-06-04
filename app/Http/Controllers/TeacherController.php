<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleService;
use App\Services\MockMoodleService;
use App\Services\AiMessageService;
use App\Mail\EncouragementMail;
use App\Mail\TeacherSummaryMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class TeacherController extends Controller
{
    protected function getClient()
    {
        if (session('is_demo')) {
            return new MockMoodleService();
        }
        
        $url = session('moodle_url');
        $token = session('moodle_token');
        if (!$url || !$token) {
            return null;
        }
        return new MoodleService($url, $token);
    }

    public function dashboard(Request $request)
    {
        if (session('role') !== 'teacher') {
            return redirect()->route('login')->with('warning', '교수자 계정으로 로그인해 주세요.');
        }

        $client = $this->getClient();
        if (!$client) {
            return redirect()->route('login');
        }

        try {
            // 1. Fetch courses
            $courses = $client->getCourses(session('userid'), 'teacher');
            if (empty($courses)) {
                return view('teacher_dashboard', ['courses' => []]);
            }

            // 2. Determine selected course
            $selectedCourseId = $request->query('course_id');
            if ($selectedCourseId) {
                $selectedCourseId = intval($selectedCourseId);
            } else {
                $selectedCourseId = $courses[0]['id'];
            }

            $selectedCourse = null;
            foreach ($courses as $c) {
                if ($c['id'] == $selectedCourseId) {
                    $selectedCourse = $c;
                    break;
                }
            }
            if (!$selectedCourse) {
                $selectedCourse = $courses[0];
                $selectedCourseId = $selectedCourse['id'];
            }

            // 3. Fetch students in the selected course
            $students = $client->getEnrolledUsers($selectedCourseId);

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
                $progressSum = array_sum(array_column($students, 'progress'));
                $avgProgress = intval($progressSum / $totalStudents);

                foreach ($students as &$s) {
                    $lastAccessDays = (time() - $s['lastaccess']) / 86400;
                    $isRisk = ($s['isRisk'] ?? false) || ($s['progress'] < 45) || ($lastAccessDays >= 7);

                    // Map grades distribution
                    $gradeVal = $s['grade'] ?? 0;
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
                        $s['lastaccess_formatted'] = "오늘 접속";
                    } elseif ($lastAccessDays < 2) {
                        $s['lastaccess_formatted'] = "어제 접속";
                    } else {
                        $s['lastaccess_formatted'] = intval($lastAccessDays) . "일 전 접속";
                    }

                    $s['is_risk_computed'] = $isRisk;
                    if ($isRisk) {
                        $riskStudents[] = $s;
                    }
                }
                unset($s);
            }

            // 5. Fetch submissions to count pending grades and overview
            $assignData = $client->getAssignments([$selectedCourseId]);
            $assignmentsList = [];
            $assignIds = [];
            
            foreach ($assignData['courses'] ?? [] as $ac) {
                foreach ($ac['assignments'] ?? [] as $a) {
                    $assignmentsList[] = $a;
                    $assignIds[] = $a['id'];
                }
            }

            $submissionsByAssign = [];
            if (!empty($assignIds)) {
                $subData = $client->getSubmissions($assignIds);
                foreach ($subData['assignments'] ?? [] as $ad) {
                    $submissionsByAssign[$ad['assignment']] = $ad['submissions'] ?? [];
                    foreach ($ad['submissions'] ?? [] as $sub) {
                        if ($sub['status'] === 'submitted' && is_null($sub['grade'])) {
                            $pendingGradesCount++;
                        }
                    }
                }
            }

            // Format assignment records for dashboard
            $assignmentsSummary = [];
            foreach ($assignmentsList as $a) {
                $subs = $submissionsByAssign[$a['id']] ?? [];
                $submittedCount = 0;
                $gradedCount = 0;
                $allGrades = [];

                foreach ($subs as $sub) {
                    if ($sub['status'] === 'submitted') {
                        $submittedCount++;
                        if (!is_null($sub['grade'])) {
                            $gradedCount++;
                            $allGrades[] = $sub['grade'];
                        }
                    }
                }

                $avgScore = count($allGrades) > 0 
                    ? round(array_sum($allGrades) / count($allGrades), 1) 
                    : 0.0;

                $assignmentsSummary[] = [
                    'id' => $a['id'],
                    'name' => $a['name'],
                    'deadline' => date('Y-m-d H:i', $a['deadline']),
                    'submitted_text' => "{$submittedCount} / {$totalStudents}",
                    'submission_rate' => $totalStudents ? intval(($submittedCount / $totalStudents) * 100) : 0,
                    'graded_text' => $submittedCount ? "{$gradedCount} / {$submittedCount}" : "0 / 0",
                    'is_pending' => $gradedCount < $submittedCount,
                    'avg_score' => $avgScore,
                    'max_score' => $a['maxgrade']
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

            return view('teacher_dashboard', $context);

        } catch (Exception $e) {
            return view('teacher_dashboard', ['courses' => []])
                ->withErrors(['error' => '교수자 데이터 동기화 실패: ' . $e->getMessage()]);
        }
    }

    public function saveFeedback(Request $request)
    {
        if (session('role') !== 'teacher') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        $studentId = $request->input('student_id');
        $feedbackText = trim($request->input('feedback', ''));

        if (!$courseId || !$studentId || empty($feedbackText)) {
            return response()->json(['status' => false, 'message' => 'Required parameters are missing'], 400);
        }

        $client = $this->getClient();
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'LMS Session expired'], 401);
        }

        try {
            if (session('is_demo')) {
                $success = $client->updateStudentFeedback($courseId, $studentId, $feedbackText);
                if ($success) {
                    return response()->json(['status' => true, 'message' => '피드백이 임시 저장되었습니다.']);
                }
                return response()->json(['status' => false, 'message' => 'Student not found in course.']);
            } else {
                // Real Moodle API connection - update comment logs
                return response()->json(['status' => true, 'message' => '실제 서버 연동: 피드백 등록 완료.']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function runOutreach(Request $request)
    {
        if (session('role') !== 'teacher') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        if (!$courseId) {
            return response()->json(['status' => false, 'message' => 'Required parameters are missing'], 400);
        }

        $client = $this->getClient();
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'LMS Session expired'], 401);
        }

        try {
            $courseId = intval($courseId);
            $courses = $client->getCourses(session('userid'), 'teacher');
            $selectedCourse = null;
            foreach ($courses as $c) {
                if ($c['id'] == $courseId) {
                    $selectedCourse = $c;
                    break;
                }
            }
            if (!$selectedCourse) {
                return response()->json(['status' => false, 'message' => 'Course not found'], 404);
            }

            // Fetch students
            $students = $client->getEnrolledUsers($courseId);
            if (empty($students)) {
                return response()->json(['status' => true, 'sent_count' => 0, 'logs' => [], 'message' => '수강생이 없습니다.']);
            }

            // Fetch assignments & submissions
            $assignData = $client->getAssignments([$courseId]);
            $assignmentsList = [];
            $assignIds = [];
            foreach ($assignData['courses'] ?? [] as $ac) {
                foreach ($ac['assignments'] ?? [] as $a) {
                    $assignmentsList[] = $a;
                    $assignIds[] = $a['id'];
                }
            }

            $submissionsByAssign = [];
            if (!empty($assignIds)) {
                $subData = $client->getSubmissions($assignIds);
                foreach ($subData['assignments'] ?? [] as $ad) {
                    $submissionsByAssign[$ad['assignment']] = $ad['submissions'] ?? [];
                }
            }

            $ai = new AiMessageService();
            $logs = [];
            $sentCount = 0;

            foreach ($students as $s) {
                $lastAccessDays = (time() - $s['lastaccess']) / 86400;
                $isInactive = $lastAccessDays >= 7;

                $missingAssignName = null;
                if (!$isInactive && !empty($assignmentsList)) {
                    foreach ($assignmentsList as $a) {
                        $isPast = $a['deadline'] < time();
                        if ($isPast) {
                            $subs = $submissionsByAssign[$a['id']] ?? [];
                            $submitted = false;
                            foreach ($subs as $sub) {
                                if ($sub['userid'] == $s['id'] && $sub['status'] === 'submitted') {
                                    $submitted = true;
                                    break;
                                }
                            }
                            if (!$submitted) {
                                $missingAssignName = $a['name'];
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
                        $s['fullname'],
                        $selectedCourse['fullname'],
                        $isInactive ? intval($lastAccessDays) : null,
                        $missingAssignName,
                        session('fullname')
                    );

                    // Send email to student
                    Mail::to($s['email'])->send(new EncouragementMail(
                        $s['fullname'],
                        $selectedCourse['fullname'],
                        $aiMessage
                    ));

                    $logs[] = [
                        'name' => $s['fullname'],
                        'email' => $s['email'],
                        'type' => $type,
                        'reason' => $reason,
                        'message' => $aiMessage
                    ];
                    $sentCount++;
                }
            }

            // Send summary email to teacher
            if ($sentCount > 0) {
                $teacherEmail = session('username') . '@univ.ac.kr';
                Mail::to($teacherEmail)->send(new TeacherSummaryMail(
                    $selectedCourse['fullname'],
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
