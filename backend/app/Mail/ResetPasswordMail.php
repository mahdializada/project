<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $resetPasswordUrl = "https://oredoh.org/auth/reset-password";

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $token = "?token=" . $this->details["token"];
        $email = "&email=" .  $this->details["email"];
        $resetUrl = $this->resetPasswordUrl . $token . $email;
        $baseUrl = "https://oredoh.org/";
        $logoUrl = "https://oredoh.org/dark-mode-logo.svg";

        return $this->view('emails.reset-password', compact('resetUrl', "baseUrl", "logoUrl"));
    }
}
