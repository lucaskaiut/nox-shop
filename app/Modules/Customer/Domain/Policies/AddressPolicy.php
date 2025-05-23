<?php

namespace App\Modules\Customer\Domain\Policies;

use App\Modules\Customer\Domain\Models\Address;
use App\Modules\User\Domain\Models\User;

class AddressPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($user, $address);
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($user, $address);
    }

    public function show(User $user, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($user, $address);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    private function isAuthorizedToManipulate(User $user, Address $address): bool
    {
        $isAdmin = !$user->customer()->first();

        if ($isAdmin) {
            return $user->company_id === $address->customer()->first()->company_id;
        }

        return $user->customer()->first()->id === $address->customer()->first()->id;
    }
}
