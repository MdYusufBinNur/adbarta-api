<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $receiver_name;
    public $code;

    /** * Create a new message instance. * *
     * @param $code
     * @param $receiver_name
     */
    public function __construct($code, $receiver_name)
    {
        $this->receiver_name = $receiver_name;
        $this->code = $code;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password')
            ->view('mail.reset_password');
    }
}
