<?php

namespace App\Mail\Verification;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        return $this->subject('Verification')
            ->view('mail.verification_code');
    }
}
