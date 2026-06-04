<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleService;
use App\Services\MockMoodleService;
use Exception;

class StudentController extends Controller
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

    public function dashboard()
    {
        if (session('role') !== 'student') {
            return redirect()->route('login')->with('warning', '학생 계정으로 로그인해 주세요.');
        }

        $client = $this->getClient();
        if (!$client) {
            return redirect()->route('login');
        }

        try {
            $userId = session('userid');
            
            // 1. Fetch courses
            if (session('is_demo')) {
                $enrolledCourses = $client->getCourses($userId, 'student');
                $historyCourses = $client->getHistoryCourses();
                $catalogCourses = $client->getCatalogCourses();
            } else {
                $courses = $client->getCourses($userId);
                $enrolledCourses = [];
                $historyCourses = [];
                $catalogCourses = []; // Real registration is done inside Moodle

                foreach ($courses as $c) {
                    $c['progress'] = $c['progress'] ?? 0;
                    $endDate = $c['enddate'] ?? 0;
                    
                    if ($c['progress'] == 100 || ($endDate < time() && $endDate > 0)) {
                        $historyCourses[] = $c;
                    } else {
                        $enrolledCourses[] = $c;
                    }
                }
            }

            // 2. Fetch pending assignments
            $courseIds = array_column($enrolledCourses, 'id');
            $pendingAssignments = [];
            
            if (!empty($courseIds)) {
                $assignData = $client->getAssignments($courseIds);
                $assignCourses = $assignData['courses'] ?? [];
                
                $allAssigns = [];
                foreach ($assignCourses as $ac) {
                    $courseId = $ac['id'];
                    // Find course name
                    $courseName = '기타 강좌';
                    foreach ($enrolledCourses as $ec) {
                        if ($ec['id'] == $courseId) {
                            $courseName = $ec['fullname'];
                            break;
                        }
                    }
                    
                    foreach ($ac['assignments'] ?? [] as $a) {
                        $allAssigns[] = [
                            'id' => $a['id'],
                            'course_id' => $courseId,
                            'course_name' => $courseName,
                            'name' => $a['name'],
                            'deadline' => $a['deadline'],
                            'maxgrade' => $a['maxgrade']
                        ];
                    }
                }

                if (!empty($allAssigns)) {
                    $assignIds = array_column($allAssigns, 'id');
                    $subData = $client->getSubmissions($assignIds);
                    
                    // Key submissions by assignment ID
                    $submissionsByAssign = [];
                    foreach ($subData['assignments'] ?? [] as $ad) {
                        $assignId = $ad['assignment'];
                        foreach ($ad['submissions'] ?? [] as $sub) {
                            if ($sub['userid'] == $userId) {
                                $submissionsByAssign[$assignId] = $sub;
                                break;
                            }
                        }
                    }

                    // Map status and construct remaining times
                    foreach ($allAssigns as $a) {
                        $sub = $submissionsByAssign[$a['id']] ?? null;
                        $status = $sub['status'] ?? 'new';
                        $isPast = $a['deadline'] < time();

                        if (in_array($status, ['new', 'abc']) && !$isPast) {
                            $timeLeft = $a['deadline'] - time();
                            $daysLeft = intval($timeLeft / 86400);
                            $hoursLeft = intval(($timeLeft % 86400) / 3600);

                            $a['remaining_text'] = $daysLeft > 0 
                                ? "{$daysLeft}일 {$hoursLeft}시간 남음" 
                                : "{$hoursLeft}시간 남음";
                            $a['days_left'] = $daysLeft;
                            
                            $pendingAssignments[] = $a;
                        }
                    }
                }

                // Sort pending assignments by deadline ASC
                usort($pendingAssignments, function($a, $b) {
                    return $a['deadline'] <=> $b['deadline'];
                });
            }

            // 3. Compute stats
            $totalCourses = count($enrolledCourses);
            $avgProgress = 0;
            if ($totalCourses > 0) {
                $progressSum = array_sum(array_column($enrolledCourses, 'progress'));
                $avgProgress = intval($progressSum / $totalCourses);
            }

            $context = [
                'enrolled_courses' => $enrolledCourses,
                'history_courses' => $historyCourses,
                'catalog_courses' => $catalogCourses,
                'pending_assignments' => $pendingAssignments,
                'total_courses' => $totalCourses,
                'avg_progress' => $avgProgress,
                'pending_count' => count($pendingAssignments)
            ];

            return view('student_dashboard', $context);

        } catch (Exception $e) {
            return view('student_dashboard', [
                'enrolled_courses' => []
            ])->withErrors(['error' => '학습 정보를 불러오지 못했습니다: ' . $e->getMessage()]);
        }
    }

    public function enroll(Request $request)
    {
        if (session('role') !== 'student') {
            return response()->json(['status' => false, 'message' => 'Unauthorized request'], 401);
        }

        $courseId = $request->input('course_id');
        if (!$courseId) {
            return response()->json(['status' => false, 'message' => 'Course ID is missing'], 400);
        }

        $client = $this->getClient();
        if (!$client) {
            return response()->json(['status' => false, 'message' => 'LMS Session expired'], 401);
        }

        try {
            $result = $client->selfEnrolUser($courseId);
            if (isset($result['status']) && $result['status']) {
                session()->flash('success', '수강신청이 성공적으로 완료되었습니다!');
                return response()->json(['status' => true]);
            }
            return response()->json([
                'status' => false,
                'message' => $result['message'] ?? '수강 신청 실패'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
