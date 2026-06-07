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
        return $this->subject("[LMS Summary Report] {$this->courseName} Academic Encouragement & Email Outreach Status")
                    ->view('moodledash::emails.teacher_summary');
    }
}
