<?php

namespace App\Modules\Customer\Domain\Services;

use App\Modules\Core\Domain\Exceptions\NotFoundException;
use App\Modules\Core\Domain\Services\MailService;
use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\Customer\Domain\Mails\NewCustomerMail;
use App\Modules\Customer\Domain\Mails\ResetCustomerPasswordMail;
use App\Modules\Customer\Domain\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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

    public function login(array $data)
    {
        $function = $data['login_type'] . 'Login';

        return $this->$function($data);
    }

    private function emailLogin(array $data): Customer
    {
        $customer = $this->findOneBy(['email' => $data['email']]);

        if (!$customer) {
            throw new NotFoundException('E-mail ou senha inválidos');
        }

        if (!Hash::check($data['password'], $customer->password)) {
            throw new NotFoundException('E-mail ou senha inválidos');
        }

        $customer->token = $customer->createToken('api')->plainTextToken;

        return $customer;
    }

    public function createPasswordReset(string $email): void
    {
        $customer = $this->findOneBy(['email' => $email]);

        if (!$customer) {
            throw new NotFoundException('Usuário não encontrado');
        }

        $token = Password::createToken($customer);

        $this->sendPasswordReset($token, $customer);
    }

    private function sendPasswordReset(string $token, Customer $customer): void
    {
        $mailService = new MailService(
            to: $customer->email,
            mailable: ResetCustomerPasswordMail::class,
            data: ['token' => $token],
        );

        $mailService->send();
    }

    public function resetPassword(array $data)
    {
        /** @var ?Customer $customer */
        $customer = $this->findOneBy(['email' => $data['email']]);

        if (!$customer) {
            throw new NotFoundException('Cadastro não encontrado');
        }

        $isValid = Password::getRepository()->exists($customer, $data['token']);

        if (!$isValid) {
            throw new NotFoundException('Cadastro não encontrato');
        }

        $customer->update(['password' => Hash::make($data['password'])]);
    }
}
