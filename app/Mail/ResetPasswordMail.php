<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;

    public function __construct($token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Admin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password-otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
