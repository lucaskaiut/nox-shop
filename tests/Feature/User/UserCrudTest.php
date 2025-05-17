<?php

namespace Tests\Feature\User;

use App\Modules\Company\Domain\Services\CompanyService;
use App\Modules\User\Domain\Mails\ResetUserPasswordMail;
use App\Modules\User\Domain\Models\User;
use Tests\TestCaseTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class UserCrudTest extends TestCaseTenant
{
    use RefreshDatabase;

    protected $company;
    protected $authUser;

    public function test_can_create_user()
    {
        $response = $this->postJson('/api/user', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_list_users()
    {
        User::factory()->count(2)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_can_show_user()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/user/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_cannot_show_user_from_another_company()
    {
        $company = app(CompanyService::class)->create([
            'name' => 'Loja Teste',
            'domain' => 'lojateste.com',
            'document' => '99999999999990',
            'contact_email' => 'contato1@lojateste.com',
        ]);

        Event::fake();

        $user = User::factory()->createQuietly(['company_id' => $company->id]);

        $response = $this->getJson("/api/user/{$user->id}");

        $response->assertStatus(404);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->putJson("/api/user/{$user->id}", [
            'name' => 'Atualizado',
            'email' => 'novo@example.com',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Atualizado',
            'email' => 'novo@example.com',
        ]);
    }

    public function test_can_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson("/api/user/login", [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    // public function test_can_create_reset_password()
    // {
    //     Mail::fake();

    //     $response = $this->postJson("/api/user/forgot-password", [
    //         'email' => $this->authUser->email,
    //     ]);

    //     $response->assertStatus(200);

    //     Mail::assertQueued(ResetUserPasswordMail::class, function ($mail) {
    //         return $mail->hasTo($this->authUser->email);
    //     });
    // }

    public function test_can_reset_password()
    {
        $token = Password::createToken($this->authUser);

        $response = $this->postJson("api/user/reset-password", [
            'email' => $this->authUser->email,
            'token' => $token,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response = $this->postJson("api/user/login", [
            'email' => $this->authUser->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/user/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
