<?php

namespace App\Modules\Customer\Domain\Services;

use App\Modules\Core\Domain\Services\MailService;
use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\Customer\Domain\Mails\NewCustomerMail;
use App\Modules\Customer\Domain\Models\Customer;

class CustomerService
{
    use ServiceTrait;

    public function model(): string 
    {
        return Customer::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }

    public function register(array $data): Customer
    {
        $customer = $this->create($data);

        $this->notifyCustomerRegistered($customer);

        return $customer;
    }

    private function notifyCustomerRegistered(Customer $customer): void
    {
        $mailService = new MailService(
            to: $customer->email, 
            mailable: NewCustomerMail::class, 
            data: [
                'customer' => $customer->toArray(),
            ]
        );

        $mailService->send();
    }
}
