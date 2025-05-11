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
    public $name;
    public $email;
    public $subjects;
    public $text;
    public $mobile;

    public function __construct($name, $email, $subjects, $text, $mobile)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subjects = $subjects;
        $this->text = $text;
        $this->mobile = $mobile;
    }

    public function build()
    {
        return $this->subject($this->subjects ?? 'Support')
            ->view('mail.contact_support_mail');
    }
}
