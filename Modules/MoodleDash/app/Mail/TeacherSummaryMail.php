<?php

namespace Modules\MoodleDash\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $courseName;
    public $sentCount;
    public $logs;

    /**
     * Create a new message instance.
     */
    public function __construct($courseName, $sentCount, $logs)
    {
        $this->courseName = $courseName;
        $this->sentCount = $sentCount;
        $this->logs = $logs;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("[LMS 요약 보고] {$this->courseName} 학업 독려 및 이메일 발송 현황 보고")
                    ->view('moodledash::emails.teacher_summary');
    }
}
