<?php

namespace App\Modules\User\Domain\Services;

use App\Modules\Core\Domain\Exceptions\NotFoundException;
use App\Modules\Core\Domain\Services\MailService;
use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\User\Domain\Mails\ResetUserPasswordMail;
use App\Modules\User\Domain\Models\PasswordResetToken;
use App\Modules\User\Domain\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService
{
    use ServiceTrait;

    public function model(): string 
    {
        return User::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }

    public function login(array $data): User
    {
        $user = $this->findOneBy(['email' => $data['email']]);

        if (!$user) {
            throw new NotFoundException('E-mail ou senha inválido');
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new NotFoundException('E-mail ou senha inválido');
        }

        $user->token = $user->createToken('api')->plainTextToken;

        return $user;
    }

    public function createPasswordReset(string $email): void
    {
        $user = $this->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundException('Usuário não encontrado');
        }

        // $rawToken = (string) Str::uuid();

        // PasswordResetToken::create(['email' => $email, 'token' => Hash::make($rawToken)]);

        $token = Password::createToken($user);

        $this->sendPasswordReset($token, $user);
    }

    private function sendPasswordReset(string $token, User $user): void
    {
        $mailService = new MailService(
            to: $user->email,
            mailable: ResetUserPasswordMail::class,
            data: ['token' => $token],
        );

        $mailService->send();
    }

    public function resetPassword(array $data)
    {
        /** @var ?User $user */
        $user = $this->findOneBy(['email' => $data['email']]);

        if (!$user) {
            throw new NotFoundException('Usuário não encontrado');
        }

        $isValid = Password::getRepository()->exists($user, $data['token']);

        if (!$isValid) {
            throw new NotFoundException('Usuário não encontrato');
        }

        $user->update(['password' => Hash::make($data['password'])]);
    }
}
