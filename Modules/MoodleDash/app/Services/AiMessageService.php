<?php

namespace Modules\MoodleDash\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class AiMessageService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Generate encouragement message for student
     *
     * @param string $studentName Name of the student
     * @param string $courseName Name of the course
     * @param int|null $inactiveDays Number of inactive days (if applicable)
     * @param string|null $missingAssignment Name of missing assignment (if applicable)
     * @return string
     */
    public function generateEncouragement($studentName, $courseName, $inactiveDays = null, $missingAssignment = null, $teacherName = null)
    {
        $teacherName = $teacherName ?: '담당 교수';

        // 1. Construct prompt
        $reason = "";
        if ($inactiveDays && $inactiveDays >= 7) {
            $reason = "최근 {$inactiveDays}일 동안 LMS 시스템에 로그인하지 않아 학습 결손이 우려되는 상황";
        } elseif ($missingAssignment) {
            $reason = "'{$missingAssignment}' 과제를 아직 제출하지 않은 상황";
        } else {
            $reason = "학습 진행 상황이 조금 더뎌 격려가 필요한 상황";
        }

        $prompt = "수강생 '{$studentName}'은 현재 '{$courseName}' 과목을 수강 중입니다.\n";
        $prompt .= "이 학생의 상태는 다음과 같습니다: {$reason}\n\n";
        $prompt .= "교수자({$teacherName})로서 이 학생에게 따뜻한 격려와 학습 독려를 보내는 메시지를 작성해 주세요.\n";
        $prompt .= "주의 사항:\n";
        $prompt .= "- 친근하고 진정성 있는 톤앤매너로 작성할 것.\n";
        $prompt .= "- 너무 딱딱하거나 강압적이지 않게 작성할 것.\n";
        $prompt .= "- 이메일 본문으로 바로 쓸 수 있도록 제목과 본문을 포함하여 3~4문장 내외로 작성해 줄 것.\n";
        $prompt .= "- 마크다운 태그를 쓰지 말고 줄바꿈을 활용한 일반 텍스트로 답해 줄 것.\n";

        // 2. Call Gemini API if Key is present
        if (!empty($this->apiKey)) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;
                
                $response = Http::timeout(10)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if (!empty($text)) {
                        return trim($text);
                    }
                }
            } catch (Exception $e) {
                logger()->error("Gemini API call failed: " . $e->getMessage());
                // Fall back to template
            }
        }

        // 3. Fallback: Rule-based custom template generator
        return $this->getMockEncouragementMessage($studentName, $courseName, $inactiveDays, $missingAssignment, $teacherName);
    }

    /**
     * Generates a realistic, highly personalized mock encouragement message
     */
    protected function getMockEncouragementMessage($studentName, $courseName, $inactiveDays, $missingAssignment, $teacherName)
    {
        $subject = "[학습 독려] 안녕하세요, {$studentName} 학생. {$courseName} 담당 교수입니다.";
        
        if ($inactiveDays && $inactiveDays >= 7) {
            return "제목: {$subject}\n\n" .
                   "안녕하세요, {$studentName} 학생.\n" .
                   "학기 진행 중에 최근 약 {$inactiveDays}일간 {$courseName} 과목의 LMS 접속 기록이 없는 것으로 확인되어 연락을 보냅니다.\n" .
                   "학업이나 일상에 어려운 점이 생겼는지 걱정이 앞섭니다. 진도율이나 학습 분량에 대한 부담이 있다면 혼자 고민하지 말고 언제든 메일이나 면담을 요청해 주세요. 조금씩이라도 접속하여 학습을 이어나가기를 응원합니다.\n\n" .
                   "- {$teacherName} 드림 -";
        } elseif ($missingAssignment) {
            return "제목: {$subject}\n\n" .
                   "안녕하세요, {$studentName} 학생.\n" .
                   "{$courseName} 과목의 주요 평가 항목인 '{$missingAssignment}' 과제가 아직 제출되지 않았네요.\n" .
                   "마감일이 경과했거나 다가오고 있어 평가에 영향이 갈까 걱정되어 알려드립니다. 혹시 작성이나 제출 과정에 혼란스러운 부분이 있었다면 즉시 질문해 주고, 보완하여 조속히 제출해 주기를 바랍니다. 포기하지 않고 마무리하는 모습 기대하겠습니다.\n\n" .
                   "- {$teacherName} 드림 -";
        } else {
            return "제목: {$subject}\n\n" .
                   "안녕하세요, {$studentName} 학생.\n" .
                   "{$courseName} 강좌의 이수율을 높이기 위해 열심히 참여해 주어 고맙습니다. 학기 중반을 넘어서며 지치기 쉬운 시기인데, 페이스를 잃지 않고 진도를 채워 나간다면 좋은 결실을 맺을 것입니다. 학습 중 어려운 내용은 언제든지 연구실이나 메일로 노크해 주세요.\n\n" .
                   "- {$teacherName} 드림 -";
        }
    }
}
