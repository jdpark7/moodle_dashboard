<?php

namespace Modules\MoodleDash\Console;

use Illuminate\Console\Command;
use Modules\MoodleDash\Services\MoodleService;
use Modules\MoodleDash\Services\MockMoodleService;
use Modules\MoodleDash\Services\AiMessageService;
use Modules\MoodleDash\Mail\EncouragementMail;
use Modules\MoodleDash\Mail\TeacherSummaryMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class MoodleSendEncouragement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:send-encouragement {course_id? : 특정 강좌 ID만 발송하고 싶은 경우 입력}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moodle 학사 데이터 기반 최근 미접속 및 과제 미제출 학생에게 AI 독려 메일을 발송하고 교수자에게 보고합니다.';

    protected $aiService;

    public function __construct(AiMessageService $aiService)
    {
        parent::__construct();
        $this->aiService = $aiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Moodle AI 독려 시스템 기동 ===");
        
        $moodleUrl = env('MOODLE_URL');
        $token = env('MOODLE_TOKEN');
        
        // Since session is not available in console commands,
        // we check environment variables or fall back to Mock.
        if (!empty($moodleUrl) && !empty($token)) {
            $this->info("실제 Moodle API 모드로 구동 중: {$moodleUrl}");
            $client = new MoodleService($moodleUrl, $token);
            $isDemo = false;
        } else {
            $this->info("API 정보가 없어 데모(Mock) 모드로 구동 중");
            $client = new MockMoodleService();
            $isDemo = true;
        }

        try {
            $siteInfo = $client->getSiteInfo($isDemo ? 'teacher' : null);
            $teacherName = $siteInfo['fullname'] ?? '담당 교수';
            
            $courses = $client->getCourses(null, 'teacher');
            $targetCourseId = $this->argument('course_id');

            if ($targetCourseId) {
                $targetCourseId = intval($targetCourseId);
                $courses = array_filter($courses, function($c) use ($targetCourseId) {
                    return $c['id'] == $targetCourseId;
                });
            }

            if (empty($courses)) {
                $this->warn("처리할 강좌가 없습니다.");
                return 0;
            }

            foreach ($courses as $course) {
                $this->info("\n[강좌 처리] {$course['fullname']} (ID: {$course['id']})");
                
                // Get students
                $students = $client->getEnrolledUsers($course['id']);
                if (empty($students)) {
                    $this->warn("- 수강생이 없습니다.");
                    continue;
                }

                // Get assignments
                $assignData = $client->getAssignments([$course['id']]);
                $assignmentsList = [];
                $assignIds = [];
                foreach ($assignData['courses'] ?? [] as $ac) {
                    foreach ($ac['assignments'] ?? [] as $a) {
                        $assignmentsList[] = $a;
                        $assignIds[] = $a['id'];
                    }
                }

                // Get submissions
                $submissionsByAssign = [];
                if (!empty($assignIds)) {
                    $subData = $client->getSubmissions($assignIds);
                    foreach ($subData['assignments'] ?? [] as $ad) {
                        $submissionsByAssign[$ad['assignment']] = $ad['submissions'] ?? [];
                    }
                }

                $logs = [];
                $sentCount = 0;

                foreach ($students as $s) {
                    $lastAccessDays = (time() - $s['lastaccess']) / 86400;
                    $isInactive = $lastAccessDays >= 7;
                    
                    // Check missing assignments
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
                                    break; // prioritize first missing homework
                                }
                            }
                        }
                    }

                    // Trigger encouragement if student meets criteria
                    if ($isInactive || $missingAssignName) {
                        $type = $isInactive ? "미접속" : "과제 미제출";
                        $reason = $isInactive 
                            ? "최근 " . intval($lastAccessDays) . "일 동안 LMS 미접속" 
                            : "과제 [{$missingAssignName}] 미제출";

                        $this->line("- 대상자 감지: {$s['fullname']} ({$s['email']}) / 사유: {$reason}");

                        // 1. Generate AI Encouraging message
                        $aiMessage = $this->aiService->generateEncouragement(
                            $s['fullname'],
                            $course['fullname'],
                            $isInactive ? intval($lastAccessDays) : null,
                            $missingAssignName,
                            $teacherName
                        );

                        // 2. Dispatch email to student
                        try {
                            Mail::to($s['email'])->send(new EncouragementMail(
                                $s['fullname'],
                                $course['fullname'],
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
                            $this->info("  -> 이메일 발송 완료 (Log 기록됨)");
                        } catch (Exception $mailEx) {
                            $this->error("  -> 이메일 발송 실패: " . $mailEx->getMessage());
                        }
                    }
                }

                // 3. Email summary report to teacher
                if ($sentCount > 0) {
                    $teacherEmail = $isDemo ? 'prof_kim@univ.ac.kr' : (($siteInfo['username'] ?? 'teacher') . '@univ.ac.kr');
                    $this->info("- 교수자 요약 보고 이메일 발송 중: {$teacherEmail}");
                    
                    try {
                        Mail::to($teacherEmail)->send(new TeacherSummaryMail(
                            $course['fullname'],
                            $sentCount,
                            $logs
                        ));
                        $this->info("  -> 요약 이메일 보고 완료!");
                    } catch (Exception $tMailEx) {
                        $this->error("  -> 요약 이메일 보고 실패: " . $tMailEx->getMessage());
                    }
                } else {
                    $this->line("- 독려 조치할 대상 수강생이 없습니다.");
                }
            }

            $this->info("\n=== Moodle AI 독려 배치 작업 종료 ===");
            return 0;

        } catch (Exception $e) {
            $this->error("배치 도중 예외 발생: " . $e->getMessage());
            return 1;
        }
    }
}
