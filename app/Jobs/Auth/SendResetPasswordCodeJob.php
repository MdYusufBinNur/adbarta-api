<?php

namespace App\Jobs\Auth;

use App\Mail\Auth\SendResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $userID;
    public function __construct($userID)
    {
        $this->userID = $userID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::query()->findOrFail($this->userID);
            $check = DB::table('password_reset_tokens')->where('email','=',$user->email)->first();
            $code = $check->token;
            $name = $user->full_name;
            Mail::to($user->email)->send(new SendResetPasswordMail($code, $name));
        } catch (\Exception $e) {
            report($e->getMessage());
        }
    }
}
