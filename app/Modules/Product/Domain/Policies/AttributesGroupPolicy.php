<?php

namespace App\Modules\Product\Domain\Policies;

use App\Modules\Product\Domain\Models\AttributesGroup;
use App\Modules\User\Domain\Models\User;

class AttributesGroupPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function show(User $user, AttributesGroup $attributesGroup): bool
    {
        return true;
    }

    public function update(User $user, AttributesGroup $attributesGroup): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, AttributesGroup $attributesGroup): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return !$user->customer()->first();
    }
}
