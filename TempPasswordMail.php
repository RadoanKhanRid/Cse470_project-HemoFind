<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TempPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tempPassword;


    public function __construct($tempPassword)
    {
        $this->tempPassword = $tempPassword;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Temporary Password for Hemofind',
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'emails.temp-password',
            with: ['tempPassword' => $this->tempPassword],
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
