<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $userMessage;
    public $subject;

    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email']; // Kullanıcının emaili
        $this->userMessage = $data['message'];
        $this->subject = $data['subject'] ?? 'New Communication Message';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('ibrahimparlak282@gmail.com', 'Miharbi Clothing'), // Sizden gider
            replyTo: [new Address($this->email, $this->name)], // Cevap kullanıcıya gider
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}