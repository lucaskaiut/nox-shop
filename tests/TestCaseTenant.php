<?php

namespace Tests;

use App\Modules\Company\Domain\Services\CompanyService;
use App\Modules\User\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCaseTenant extends BaseTestCase
{
    use RefreshDatabase;

    protected $company;
    protected $authUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = app(CompanyService::class)->create([
            'name' => 'Loja Teste',
            'domain' => 'lojateste.com',
            'document' => '99999999999999',
            'contact_email' => 'contato@lojateste.com',
        ]);

        app('company')->registerCompany($this->company);

        app(CompanyService::class)->update($this->company, [
            'settings' => [
                [
                    'type' => 'mail',
                    'key' => 'host',
                    'value' => config('mail.mailers.smtp.host'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'port',
                    'value' => config('mail.mailers.smtp.port'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'username',
                    'value' => config('mail.mailers.smtp.username'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'password',
                    'value' => config('mail.mailers.smtp.password'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_name',
                    'value' => config('mail.from.name'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_address',
                    'value' => config('mail.from.address'),
                ],
            ],
        ]);
    }
}
