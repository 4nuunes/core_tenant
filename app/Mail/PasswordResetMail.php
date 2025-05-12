<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public string $password;

    public string $name;

    /**
     * Create a new message instance.
     *
     * @param string $password Nova senha do usuário.
     */
    public function __construct(string $password, string $name)
    {
        $this->password = $password;
        $this->name     = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("{$this->name} - Sua nova senha")
                    ->view('emails.password-reset')
                    ->with([
                        'password' => $this->password,
                        'name'     => $this->name,
                    ]);
    }
}
