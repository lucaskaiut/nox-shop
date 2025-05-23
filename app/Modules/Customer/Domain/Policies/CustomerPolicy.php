<?php

namespace App\Modules\Customer\Domain\Policies;

use App\Modules\Customer\Domain\Models\Customer;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Modules\User\Domain\Models\User;

class CustomerPolicy
{
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->isAuthorizedToManipulate($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->isAdmin($user);
    }

    public function show(User $user, Customer $customer): bool
    {
        return $this->isAuthorizedToManipulate($user, $customer);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAuthorizedToManipulate(User $user, Customer $customer): bool
    {
        $isAdmin = $this->isAdmin($user);

        if ($isAdmin) {
            return $user->company_id === $customer->company_id;
        }

        return $user->customer()->first()->id === $customer->id;
    }

    private function isAdmin(User $user): bool
    {
        return !$user->customer()->first();
    }
}
