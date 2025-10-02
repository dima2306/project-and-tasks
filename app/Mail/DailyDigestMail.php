<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 15:00.
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $digestData,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'დღიური მიმოხილვა ' . $this->digestData['date'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-digest',
            with: $this->digestData,
        );
    }
}
