<?php

namespace Modules\MoodleDash\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EncouragementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $courseName;
    public $aiMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $courseName, $aiMessage)
    {
        $this->studentName = $studentName;
        $this->courseName = $courseName;
        
        // Strip out the "제목: ..." prefix from the AI text if the model outputs it
        $this->aiMessage = preg_replace('/^제목:\s*.*?\n+/u', '', $aiMessage);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("[LMS Learning Status] {$this->studentName}, academic guidance for course {$this->courseName}.")
                    ->view('moodledash::emails.encouragement');
    }
}
