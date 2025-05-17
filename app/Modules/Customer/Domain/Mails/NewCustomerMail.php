<?php

namespace App\Modules\Customer\Domain\Mails;

use App\Modules\Core\Domain\Mails\CoreMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCustomerMail extends CoreMailable
{
    use Queueable, SerializesModels;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova conta',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'customer.new',
        );
    }
}
