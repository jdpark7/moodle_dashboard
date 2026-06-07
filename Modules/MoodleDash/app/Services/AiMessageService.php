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
        $teacherName = $teacherName ?: 'Course Instructor';

        // 1. Construct prompt
        $reason = "";
        if ($inactiveDays && $inactiveDays >= 7) {
            $reason = "Not having logged in to the LMS system for the last {$inactiveDays} days, which raises concerns about learning gaps";
        } elseif ($missingAssignment) {
            $reason = "Has not yet submitted the assignment '{$missingAssignment}'";
        } else {
            $reason = "Learning progress is a bit slow and needs encouragement";
        }

        $prompt = "The student '{$studentName}' is currently taking the course '{$courseName}'.\n";
        $prompt .= "The student's status is: {$reason}\n\n";
        $prompt .= "As the course instructor ({$teacherName}), write a warm, encouraging message to this student to motivate them to catch up.\n";
        $prompt .= "Guidelines:\n";
        $prompt .= "- Write in a friendly, warm, and authentic tone.\n";
        $prompt .= "- Do not sound overly strict or authoritarian.\n";
        $prompt .= "- Write it in about 3-4 sentences so it can be used directly as an email body, including a Subject line at the beginning.\n";
        $prompt .= "- Do not use markdown formatting tags; output plain text using line breaks for layout.\n";

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
        $subject = "[Learning Encouragement] Hello {$studentName}, this is your instructor for {$courseName}.";
        
        if ($inactiveDays && $inactiveDays >= 7) {
            return "Subject: {$subject}\n\n" .
                   "Hello {$studentName},\n" .
                   "I noticed that you haven't logged into our {$courseName} course on the LMS for the past {$inactiveDays} days.\n" .
                   "I wanted to reach out and check if everything is okay. If you are feeling overwhelmed or having difficulties, please don't hesitate to reach out to me for guidance or a meeting. I encourage you to log in and keep up the great effort, even if it's step by step.\n\n" .
                   "Best regards,\n" .
                   "{$teacherName}";
        } elseif ($missingAssignment) {
            return "Subject: {$subject}\n\n" .
                   "Hello {$studentName},\n" .
                   "I noticed that you haven't submitted the assignment '{$missingAssignment}' for {$courseName} yet.\n" .
                   "I wanted to remind you as this is an important part of your grade, and I don't want you to fall behind. If you have any questions or faced issues while working on it, please let me know. I look forward to seeing your submission soon.\n\n" .
                   "Best regards,\n" .
                   "{$teacherName}";
        } else {
            return "Subject: {$subject}\n\n" .
                   "Hello {$studentName},\n" .
                   "Thank you for your active participation in the {$courseName} course. It is easy to get tired as the semester goes on, but if you keep up your current pace, I am sure you will achieve great results. Please let me know if you run into any questions or difficulties.\n\n" .
                   "Best regards,\n" .
                   "{$teacherName}";
        }
    }
}
