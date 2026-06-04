<?php

namespace App\Mail;

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
        return $this->subject("[LMS 학습 현황 안내] {$this->studentName} 학생, {$this->courseName} 학사 안내 메일입니다.")
                    ->view('emails.encouragement');
    }
}
