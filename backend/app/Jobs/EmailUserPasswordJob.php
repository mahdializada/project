<?php

namespace App\Jobs;

use App\Mail\UserPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailUserPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;
    private string $userPassword;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $userPassword)
    {
        $this->user = $user;
        $this->userPassword = $userPassword;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to([$this->user->email])->send(new UserPasswordMail($this->userPassword));
    }
}
