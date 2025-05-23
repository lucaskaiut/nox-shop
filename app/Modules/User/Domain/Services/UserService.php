<?php

namespace App\Modules\User\Domain\Services;

use App\Modules\Company\Domain\Scopes\CompanyGlobalScope;
use App\Modules\Core\Domain\Exceptions\NotFoundException;
use App\Modules\Core\Domain\Services\MailService;
use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\User\Domain\Mails\ResetUserPasswordMail;
use App\Modules\User\Domain\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
    
    public function create(array $data): User
    {
        $collected = collect($data);
        $relations = $this->hasManyRelations();

        $this->validateDuplicateEntry($collected);

        $user = User::create($collected->except($relations)->all());

        foreach ($relations as $relation) {
            $this->syncMany($user, $collected->get($relation) ?? [], Str::camel($relation));
        }
            
        return $user;
    }

    public function update(User|int $user, array $data): User
    {
        if (is_int($user)) {
            $user = User::query()->lockForUpdate()->findOrFail($user);
        }

        $collected = collect($data);

        $this->validateDuplicateEntry($collected, $user);

        $relations = $this->hasManyRelations();
        
        $user->update($collected->except($relations)->all());

        foreach ($relations as $relation) {
            $this->syncMany($user, $collected->get($relation) ?? [], Str::camel($relation));
        }

        return $user->refresh();
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

    private function validateDuplicateEntry(Collection $collected, ?User $user = null): void
    {
        $exists = null;

        if ($collected->get('type')) {
            $exists = $this->findOneBy(['email' => $collected->get('email'), 'type' => 'customer']);
        }

        if (!$collected->get('type')) {
            $exists = User::withoutGlobalScope(CompanyGlobalScope::class)
                ->where('email', $collected->get('email'))
                ->where('type', 'admin')
                ->first();
        }

        if (!$exists) {
            return;
        }

        if ($user && $exists->id == $user->id) {
            return;
        }
        
        $this->handleDuplicateEntry();
    }

    private function handleDuplicateEntry(): void
    {
        throw new UnprocessableEntityHttpException('Já existe um registro com esse e-mail.');
    }

}
