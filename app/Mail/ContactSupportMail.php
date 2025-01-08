<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSupportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    private $name, $email, $subjects, $text;

    public function __construct($name, $email, $subjects, $text)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subjects = $subjects;
        $this->text = $text;
    }

    public function build()
    {
        return $this->subject($this->subject ?? 'Support')
            ->view('mail.contact_support_mail');
    }
}
