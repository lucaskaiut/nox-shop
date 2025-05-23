<?php

namespace App\Modules\Core\Domain\Services;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function __construct(
        public string $to,
        public string $mailable,
        public ?array $data = [],
    ) {
        if (config('app.env') !== 'production') {
            $this->to = config('mail.always_to');
        }
    }

    public function send(): void
    {
        $class = $this->mailable;

        $this->data['domain'] = app('company')->company()->domain;

        $mailable = new $class($this->data, $this->getMailConfig());

        Mail::to($this->to)->queue($mailable);
    }

    protected function getMailConfig(): array
    {
        $settings = app('company')->company()->getSettingsByType('mail');
        
        return [
            'host' => $settings['host'] ?? config('mail.mailers.smtp.host'),
            'port' => $settings['port'] ?? config('mail.mailers.smtp.port'),
            'username' => $settings['username'] ?? config('mail.mailers.smtp.username'),
            'password' => $settings['password'] ?? config('mail.mailers.smtp.password'),
            'from_address' => $settings['from_address'] ?? config('mail.from.address'),
            'from_name' => $settings['from_name'] ?? config('mail.from.name'),
        ];
    }
}
