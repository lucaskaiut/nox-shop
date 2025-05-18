<?php

namespace Tests\Feature\Admin;

use App\Modules\Company\Domain\Services\CompanyService;
use App\Modules\User\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCaseTenant;

abstract class TestAdminCase extends TestCaseTenant
{
    protected $authUser;

    protected function setUp(): void
    {
        parent::setUp();

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
