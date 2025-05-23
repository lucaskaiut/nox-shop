<?php

namespace App\Modules\Customer\Domain\Mails;

use App\Modules\Core\Domain\Mails\CoreMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ResetCustomerPasswordMail extends CoreMailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redefinir a senha da loja',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'customer.reset-password',
        );
    }
}
