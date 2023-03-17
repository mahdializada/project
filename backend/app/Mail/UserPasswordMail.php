<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $userPassword;

    public function __construct(string $userPassword)
    {
        $this->userPassword = $userPassword;
    }


    public function build()
    {
        $data = [
            'password' => $this->userPassword
        ];

        return $this->view("user-password", $data);
    }
}
