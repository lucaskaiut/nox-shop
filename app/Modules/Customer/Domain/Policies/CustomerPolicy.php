<?php

namespace App\Modules\Customer\Domain\Policies;

use App\Modules\Customer\Domain\Models\Customer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;

class CustomerPolicy
{
    public function create(Authenticatable $authenticated): bool
    {
        return $authenticated instanceof User;
    }

    public function update(Authenticatable $authenticated, Customer $customer): bool
    {
        return $this->isAuthorizedToManipulate($authenticated, $customer);
    }

    public function delete(Authenticatable $authenticated, Customer $customer): bool
    {
        return $authenticated instanceof User;
    }

    public function show(Authenticatable $authenticated, Customer $customer): bool
    {
        return $this->isAuthorizedToManipulate($authenticated, $customer);
    }

    public function viewAny(Authenticatable $authenticated): bool
    {
        return $authenticated instanceof User;
    }

    private function isAuthorizedToManipulate(Authenticatable $authenticated, Customer $customer): bool
    {
        $isAdmin = $authenticated instanceof User;

        if ($isAdmin) {
            return $authenticated->company_id === $customer->company_id;
        }

        return $authenticated->id === $customer->id;
    }
}
