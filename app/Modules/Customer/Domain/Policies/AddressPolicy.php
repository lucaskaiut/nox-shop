<?php

namespace App\Modules\Customer\Domain\Policies;

use App\Modules\Customer\Domain\Models\Address;
use App\Modules\User\Domain\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AddressPolicy
{
    public function create(Authenticatable $authenticated): bool
    {
        return true;
    }

    public function update(Authenticatable $authenticated, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($authenticated, $address);
    }

    public function delete(Authenticatable $authenticated, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($authenticated, $address);
    }

    public function show(Authenticatable $authenticated, Address $address): bool
    {
        return $this->isAuthorizedToManipulate($authenticated, $address);
    }

    public function viewAny(Authenticatable $authenticated): bool
    {
        return true;
    }

    private function isAuthorizedToManipulate(Authenticatable $authenticated, Address $address): bool
    {
        $isAdmin = $authenticated instanceof User;

        if ($isAdmin) {
            return $authenticated->company_id === $address->customer()->first()->company_id;
        }

        return $authenticated->id === $address->customer()->first()->id;
    }
}
