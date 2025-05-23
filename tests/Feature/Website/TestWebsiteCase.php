<?php

namespace Tests\Feature\Website;

use App\Modules\Customer\Domain\Services\CustomerService;
use Tests\TestCaseTenant;

abstract class TestWebsiteCase extends TestCaseTenant
{
    protected $authUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authUser = app(CustomerService::class)->create([
            'company_id' => $this->company->id,
            'first_name' => 'Fulano',
            'last_name' => 'de Tal',
            'type' => 'person',
            'document' => '99999999999',
            'email' => 'fulanodetal@lojateste.com',
            'password' => 'abc@123',
            'birthdate' => '1999-03-31',
        ])->user()->first();

        $token = $this->authUser->createToken('testing')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token);

        $this->actingAs($this->authUser, 'sanctum');
    }
}
