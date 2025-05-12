<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content};
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $name;

    public $password;

    public $organizationName;
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return void
     */
    public function __construct($name, $password, $organizationName)
    {
        $this->name             = $name;
        $this->password         = $password;
        $this->organizationName = $organizationName;
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Bem-vindo(a) à sua conta no ' . $this->organizationName)
                    ->view('emails.welcome_user')
                    ->with([
                        'name'             => $this->name,
                        'password'         => $this->password,
                        'organizationName' => $this->organizationName,
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
