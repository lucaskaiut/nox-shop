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

        // Cria e autentica um usuário da company
        $this->authUser = User::create([
            'company_id' => $this->company->id,
            'name' => 'Fulano de Tal',
            'email' => 'fulanodetal@lojateste.com',
            'password' => Hash::make('abc@123'),
        ]);

        $token = $this->authUser->createToken('testing')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token);

        $this->actingAs($this->authUser, 'sanctum');
    }
}
