<?php

namespace App\Modules\Core\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoreMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $data, public array $settings)
    {
        //
    }

    public function build()
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $this->settings['host'],
            'mail.mailers.smtp.port' => $this->settings['port'],
            'mail.mailers.smtp.username' => $this->settings['username'],
            'mail.mailers.smtp.password' => $this->settings['password'],
            'mail.mailers.smtp.encryption' => $this->settings['encryption'] ?? null,
            'mail.from.address' => $this->settings['from_address'],
            'mail.from.name' => $this->settings['from_name'],
        ]);
    }
}
