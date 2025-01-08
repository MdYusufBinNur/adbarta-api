<?php

namespace App\Jobs\Support;

use App\Mail\Auth\SendResetPasswordMail;
use App\Mail\ContactSupportMail;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactSupportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $contactId;

    public function __construct($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $contact = Contact::query()->findOrFail($this->contactId);
            $name = $contact->first_name . " " . $contact->last_name;
            $email = $contact->email;
            $subject = $contact->subject;
            $text = $contact->message;
            Mail::to('adbartaltd@gmail.com')->send(new ContactSupportMail($name, $email, $subject, $text));
        } catch (\Exception $e) {
            report($e->getMessage());
        }
    }
}
